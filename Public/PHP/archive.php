<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */
/** @var string $role */

// Nur Admin und Dozent dürfen das Archiv sehen
if (!in_array($role, ['admin', 'teacher'], true)) {
    header("Location: dashboard.php");
    exit;
}

// Kurzes Kürzel, um Text sicher auszugeben (HTML-Sonderzeichen werden escaped).
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Wandelt den Punkte-Modus-Code in den lesbaren Anzeigenamen um
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

// Prozent richtig (gerundet) oder null, wenn keine getrackten Antworten vorliegen
function pct(int $correct, int $answered): ?int
{
    return $answered > 0 ? (int) round($correct / $answered * 100) : null;
}

// Formatiert die Quote als Prozenttext für die Anzeige.
function pctLabel(?int $p): string
{
    return $p === null ? '—' : $p . '%';
}

// Quoten unter 50 % zur Hervorhebung markieren (ohne Wert bleibt unmarkiert);
// 0 % gilt als kritisch und wird zusätzlich rot dargestellt.
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
// Filter aus GET einlesen
// ---------------------------------------------------------------------------
$fPool = trim($_GET['question_pool'] ?? '');
$fFrom = trim($_GET['date_from'] ?? '');
$fTo   = trim($_GET['date_to'] ?? '');
$fUser = trim($_GET['username'] ?? '');

// Auswahlliste der jemals gespielten Pools (für das Dropdown)
$poolOptions = [];
try {
    $poolOptions = $pdo->query("
        SELECT DISTINCT question_pool
        FROM quiz_lobbies
        WHERE is_started = 1 AND question_pool IS NOT NULL AND question_pool <> ''
        ORDER BY question_pool ASC
    ")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $poolOptions = [];
}

// ---------------------------------------------------------------------------
// Spiele (Ebene 2) inkl. Summen laden, nach den Filtern
// ---------------------------------------------------------------------------
$where  = ['ql.is_started = 1'];
$params = [];

if ($fPool !== '') { $where[] = 'ql.question_pool = ?';  $params[] = $fPool; }
if ($fFrom !== '') { $where[] = 'ql.created_at >= ?';    $params[] = $fFrom . ' 00:00:00'; }
if ($fTo   !== '') { $where[] = 'ql.created_at <= ?';    $params[] = $fTo   . ' 23:59:59'; }
if ($fUser !== '') {
    $where[]  = 'EXISTS (SELECT 1 FROM lobby_players lp WHERE lp.lobby_id = ql.id AND lp.player_name LIKE ?)';
    $params[] = '%' . $fUser . '%';
}

$games      = [];
$loadError  = '';
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
                WHERE pa.lobby_id = ql.id AND pa.is_correct IS NOT NULL) AS answered,
            (SELECT COALESCE(SUM(pa.is_correct), 0) FROM player_answers pa
                WHERE pa.lobby_id = ql.id AND pa.is_correct IS NOT NULL) AS correct
        FROM quiz_lobbies ql
        WHERE " . implode(' AND ', $where) . "
        ORDER BY ql.question_pool ASC, ql.created_at DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $loadError = 'Die Archivdaten konnten nicht geladen werden. Wurde die Tabelle player_answers bereits um is_correct/points_earned erweitert?';
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
        // Teilnehmerdaten sind optional, die Seite bleibt auch ohne sie nutzbar
    }
}

// ---------------------------------------------------------------------------
// Spiele zu Pools (Ebene 1) gruppieren
// ---------------------------------------------------------------------------
$pools = []; // pool => ['games' => [...], 'answered' => int, 'correct' => int]
foreach ($games as $g) {
    $key = $g['question_pool'] !== null && $g['question_pool'] !== '' ? $g['question_pool'] : 'Unbekannter Pool';
    if (!isset($pools[$key])) {
        $pools[$key] = ['games' => [], 'answered' => 0, 'correct' => 0];
    }
    $pools[$key]['games'][]   = $g;
    $pools[$key]['answered'] += (int) $g['answered'];
    $pools[$key]['correct']  += (int) $g['correct'];
}
ksort($pools);

$hasFilter = ($fPool !== '' || $fFrom !== '' || $fTo !== '' || $fUser !== '');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archiv | damago Quizsystem</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>

    <header class="topbar">
        <a href="dashboard.php" class="topbar-brand">
            <img src="../damago-logo.png" alt="damago Logo" class="topbar-logo">
        </a>
        <div class="topbar-account">
            <a href="profile.php" class="account-name account-link" data-tooltip="Klicken, um Profil zu bearbeiten"><?php echo e($username ?? 'Gast'); ?></a>
            <a href="logout.php" class="logout-button">logout</a>
        </div>
    </header>

    <main class="container archive-page">

        <div class="archive-head">
            <span class="eyebrow">Archiv durchsuchen</span>
            <a href="<?php echo $role === 'admin' ? 'admin_area.php' : 'teacher_area.php'; ?>" class="back-button">
                ← Zurück zum <?php echo $role === 'admin' ? 'Adminbereich' : 'Lehrerbereich'; ?>
            </a>
        </div>

        <h1>Archiv</h1>
        <p>Historie aller gespielten Quizrunden – gruppiert nach Fragenpool. Über die Ebenen lassen sich Spiele und Teilnehmer aufklappen.</p>

        <!-- ===================== Filter (einklappbar) ===================== -->
        <?php $activeFilters = ($fPool !== '' ? 1 : 0) + ($fFrom !== '' ? 1 : 0) + ($fTo !== '' ? 1 : 0) + ($fUser !== '' ? 1 : 0); ?>
        <section class="statistics-card">
            <div class="stat-row archive-filter<?php echo $hasFilter ? ' is-open' : ''; ?>">
                <button type="button" class="archive-filter-toggle" data-target="archive-filter-body" aria-expanded="<?php echo $hasFilter ? 'true' : 'false'; ?>">
                    <span class="archive-filter-heading">
                        <span class="stat-title">Archiv filtern</span>
                        <?php if ($hasFilter): ?>
                            <span class="archive-filter-badge"><?php echo $activeFilters; ?> aktiv</span>
                        <?php endif; ?>
                    </span>
                    <span class="archive-filter-chevron" aria-hidden="true">▾</span>
                </button>

                <div class="archive-filter-body" id="archive-filter-body"<?php echo $hasFilter ? '' : ' hidden'; ?>>
                    <form class="archive-filter-form" action="archive.php" method="GET">
                        <div class="archive-filter-grid">
                            <div class="form-group">
                                <label for="question_pool">Fragenpool</label>
                                <select id="question_pool" name="question_pool">
                                    <option value="">Alle Fragenpools</option>
                                    <?php foreach ($poolOptions as $po): ?>
                                        <option value="<?php echo e($po); ?>" <?php echo $fPool === $po ? 'selected' : ''; ?>>
                                            <?php echo e($po); ?>
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

                            <div class="form-group">
                                <label for="username">Teilnehmer</label>
                                <input type="text" id="username" name="username" placeholder="z. B. Gast oder Volker" value="<?php echo e($fUser); ?>">
                            </div>
                        </div>

                        <div class="archive-filter-actions">
                            <button type="submit" class="btn btn-primary">Archiv durchsuchen</button>
                            <a href="archive.php" class="back-button">Filter zurücksetzen</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <br>

        <!-- ===================== Ebene 1: Pools ===================== -->
        <section class="statistics-card">
            <div class="stat-row">
                <div class="stat-title">Archiv-Übersicht<?php echo $hasFilter ? ' (gefiltert)' : ''; ?></div>

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

                                <!-- Ebene 2: Spiele dieses Pools -->
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
                                                    <th>Gewinner</th>
                                                    <th class="cell-action">Aktion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($pool['games'] as $g):
                                                    $gid       = (int) $g['id'];
                                                    $gAnswered = (int) $g['answered'];
                                                    $gCorrect  = (int) $g['correct'];
                                                    $gPct      = pct($gCorrect, $gAnswered);
                                                    $qShown    = (int) $g['q_count'] > 0 ? (int) $g['q_count'] : (int) $g['question_count'];
                                                    $gPlayers  = $playersByLobby[$gid] ?? [];
                                                    $winner    = $gPlayers[0] ?? null; // Liste ist nach Punkten absteigend sortiert
                                                ?>
                                                    <tr class="archive-game-row">
                                                        <td><?php echo formatDateTime($g['created_at']); ?></td>
                                                        <td class="cell-host"><?php echo ($g['host_name'] !== null && $g['host_name'] !== '') ? e($g['host_name']) : '—'; ?></td>
                                                        <td class="cell-num"><?php echo (int) $g['participants']; ?></td>
                                                        <td class="cell-num"><?php echo $qShown; ?></td>
                                                        <td class="cell-num"><?php echo !empty($g['time_limit']) ? (int) $g['time_limit'] . ' s' : '—'; ?></td>
                                                        <td><?php echo e(modeName($g['point_mode'])); ?></td>
                                                        <td class="score-cell<?php echo pctClass($gPct); ?>"><?php echo pctLabel($gPct); ?></td>
                                                        <td>
                                                            <?php if ($winner): ?>
                                                                <span class="ranking-name-cell">
                                                                    <?php if (!empty($winner['avatar'])): ?>
                                                                        <img src="../Uploads/Avatare/<?php echo rawurlencode($winner['avatar']); ?>" alt="" class="ranking-avatar">
                                                                    <?php endif; ?>
                                                                    <?php echo e($winner['player_name']); ?>
                                                                </span>
                                                            <?php else: ?>
                                                                —
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="cell-action">
                                                            <button type="button" class="back-button archive-toggle" data-target="game-<?php echo $gid; ?>">
                                                                Details ansehen
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <!-- Ebene 3: Teilnehmer dieses Spiels -->
                                                    <tr class="archive-detail-row" id="game-<?php echo $gid; ?>" hidden>
                                                        <td colspan="9">
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
                                                                        <?php $arPrevScore = null; $arRank = 0; ?>
                                                                        <?php foreach ($gPlayers as $rankIdx => $p):
                                                                            $pAns = (int) $p['answered'];
                                                                            $pCor = (int) $p['correct'];
                                                                            $pWrong = $pAns - $pCor;
                                                                            $pPct = pct($pCor, $pAns);
                                                                            // Standard-Competition-Ranking (LH 20): gleiche Punkte = gleicher Platz.
                                                                            $pPoints = (int) $p['points'];
                                                                            if ($arPrevScore === null || $pPoints < $arPrevScore) { $arRank = $rankIdx + 1; }
                                                                            $arPrevScore = $pPoints;
                                                                            $pRank = $arRank;
                                                                        ?>
                                                                            <tr<?php echo $pRank === 1 ? ' class="archive-winner-row"' : ''; ?>>
                                                                                <td class="cell-num"><?php echo $pRank; ?>.</td>
                                                                                <td>
                                                                                    <span class="ranking-name-cell">
                                                                                        <?php if (!empty($p['avatar'])): ?>
                                                                                            <img src="../Uploads/Avatare/<?php echo rawurlencode($p['avatar']); ?>" alt="" class="ranking-avatar">
                                                                                        <?php endif; ?>
                                                                                        <?php echo e($p['player_name']); ?>
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
