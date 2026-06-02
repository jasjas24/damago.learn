<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */
/** @var string $role */
/** @var \PDO $pdo */

if (!in_array($role, ['admin', 'teacher'])) {
    header("Location: dashboard.php");
    exit;
}

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Eingeloggten User ermitteln – primär über die beim Login gesetzte Session-ID
$currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

// Fallback: über den Benutzernamen, falls die Session-ID einmal fehlt
if ($currentUserId === null && $username !== 'Gast') {
    $stmtUser = $pdo->prepare("SELECT id FROM users WHERE username = :u LIMIT 1");
    $stmtUser->execute([':u' => $username]);
    $u = $stmtUser->fetch();
    $currentUserId = $u ? (int)$u['id'] : null;
}

// Aktive Fachbereiche laden (für Filter + Auswahl in den Modals)
$departments  = $pdo->query(
    "SELECT id, parent_id, display_name FROM departments WHERE is_active = 1 ORDER BY display_name ASC"
)->fetchAll(PDO::FETCH_ASSOC);
$validDeptIds = array_map(fn($d) => (int)$d['id'], $departments);

// Fachbereiche als Baum aufbauen (parent_id). Verwaiste Knoten landen oben.
$activeDeptIds = array_flip($validDeptIds);
$deptChildren  = [];
$deptRoots     = [];
foreach ($departments as $d) {
    $pid = $d['parent_id'] !== null ? (int)$d['parent_id'] : 0;
    if ($pid !== 0 && isset($activeDeptIds[$pid])) {
        $deptChildren[$pid][] = $d;
    } else {
        $deptRoots[] = $d;
    }
}

// Baum als verschachtelte Checkbox-Liste rendern (für die Modals)
function renderDepartmentTree(array $nodes, array $childrenByParent): string {
    $html = '';
    foreach ($nodes as $node) {
        $id = (int)$node['id'];
        $html .= '<div class="mq-dept-node">';
        $html .= '<label class="modal-checkbox-label"><input type="checkbox" name="departments[]" value="'
               . $id . '"> ' . e($node['display_name']) . '</label>';
        if (!empty($childrenByParent[$id])) {
            $html .= '<div class="mq-dept-children">'
                   . renderDepartmentTree($childrenByParent[$id], $childrenByParent)
                   . '</div>';
        }
        $html .= '</div>';
    }
    return $html;
}

// Einen Fachbereich + alle seine Unterbereiche (rekursiv) als ID-Liste
function collectDeptWithDescendants(int $id, array $childrenByParent): array {
    $ids = [$id];
    foreach ($childrenByParent[$id] ?? [] as $child) {
        $ids = array_merge($ids, collectDeptWithDescendants((int)$child['id'], $childrenByParent));
    }
    return $ids;
}

// Baum flach mit Tiefe (für den Filter-Dropdown)
function flattenDepartmentTree(array $nodes, array $childrenByParent, int $depth = 0): array {
    $out = [];
    foreach ($nodes as $node) {
        $out[] = ['dept' => $node, 'depth' => $depth];
        $id = (int)$node['id'];
        if (!empty($childrenByParent[$id])) {
            $out = array_merge($out, flattenDepartmentTree($childrenByParent[$id], $childrenByParent, $depth + 1));
        }
    }
    return $out;
}
$deptOrdered = flattenDepartmentTree($deptRoots, $deptChildren);

$message     = '';
$messageType = '';

// POST-Aktionen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Gewählte Fachbereiche aus dem Formular (nur gültige IDs)
    $deptSel = array_values(array_intersect(
        array_map('intval', (array)($_POST['departments'] ?? [])),
        $validDeptIds
    ));

    // Status umschalten
    if ($action === 'toggle_status') {
        $poolId = (int)($_POST['pool_id'] ?? 0);
        if ($poolId > 0) {
            $stmt = $pdo->prepare(
                "UPDATE question_pools SET is_active = 1 - is_active, updated_by = ?, updated_at = NOW() WHERE id = ?"
            );
            $stmt->execute([$currentUserId, $poolId]);
            $message     = 'Status erfolgreich geändert.';
            $messageType = 'success';
        }
    }

    // Pool erstellen
    if ($action === 'create_pool') {
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '') {
            $message     = 'Bitte einen Namen für den Fragenpool angeben.';
            $messageType = 'error';
        } elseif (empty($deptSel)) {
            $message     = 'Bitte mindestens einen Fachbereich auswählen.';
            $messageType = 'error';
        } else {
            $pdo->prepare(
                "INSERT INTO question_pools (name, description, created_by, is_active) VALUES (?, ?, ?, 1)"
            )->execute([$name, $description, $currentUserId]);
            $newId = (int)$pdo->lastInsertId();

            $insDept = $pdo->prepare(
                "INSERT INTO question_pool_departments (question_pool_id, department_id, created_by) VALUES (?, ?, ?)"
            );
            foreach ($deptSel as $did) {
                $insDept->execute([$newId, $did, $currentUserId]);
            }

            $message     = 'Fragenpool erfolgreich erstellt.';
            $messageType = 'success';
        }
    }

    // Pool aktualisieren
    if ($action === 'update_pool') {
        $poolId      = (int)($_POST['pool_id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $isActive    = ($_POST['pool_status'] ?? 'inactive') === 'active' ? 1 : 0;

        if ($poolId <= 0 || $name === '') {
            $message     = 'Bitte einen Namen für den Fragenpool angeben.';
            $messageType = 'error';
        } elseif (empty($deptSel)) {
            $message     = 'Bitte mindestens einen Fachbereich auswählen.';
            $messageType = 'error';
        } else {
            $pdo->prepare(
                "UPDATE question_pools SET name = ?, description = ?, is_active = ?, updated_by = ?, updated_at = NOW()
                 WHERE id = ?"
            )->execute([$name, $description, $isActive, $currentUserId, $poolId]);

            // Fachbereich-Zuordnung neu setzen
            $pdo->prepare("DELETE FROM question_pool_departments WHERE question_pool_id = ?")->execute([$poolId]);
            $insDept = $pdo->prepare(
                "INSERT INTO question_pool_departments (question_pool_id, department_id, created_by) VALUES (?, ?, ?)"
            );
            foreach ($deptSel as $did) {
                $insDept->execute([$poolId, $did, $currentUserId]);
            }

            $message     = 'Fragenpool erfolgreich aktualisiert.';
            $messageType = 'success';
        }
    }

    // Pool löschen (nur wenn keine Fragen zugeordnet sind)
    if ($action === 'delete_pool') {
        $poolId = (int)($_POST['pool_id'] ?? 0);
        if ($poolId > 0) {
            $cnt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE question_pool_id = ?");
            $cnt->execute([$poolId]);
            $questionCount = (int)$cnt->fetchColumn();

            if ($questionCount > 0) {
                $message     = 'Pool kann nicht gelöscht werden: Ihm sind noch ' . $questionCount
                    . ' Frage' . ($questionCount !== 1 ? 'n' : '') . ' zugeordnet.';
                $messageType = 'error';
            } else {
                try {
                    // Fachbereich-Zuordnungen entfernen (CASCADE greift zwar, wir räumen sauber auf)
                    $pdo->prepare("DELETE FROM question_pool_departments WHERE question_pool_id = ?")->execute([$poolId]);
                    $pdo->prepare("DELETE FROM question_pools WHERE id = ?")->execute([$poolId]);
                    $message     = 'Fragenpool erfolgreich gelöscht.';
                    $messageType = 'success';
                } catch (PDOException $ex) {
                    $message     = 'Pool kann nicht gelöscht werden, da er noch verwendet wird.';
                    $messageType = 'error';
                }
            }
        }
    }
}

// Bei Fehlern das Erstellen-Modal nach dem Reload wieder öffnen
$openModalOnError = '';
if ($messageType === 'error' && ($_POST['action'] ?? '') === 'create_pool') {
    $openModalOnError = 'create';
}

// Fachbereich-Filter aus GET
$selectedDept = isset($_GET['dept']) ? (int)$_GET['dept'] : 0;
$filterQuery  = $selectedDept > 0 ? '?dept=' . $selectedDept : '';

// Fragenpools laden (mit Ersteller-Name und Fragenanzahl), optional nach Fachbereich gefiltert
if ($selectedDept > 0) {
    // Ausgewählter Fachbereich inkl. aller Unterbereiche
    $filterDeptIds = collectDeptWithDescendants($selectedDept, $deptChildren);
    $phDept        = implode(',', array_fill(0, count($filterDeptIds), '?'));
    $stmtP = $pdo->prepare("
        SELECT qp.id, qp.name, qp.description, qp.is_active, qp.created_at, qp.created_by,
               u.username AS creator_name,
               (SELECT COUNT(*) FROM questions q WHERE q.question_pool_id = qp.id) AS question_count
        FROM question_pools qp
        LEFT JOIN users u ON u.id = qp.created_by
        WHERE qp.id IN (
            SELECT qpd.question_pool_id FROM question_pool_departments qpd
            WHERE qpd.department_id IN ($phDept)
        )
        ORDER BY qp.name ASC
    ");
    $stmtP->execute($filterDeptIds);
} else {
    $stmtP = $pdo->query("
        SELECT qp.id, qp.name, qp.description, qp.is_active, qp.created_at, qp.created_by,
               u.username AS creator_name,
               (SELECT COUNT(*) FROM questions q WHERE q.question_pool_id = qp.id) AS question_count
        FROM question_pools qp
        LEFT JOIN users u ON u.id = qp.created_by
        ORDER BY qp.name ASC
    ");
}
$pools = $stmtP->fetchAll(PDO::FETCH_ASSOC);

// Fachbereiche je Pool laden (für Anzeige + Vorbefüllung im Bearbeiten-Modal)
$poolDepts = [];
if (!empty($pools)) {
    $ids = array_column($pools, 'id');
    $ph  = implode(',', array_fill(0, count($ids), '?'));
    $stmtD = $pdo->prepare("
        SELECT qpd.question_pool_id, d.id AS dept_id, d.display_name
        FROM question_pool_departments qpd
        INNER JOIN departments d ON d.id = qpd.department_id
        WHERE qpd.question_pool_id IN ($ph)
        ORDER BY d.display_name ASC
    ");
    $stmtD->execute($ids);
    foreach ($stmtD->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $poolDepts[(int)$r['question_pool_id']][] = ['id' => (int)$r['dept_id'], 'name' => $r['display_name']];
    }
}

// Kennzahlen über alle Pools (unabhängig vom Filter)
$totalPools  = (int)$pdo->query("SELECT COUNT(*) FROM question_pools")->fetchColumn();
$activePools = (int)$pdo->query("SELECT COUNT(*) FROM question_pools WHERE is_active = 1")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Fragenpools verwalten</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/style.css">
</head>

<body class="manage-questions-page">

<div class="page-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>

<?php include_once 'topbar.php'; ?>

<main class="play-layout">

    <section class="quiz-main mq-panel">

        <div class="mq-topbar">
            <div class="mq-topbar-info">
                <span class="eyebrow">Fragenpools-Verwaltung</span>
                <h2>Vorhandene Fragenpools</h2>
            </div>
            <div class="mq-topbar-actions">
                <a href="<?php echo $role === 'admin' ? 'admin_area.php' : 'teacher_area.php'; ?>" class="back-button">
                    ← Zurück zum <?php echo $role === 'admin' ? 'Adminbereich' : 'Lehrerbereich'; ?>
                </a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="import-section import-message <?php echo $messageType === 'success' ? 'import-message-success' : 'import-message-error'; ?>">
                <span class="import-message-text">
                    <?php echo e($message); ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="mq-toolbar">
            <form method="GET" action="manage_question_pools.php" class="form-group pool-filter-form mq-filter">
                <label for="deptFilter">Fachbereich</label>
                <select id="deptFilter" name="dept" onchange="this.form.submit()">
                    <option value="">Alle Fachbereiche</option>
                    <?php foreach ($deptOrdered as $row): $d = $row['dept']; ?>
                        <option value="<?php echo $d['id']; ?>"
                            <?php echo $selectedDept === (int)$d['id'] ? 'selected' : ''; ?>>
                            <?php echo str_repeat('— ', $row['depth']) . e($d['display_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <div class="lobby-meta mq-meta">
                <div>
                    <span>Gesamt</span>
                    <strong><?php echo $totalPools; ?> Pools</strong>
                </div>
                <div>
                    <span>Aktiv</span>
                    <strong><?php echo $activePools; ?> aktiv</strong>
                </div>
            </div>
        </div>

        <div class="question-list-header">
            <span class="col-frage">Fragenpool</span>
            <span class="col-qaktion">Aktion</span>
        </div>

        <div class="question-list" id="poolList">

            <?php if (empty($pools)): ?>
                <div class="empty-list-hint">
                    Keine Fragenpools gefunden.
                </div>
            <?php else: ?>
                <?php foreach ($pools as $p):
                    $depts   = $poolDepts[(int)$p['id']] ?? [];
                    $pData = [
                        'id'          => $p['id'],
                        'name'        => $p['name'],
                        'description' => $p['description'] ?? '',
                        'status'      => $p['is_active'] ? 'active' : 'inactive',
                        'departments' => array_map(fn($d) => $d['id'], $depts),
                    ];
                    $created  = $p['created_at'] ? date('d.m.Y', strtotime($p['created_at'])) : '–';
                    $creator  = $p['creator_name'] ?: 'Unbekannt';
                    $qCount   = (int)$p['question_count'];
                    $metaLine = 'von ' . $creator . ' · ' . $created . ' · ' . $qCount . ' Frage' . ($qCount !== 1 ? 'n' : '');
                ?>
                    <div class="question-row" data-active="<?php echo $p['is_active']; ?>" data-tooltip="<?php echo e($p['description'] !== '' ? $p['description'] : $p['name']); ?>">
                        <div class="col-frage">
                            <div class="mq-frage-texts">
                                <div class="question-text">
                                    <?php echo e($p['name']); ?>
                                </div>
                                <div class="mq-dept-tags">
                                    <?php if (empty($depts)): ?>
                                        <span class="mq-dept-tag is-empty">Kein Fachbereich</span>
                                    <?php else: ?>
                                        <?php foreach ($depts as $d): ?>
                                            <span class="mq-dept-tag"><?php echo e($d['name']); ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="question-pool-label"><?php echo e($metaLine); ?></div>
                            </div>
                        </div>

                        <div class="col-qaktion">
                            <form method="POST" action="manage_question_pools.php<?php echo $filterQuery; ?>" class="inline-form">
                                <input type="hidden" name="action" value="toggle_status">
                                <input type="hidden" name="pool_id" value="<?php echo $p['id']; ?>">
                                <button type="submit" class="btn-icon <?php echo $p['is_active'] ? 'btn-toggle-active' : 'btn-toggle-inactive'; ?>">
                                    <?php echo $p['is_active'] ? '● Aktiv' : '○ Inaktiv'; ?>
                                </button>
                            </form>
                            <button type="button" class="btn-icon btn-edit"
                                data-pool="<?php echo e(json_encode($pData)); ?>"
                                onclick="openEditPoolModal(this)">
                                Bearbeiten
                            </button>
                            <button type="button" class="btn-icon btn-delete"
                                onclick="openDeletePoolModal(<?php echo $p['id']; ?>, this)"
                                data-name="<?php echo e($p['name']); ?>"
                                data-count="<?php echo $qCount; ?>"
                                aria-label="Fragenpool löschen" title="Fragenpool löschen">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                    <path d="M10 11v6M14 11v6"></path>
                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>

        <?php if (!empty($pools)): ?>
            <div class="mq-pagination" id="mqPagination">
                <div class="mq-pagination-info">
                    <span id="mqRangeInfo"></span>
                </div>
                <div class="mq-pagination-controls">
                    <label class="mq-perpage">
                        Pro Seite
                        <select id="mqPerPage">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </label>
                    <div class="mq-pager">
                        <button type="button" class="btn-icon mq-pager-btn" id="mqPrev" aria-label="Vorherige Seite">‹</button>
                        <span class="mq-pager-pages" id="mqPages"></span>
                        <button type="button" class="btn-icon mq-pager-btn" id="mqNext" aria-label="Nächste Seite">›</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="dashboard-footer-links">
            <button type="button" class="btn-icon btn-add mq-action-btn" onclick="openCreateModal()">
                Neuen Fragenpool erstellen
            </button>
        </div>

    </section>

</main>

<!-- Modal: Neuer Fragenpool -->
<div class="modal-overlay" id="createPoolModal">
    <div class="modal">
        <div class="modal-title">Neuen Fragenpool hinzufügen</div>
        <div class="modal-subtitle">Lege einen neuen Themenbereich für Fragen an.</div>

        <form method="POST" action="manage_question_pools.php<?php echo $filterQuery; ?>">
            <input type="hidden" name="action" value="create_pool">

            <div class="modal-field">
                <label>Name des Fragenpools</label>
                <input type="text" name="name" id="createPoolName" placeholder="z. B. PHP Grundlagen" required>
            </div>

            <div class="modal-field">
                <label>Beschreibung</label>
                <input type="text" name="description" id="createPoolDescription" placeholder="Kurze Beschreibung des Themenbereichs">
            </div>

            <div class="modal-field">
                <label>Fachbereich(e)</label>
                <div class="mq-dept-tree" id="createPoolDepts">
                    <?php echo renderDepartmentTree($deptRoots, $deptChildren); ?>
                </div>
                <span class="modal-hint">Ein Fragenpool muss mindestens einem Fachbereich zugeordnet sein. Mehrfachauswahl möglich.</span>
            </div>

            <div class="modal-field">
                <span class="modal-hint">Neue Pools sind sofort aktiv und können jederzeit über den Button deaktiviert werden.</span>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeCreateModal()">Abbrechen</button>
                <button type="submit" class="btn-icon btn-edit">Pool erstellen</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Fragenpool bearbeiten -->
<div class="modal-overlay" id="editPoolModal">
    <div class="modal">
        <div class="modal-title">Fragenpool bearbeiten</div>
        <div class="modal-subtitle" id="editPoolSubtitle">Pooldaten anpassen</div>

        <form method="POST" action="manage_question_pools.php<?php echo $filterQuery; ?>">
            <input type="hidden" name="action" value="update_pool">
            <input type="hidden" name="pool_id" id="editPoolId">

            <div class="modal-field">
                <label>Name des Fragenpools</label>
                <input type="text" name="name" id="editPoolName" required>
            </div>

            <div class="modal-field">
                <label>Beschreibung</label>
                <input type="text" name="description" id="editPoolDescription" placeholder="Kurze Beschreibung des Themenbereichs">
            </div>

            <div class="modal-field">
                <label>Fachbereich(e)</label>
                <div class="mq-dept-tree" id="editPoolDepts">
                    <?php echo renderDepartmentTree($deptRoots, $deptChildren); ?>
                </div>
                <span class="modal-hint">Mindestens ein Fachbereich erforderlich. Mehrfachauswahl möglich.</span>
            </div>

            <div class="modal-field">
                <label>Status</label>
                <select name="pool_status" id="editPoolStatus">
                    <option value="active">Aktiv</option>
                    <option value="inactive">Inaktiv</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeEditPoolModal()">Abbrechen</button>
                <button type="submit" class="btn-icon btn-edit">Speichern</button>
            </div>
        </form>
    </div>
</div>

<!-- Lösch-Bestätigung Fragenpool -->
<div class="modal-overlay" id="deletePoolModal">
    <div class="modal">
        <div class="modal-title">Fragenpool löschen</div>
        <div class="modal-subtitle" id="deletePoolSubtitle">Möchtest du diesen Fragenpool wirklich löschen?</div>

        <form method="POST" action="manage_question_pools.php<?php echo $filterQuery; ?>">
            <input type="hidden" name="action" value="delete_pool">
            <input type="hidden" name="pool_id" id="deletePoolId">

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeDeletePoolModal()">Nein, abbrechen</button>
                <button type="submit" class="btn-danger">Ja, löschen</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Create-Modal
    function openCreateModal()  { document.getElementById('createPoolModal').classList.add('active'); }
    function closeCreateModal() { document.getElementById('createPoolModal').classList.remove('active'); }

    document.getElementById('createPoolModal').addEventListener('click', function(e) {
        if (e.target === this) closeCreateModal();
    });

    // Edit-Modal: Daten aus data-pool JSON befüllen
    function openEditPoolModal(btn) {
        const data = JSON.parse(btn.getAttribute('data-pool'));
        document.getElementById('editPoolId').value          = data.id;
        document.getElementById('editPoolName').value         = data.name;
        document.getElementById('editPoolDescription').value  = data.description || '';
        document.getElementById('editPoolStatus').value       = data.status;
        document.getElementById('editPoolSubtitle').textContent = 'Pool #' + data.id;

        // Fachbereiche vorbelegen
        const selected = (data.departments || []).map(Number);
        document.querySelectorAll('#editPoolDepts input[name="departments[]"]').forEach(cb => {
            cb.checked = selected.includes(parseInt(cb.value, 10));
        });

        document.getElementById('editPoolModal').classList.add('active');
    }

    function closeEditPoolModal() {
        document.getElementById('editPoolModal').classList.remove('active');
    }

    document.getElementById('editPoolModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditPoolModal();
    });

    // Lösch-Bestätigung
    function openDeletePoolModal(id, btn) {
        document.getElementById('deletePoolId').value = id;
        const name  = btn.getAttribute('data-name') || '';
        const count = parseInt(btn.getAttribute('data-count'), 10) || 0;
        const sub   = document.getElementById('deletePoolSubtitle');
        if (count > 0) {
            sub.textContent = '„' + name + '" hat noch ' + count + ' zugeordnete Frage' + (count !== 1 ? 'n' : '')
                + '. Solange Fragen zugeordnet sind, kann der Pool nicht gelöscht werden.';
        } else {
            sub.textContent = '„' + name + '" wirklich löschen? Das kann nicht rückgängig gemacht werden.';
        }
        document.getElementById('deletePoolModal').classList.add('active');
    }

    function closeDeletePoolModal() {
        document.getElementById('deletePoolModal').classList.remove('active');
    }

    document.getElementById('deletePoolModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeletePoolModal();
    });

    // Bei Validierungsfehler das Erstellen-Modal wieder öffnen
    <?php if ($openModalOnError === 'create'): ?>openCreateModal();<?php endif; ?>

    // Pagination der Pool-Liste
    (function () {
        const list = document.getElementById('poolList');
        if (!list) return;
        const rows = Array.from(list.querySelectorAll('.question-row'));
        const bar  = document.getElementById('mqPagination');
        if (!rows.length || !bar) { if (bar) bar.style.display = 'none'; return; }

        const perPageSel = document.getElementById('mqPerPage');
        const rangeInfo  = document.getElementById('mqRangeInfo');
        const pagesBox   = document.getElementById('mqPages');
        const prevBtn    = document.getElementById('mqPrev');
        const nextBtn    = document.getElementById('mqNext');

        let perPage = parseInt(perPageSel.value, 10) || 10;
        let current = 1;

        function totalPages() {
            return Math.max(1, Math.ceil(rows.length / perPage));
        }

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

        function render() {
            const total = totalPages();
            if (current > total) current = total;

            const start = (current - 1) * perPage;
            const end   = start + perPage;
            rows.forEach((row, i) => {
                row.style.display = (i >= start && i < end) ? '' : 'none';
            });

            const from = rows.length ? start + 1 : 0;
            const to   = Math.min(end, rows.length);
            rangeInfo.textContent = from + '–' + to + ' von ' + rows.length;

            pagesBox.innerHTML = '';
            pageList(total, current).forEach(p => {
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
                    btn.addEventListener('click', () => { current = p; render(); });
                    pagesBox.appendChild(btn);
                }
            });

            prevBtn.disabled = current <= 1;
            nextBtn.disabled = current >= total;
        }

        prevBtn.addEventListener('click', () => { if (current > 1) { current--; render(); } });
        nextBtn.addEventListener('click', () => { if (current < totalPages()) { current++; render(); } });
        perPageSel.addEventListener('change', () => {
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
