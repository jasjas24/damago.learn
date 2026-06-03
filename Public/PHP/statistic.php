<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */
/** @var string $role */

// Login erforderlich, Gäste haben keine persönliche Historie
$userId      = $_SESSION['user_id'] ?? null;
$currentRole = $_SESSION['user_role'] ?? $role ?? 'guest';
if (empty($userId) || $currentRole === 'guest') {
    header("Location: ../login.html");
    exit;
}

// In lobby_players steht der player_name = Benutzername des angemeldeten Nutzers
$me = $_SESSION['username'] ?? $username;

// Kurzes Kürzel, um Text sicher auszugeben (HTML-Sonderzeichen werden escaped).
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Gibt zum gespeicherten Punkte-Modus-Code den lesbaren Namen zurück.
function modeName(?string $code): string
{
    switch ($code) {
        case 'all_or_nothing':            return 'Ganz oder gar nicht';
        case 'partial':
        case 'partial_points':            return 'Teilpunkte';
        case 'time_bonus':                return 'Zeitbonus';
        default:                          return $code ? $code : '—';
    }
}

// Rechnet aus richtigen und beantworteten Fragen die Prozentquote aus (null, wenn nichts beantwortet).
function pct(int $correct, int $answered): ?int
{
    return $answered > 0 ? (int) round($correct / $answered * 100) : null;
}

// Formatiert die Quote als Prozenttext für die Anzeige.
function pctLabel(?int $p): string
{
    return $p === null ? '—' : $p . '%';
}

// Quoten unter 50 % markieren; 0 % zusätzlich als kritisch
function pctClass(?int $p): string
{
    if ($p === null || $p >= 50) {
        return '';
    }
    return $p === 0 ? ' pct-low pct-zero' : ' pct-low';
}

// Bringt einen Zeitstempel in ein lesbares deutsches Datumsformat.
function formatDateTime($dt): string
{
    if (empty($dt)) return '—';
    $ts = strtotime($dt);
    return $ts === false ? e($dt) : date('d.m.Y - H:i', $ts) . ' Uhr';
}

// ---------------------------------------------------------------------------
// Filter aus GET einlesen (Fachbereich + Zeitraum)
// ---------------------------------------------------------------------------
$fDept = trim($_GET['department'] ?? '');
$fFrom = trim($_GET['date_from'] ?? '');
$fTo   = trim($_GET['date_to'] ?? '');

// Fachbereiche für das Dropdown laden (Eltern, dann Kinder eingerückt)
$departments = [];
try {
    $departments = $pdo->query("
        SELECT id, parent_id, display_name
        FROM departments
        WHERE is_active = 1
        ORDER BY COALESCE(parent_id, id), (parent_id IS NOT NULL), display_name
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $departments = [];
}

// Gewählter Fachbereich + dessen Unter-Fachbereiche
$deptIds = [];
if ($fDept !== '' && ctype_digit($fDept)) {
    $sel       = (int) $fDept;
    $deptIds[] = $sel;
    foreach ($departments as $d) {
        if ((int) $d['parent_id'] === $sel) {
            $deptIds[] = (int) $d['id'];
        }
    }
}

// ---------------------------------------------------------------------------
// Spiele (Ebene 2) laden, nur Runden, an denen der Nutzer teilgenommen hat
// ---------------------------------------------------------------------------
$where       = ['ql.is_started = 1'];
$whereParams = [];

// Eigene Teilnahme eindeutig über die user_id erkennen (nicht über den Anzeigenamen,
// sonst würde ein Gast mit gleichem Namen in fremde Historie einfließen, LH 21.2).
$where[]       = 'EXISTS (SELECT 1 FROM lobby_players lp WHERE lp.lobby_id = ql.id AND lp.user_id = ?)';
$whereParams[] = $userId;

if ($fFrom !== '') { $where[] = 'ql.created_at >= ?'; $whereParams[] = $fFrom . ' 00:00:00'; }
if ($fTo   !== '') { $where[] = 'ql.created_at <= ?'; $whereParams[] = $fTo   . ' 23:59:59'; }

if (!empty($deptIds)) {
    $ph        = implode(',', array_fill(0, count($deptIds), '?'));
    $where[]   = "ql.question_pool IN (
        SELECT qp.name FROM question_pools qp
        INNER JOIN question_pool_departments qpd ON qpd.question_pool_id = qp.id
        WHERE qpd.department_id IN ($ph)
    )";
    $whereParams = array_merge($whereParams, $deptIds);
}

// Reihenfolge der Platzhalter: erst die beiden SELECT-Subqueries (?=me), dann WHERE
$params = array_merge([$me, $me], $whereParams);

$games     = [];
$loadError = '';
try {
    $sql = "
        SELECT
            ql.id,
            ql.question_pool,
            ql.point_mode,
            ql.question_count,
            ql.time_limit,
            ql.host_name,
            ql.created_at,
            (SELECT COUNT(*) FROM lobby_players  lp WHERE lp.lobby_id = ql.id) AS participants,
            (SELECT COUNT(*) FROM lobby_questions lq WHERE lq.lobby_id = ql.id) AS q_count,
            (SELECT COUNT(*) FROM player_answers pa
                WHERE pa.lobby_id = ql.id AND pa.player_name = ? AND pa.is_correct IS NOT NULL) AS my_answered,
            (SELECT COALESCE(SUM(pa.is_correct), 0) FROM player_answers pa
                WHERE pa.lobby_id = ql.id AND pa.player_name = ? AND pa.is_correct IS NOT NULL) AS my_correct
        FROM quiz_lobbies ql
        WHERE " . implode(' AND ', $where) . "
        ORDER BY ql.question_pool ASC, ql.created_at DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $loadError = 'Die Statistikdaten konnten nicht geladen werden.';
}

// ---------------------------------------------------------------------------
// Teilnehmer (Ebene 3) für die geladenen Spiele
// ---------------------------------------------------------------------------
$playersByLobby = [];
$lobbyIds = array_map(fn($g) => (int) $g['id'], $games);
if (!empty($lobbyIds)) {
    $in = implode(',', array_fill(0, count($lobbyIds), '?'));
    try {
        $sqlP = "
            SELECT
                lp.lobby_id,
                lp.player_name,
                lp.avatar,
                lp.points,
                lp.user_id,
                (SELECT COUNT(*) FROM player_answers pa
                    WHERE pa.lobby_id = lp.lobby_id AND pa.player_name = lp.player_name
                      AND pa.is_correct IS NOT NULL) AS answered,
                (SELECT COALESCE(SUM(pa.is_correct), 0) FROM player_answers pa
                    WHERE pa.lobby_id = lp.lobby_id AND pa.player_name = lp.player_name
                      AND pa.is_correct IS NOT NULL) AS correct
            FROM lobby_players lp
            WHERE lp.lobby_id IN ($in)
            ORDER BY lp.points DESC, lp.player_name ASC
        ";
        $stmtP = $pdo->prepare($sqlP);
        $stmtP->execute($lobbyIds);
        foreach ($stmtP->fetchAll(PDO::FETCH_ASSOC) as $p) {
            $playersByLobby[(int) $p['lobby_id']][] = $p;
        }
    } catch (PDOException $e) {
        // Teilnehmerdaten optional
    }
}

// ---------------------------------------------------------------------------
// Spiele zu Pools (Ebene 1) gruppieren, die Summen beziehen sich auf den Nutzer
// ---------------------------------------------------------------------------
$pools = [];
foreach ($games as $g) {
    $key = $g['question_pool'] !== null && $g['question_pool'] !== '' ? $g['question_pool'] : 'Unbekannter Pool';
    if (!isset($pools[$key])) {
        $pools[$key] = ['games' => [], 'answered' => 0, 'correct' => 0];
    }
    $pools[$key]['games'][]   = $g;
    $pools[$key]['answered'] += (int) $g['my_answered'];
    $pools[$key]['correct']  += (int) $g['my_correct'];
}
ksort($pools);

$activeFilters = ($fDept !== '' ? 1 : 0) + ($fFrom !== '' ? 1 : 0) + ($fTo !== '' ? 1 : 0);
$hasFilter     = $activeFilters > 0;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lernfortschritt | damago Quizsystem</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>

    <?php include_once 'topbar.php'; ?>

    <main class="container archive-page">

        <div class="archive-head">
            <span class="eyebrow">Historie</span>
            <a href="dashboard.php" class="back-button">← Zurück zum Dashboard</a>
        </div>

        <h1>Lernfortschritt</h1>
        <p>Deine persönliche Historie – gruppiert nach Fragenpool. Über die Ebenen lassen sich deine Spiele und die Teilnehmer aufklappen.</p>

        <!-- ===================== Filter (einklappbar) ===================== -->
        <section class="statistics-card">
            <div class="stat-row archive-filter<?php echo $hasFilter ? ' is-open' : ''; ?>">
                <button type="button" class="archive-filter-toggle" data-target="stat-filter-body" aria-expanded="<?php echo $hasFilter ? 'true' : 'false'; ?>">
                    <span class="archive-filter-heading">
                        <span class="stat-title">Statistik filtern</span>
                        <?php if ($hasFilter): ?>
                            <span class="archive-filter-badge"><?php echo $activeFilters; ?> aktiv</span>
                        <?php endif; ?>
                    </span>
                    <span class="archive-filter-chevron" aria-hidden="true">▾</span>
                </button>

                <div class="archive-filter-body" id="stat-filter-body"<?php echo $hasFilter ? '' : ' hidden'; ?>>
                    <form class="archive-filter-form" action="statistic.php" method="GET">
                        <div class="archive-filter-grid">
                            <div class="form-group">
                                <label for="department">Fachbereich</label>
                                <select id="department" name="department">
                                    <option value="">Alle Fachbereiche</option>
                                    <?php foreach ($departments as $d): ?>
                                        <?php $isChild = $d['parent_id'] !== null; ?>
                                        <option value="<?php echo (int) $d['id']; ?>" <?php echo $fDept === (string) $d['id'] ? 'selected' : ''; ?>>
                                            <?php echo ($isChild ? '– ' : '') . e($d['display_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="date_from">Datum von</label>
                                <input type="date" id="date_from" name="date_from" value="<?php echo e($fFrom); ?>">
                            </div>

                            <div class="form-group">
                                <label for="date_to">Datum bis</label>
                                <input type="date" id="date_to" name="date_to" value="<?php echo e($fTo); ?>">
                            </div>
                        </div>

                        <div class="archive-filter-actions">
                            <button type="submit" class="btn btn-primary">Statistik anzeigen</button>
                            <a href="statistic.php" class="back-button">Filter zurücksetzen</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <br>

        <!-- ===================== Ebene 1: Pools ===================== -->
        <section class="statistics-card">
            <div class="stat-row">
                <div class="stat-title">Dein Lernfortschritt<?php echo $hasFilter ? ' (gefiltert)' : ''; ?></div>

                <?php if ($loadError !== ''): ?>
                    <p class="archive-empty"><?php echo e($loadError); ?></p>
                <?php elseif (empty($pools)): ?>
                    <p class="archive-empty">Keine gespielten Quizrunden gefunden<?php echo $hasFilter ? ' (für die aktuellen Filter)' : ''; ?>.</p>
                <?php else: ?>
                    <table class="ranking-table archive-table">
                        <thead>
                            <tr>
                                <th>Fragenpool</th>
                                <th class="cell-num">Spiele</th>
                                <th class="score-cell">Ø richtig</th>
                                <th class="cell-action">Aktion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $pi = 0; foreach ($pools as $poolName => $pool): $pi++;
                                $poolPct = pct($pool['correct'], $pool['answered']);
                            ?>
                                <tr class="archive-pool-row">
                                    <td><?php echo e($poolName); ?></td>
                                    <td class="cell-num"><?php echo count($pool['games']); ?></td>
                                    <td class="score-cell<?php echo pctClass($poolPct); ?>"><?php echo pctLabel($poolPct); ?></td>
                                    <td class="cell-action">
                                        <button type="button" class="back-button archive-toggle" data-target="pool-<?php echo $pi; ?>">
                                            Details ansehen
                                        </button>
                                    </td>
                                </tr>

                                <!-- Ebene 2: Deine Spiele in diesem Pool -->
                                <tr class="archive-detail-row" id="pool-<?php echo $pi; ?>" hidden>
                                    <td colspan="4">
                                        <table class="ranking-table archive-subtable">
                                            <thead>
                                                <tr>
                                                    <th>Datum / Uhrzeit</th>
                                                    <th class="cell-host">Host</th>
                                                    <th class="cell-num">Teilnehmer</th>
                                                    <th class="cell-num">Fragen</th>
                                                    <th class="cell-num">Zeitlimit</th>
                                                    <th>Modus</th>
                                                    <th class="score-cell">Ø richtig</th>
                                                    <th class="cell-action">Aktion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($pool['games'] as $g):
                                                    $gid       = (int) $g['id'];
                                                    $myAns     = (int) $g['my_answered'];
                                                    $myCor     = (int) $g['my_correct'];
                                                    $myPct     = pct($myCor, $myAns);
                                                    $qShown    = (int) $g['q_count'] > 0 ? (int) $g['q_count'] : (int) $g['question_count'];
                                                    $gPlayers  = $playersByLobby[$gid] ?? [];
                                                ?>
                                                    <tr class="archive-game-row">
                                                        <td><?php echo formatDateTime($g['created_at']); ?></td>
                                                        <td class="cell-host"><?php echo ($g['host_name'] !== null && $g['host_name'] !== '') ? e($g['host_name']) : '—'; ?></td>
                                                        <td class="cell-num"><?php echo (int) $g['participants']; ?></td>
                                                        <td class="cell-num"><?php echo $qShown; ?></td>
                                                        <td class="cell-num"><?php echo !empty($g['time_limit']) ? (int) $g['time_limit'] . ' s' : '—'; ?></td>
                                                        <td><?php echo e(modeName($g['point_mode'])); ?></td>
                                                        <td class="score-cell<?php echo pctClass($myPct); ?>"><?php echo pctLabel($myPct); ?></td>
                                                        <td class="cell-action">
                                                            <button type="button" class="back-button archive-toggle" data-target="game-<?php echo $gid; ?>">
                                                                Details ansehen
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <!-- Ebene 3: Teilnehmer dieses Spiels (eigene Zeile hervorgehoben) -->
                                                    <tr class="archive-detail-row" id="game-<?php echo $gid; ?>" hidden>
                                                        <td colspan="8">
                                                            <?php if (empty($gPlayers)): ?>
                                                                <p class="archive-empty">Keine Teilnehmerdaten vorhanden.</p>
                                                            <?php else: ?>
                                                                <table class="ranking-table archive-subtable">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="cell-num">Pl.</th>
                                                                            <th>Teilnehmer</th>
                                                                            <th class="score-cell">Punkte</th>
                                                                            <th class="cell-num">Richtig</th>
                                                                            <th class="cell-num">Falsch</th>
                                                                            <th class="score-cell">% richtig</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php $stPrevScore = null; $stRank = 0; ?>
                                                                        <?php foreach ($gPlayers as $rankIdx => $p):
                                                                            $pAns   = (int) $p['answered'];
                                                                            $pCor   = (int) $p['correct'];
                                                                            $pWrong = $pAns - $pCor;
                                                                            $pPct   = pct($pCor, $pAns);
                                                                            // Standard-Competition-Ranking (LH 20): gleiche Punkte = gleicher Platz.
                                                                            $pPoints = (int) $p['points'];
                                                                            if ($stPrevScore === null || $pPoints < $stPrevScore) { $stRank = $rankIdx + 1; }
                                                                            $stPrevScore = $pPoints;
                                                                            $pRank  = $stRank;
                                                                            $isMe   = ((int)($p['user_id'] ?? 0) === (int)$userId);
                                                                        ?>
                                                                            <tr<?php echo $isMe ? ' class="current-player"' : ''; ?>>
                                                                                <td class="cell-num"><?php echo $pRank; ?>.</td>
                                                                                <td>
                                                                                    <span class="ranking-name-cell">
                                                                                        <?php if (!empty($p['avatar'])): ?>
                                                                                            <img src="../Uploads/Avatare/<?php echo rawurlencode($p['avatar']); ?>" alt="" class="ranking-avatar">
                                                                                        <?php endif; ?>
                                                                                        <?php echo e($p['player_name']); ?>
                                                                                        <?php if ($isMe): ?><span class="you-badge">(Du)</span><?php endif; ?>
                                                                                    </span>
                                                                                </td>
                                                                                <td class="score-cell"><?php echo (int) $p['points']; ?></td>
                                                                                <td class="cell-num"><?php echo $pAns > 0 ? $pCor : '—'; ?></td>
                                                                                <td class="cell-num"><?php echo $pAns > 0 ? $pWrong : '—'; ?></td>
                                                                                <td class="score-cell<?php echo pctClass($pPct); ?>"><?php echo pctLabel($pPct); ?></td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="mq-pagination" id="arPagination">
                        <div class="mq-pagination-info">
                            <span id="arRangeInfo"></span>
                        </div>
                        <div class="mq-pagination-controls">
                            <label class="mq-perpage">
                                Pro Seite
                                <select id="arPerPage">
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </label>
                            <div class="mq-pager">
                                <button type="button" class="btn-icon mq-pager-btn" id="arPrev" aria-label="Vorherige Seite">‹</button>
                                <span class="mq-pager-pages" id="arPages"></span>
                                <button type="button" class="btn-icon mq-pager-btn" id="arNext" aria-label="Nächste Seite">›</button>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>
            </div>
        </section>

    </main>

    <script>
        // Auf-/Zuklappen des Filters
        document.querySelectorAll('.archive-filter-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = document.getElementById(this.getAttribute('data-target'));
                var box = this.closest('.archive-filter');
                if (!target) return;
                if (target.hasAttribute('hidden')) {
                    target.removeAttribute('hidden');
                    this.setAttribute('aria-expanded', 'true');
                    if (box) box.classList.add('is-open');
                } else {
                    target.setAttribute('hidden', '');
                    this.setAttribute('aria-expanded', 'false');
                    if (box) box.classList.remove('is-open');
                }
            });
        });

        // Auf-/Zuklappen der Detail-Ebenen
        document.querySelectorAll('.archive-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = document.getElementById(this.getAttribute('data-target'));
                if (!target) return;
                var open = target.hasAttribute('hidden') ? false : true;
                if (open) {
                    target.setAttribute('hidden', '');
                    this.textContent = 'Details ansehen';
                } else {
                    target.removeAttribute('hidden');
                    this.textContent = 'Details ausblenden';
                }
            });
        });

        // Pagination der Pool-Liste (10 / 25 / 50 pro Seite)
        (function () {
            const table = document.querySelector('.archive-table');
            const bar   = document.getElementById('arPagination');
            if (!table || !bar) { if (bar) bar.style.display = 'none'; return; }

            const tbody    = table.querySelector('tbody');
            const poolRows = Array.from(tbody.children).filter(function (el) {
                return el.classList && el.classList.contains('archive-pool-row');
            });
            if (!poolRows.length) { bar.style.display = 'none'; return; }

            const perPageSel = document.getElementById('arPerPage');
            const rangeInfo  = document.getElementById('arRangeInfo');
            const pagesBox   = document.getElementById('arPages');
            const prevBtn    = document.getElementById('arPrev');
            const nextBtn    = document.getElementById('arNext');

            let perPage = parseInt(perPageSel.value, 10) || 10;
            let current = 1;

            // Findet die zugehörige aufklappbare Detailzeile zu einer Pool-Zeile, falls vorhanden.
            function detailOf(row) {
                const n = row.nextElementSibling;
                return (n && n.classList.contains('archive-detail-row')) ? n : null;
            }
            // Berechnet, wie viele Seiten die Pool-Liste bei der gewählten Seitengröße hat.
            function totalPages() { return Math.max(1, Math.ceil(poolRows.length / perPage)); }
            // Baut die Liste der anzuzeigenden Seitenzahlen, bei vielen Seiten mit Auslassungspunkten.
            function pageList(total, cur) {
                const out = [];
                for (let i = 1; i <= total; i++) {
                    if (i === 1 || i === total || (i >= cur - 1 && i <= cur + 1)) {
                        out.push(i);
                    } else if (out[out.length - 1] !== '…') {
                        out.push('…');
                    }
                }
                return out;
            }
            // Zeigt nur die Pools der aktuellen Seite an und baut die Seiten-Buttons neu.
            function render() {
                const total = totalPages();
                if (current > total) current = total;
                const start = (current - 1) * perPage;
                const end   = start + perPage;
                poolRows.forEach(function (row, i) {
                    const show = (i >= start && i < end);
                    row.style.display = show ? '' : 'none';
                    const d = detailOf(row);
                    if (d) d.style.display = show ? '' : 'none';
                });
                const from = poolRows.length ? start + 1 : 0;
                const to   = Math.min(end, poolRows.length);
                rangeInfo.textContent = from + '–' + to + ' von ' + poolRows.length;

                pagesBox.innerHTML = '';
                pageList(total, current).forEach(function (p) {
                    if (p === '…') {
                        const span = document.createElement('span');
                        span.className = 'mq-pager-ellipsis';
                        span.textContent = '…';
                        pagesBox.appendChild(span);
                    } else {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'btn-icon mq-pager-btn mq-page-num' + (p === current ? ' is-active' : '');
                        btn.textContent = p;
                        btn.addEventListener('click', function () { current = p; render(); });
                        pagesBox.appendChild(btn);
                    }
                });

                prevBtn.disabled = current <= 1;
                nextBtn.disabled = current >= total;
            }

            prevBtn.addEventListener('click', function () { if (current > 1) { current--; render(); } });
            nextBtn.addEventListener('click', function () { if (current < totalPages()) { current++; render(); } });
            perPageSel.addEventListener('change', function () {
                perPage = parseInt(perPageSel.value, 10) || 10;
                current = 1;
                render();
            });

            render();
        })();
    </script>

    <?php include_once 'footbar.php'; ?>

</body>
</html>
