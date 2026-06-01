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

function e($val) {
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}

// Bild-Upload-Ordner (gleicher Pfad wie in manage_media.php)
$imageUploadDir    = __DIR__ . '/../uploads/questions/';
$imageUploadUrl    = '../uploads/questions/';
$imageExtensions   = ['jpg', 'jpeg', 'png', 'webp'];

// Verfügbare, hochgeladene Bilder einlesen (Dateien auf der Platte)
$availableImages = [];
if (is_dir($imageUploadDir)) {
    foreach (scandir($imageUploadDir) as $f) {
        if ($f === '.' || $f === '..') continue;
        if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $imageExtensions, true)) {
            $availableImages[] = $f;
        }
    }
    rsort($availableImages);
}

/**
 * Wandelt einen Bild-Dateinamen in eine media_files-ID um. Existiert noch kein
 * Datensatz für die Datei, wird er angelegt (manage_media.php legt nur Dateien ab).
 * Gibt null zurück, wenn kein/ein ungültiges Bild gewählt wurde.
 */
function resolveQuestionImageId(?string $fileName, PDO $pdo, string $dir, array $exts, ?int $userId): ?int {
    $fileName = trim((string)$fileName);
    if ($fileName === '') return null;
    $fileName = basename($fileName); // Schutz vor Pfad-Tricks
    $path = $dir . $fileName;
    if (!is_file($path)) return null;
    if (!in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), $exts, true)) return null;

    $stmt = $pdo->prepare("SELECT id FROM media_files WHERE file_name = ? LIMIT 1");
    $stmt->execute([$fileName]);
    $row = $stmt->fetch();
    if ($row) return (int)$row['id'];

    // Noch nicht registriert -> Datensatz nachtragen
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($path) ?: 'application/octet-stream';
    $size  = (int)(@filesize($path) ?: 0);
    $ins = $pdo->prepare(
        "INSERT INTO media_files (file_name, original_name, mime_type, file_size, created_by)
         VALUES (?, ?, ?, ?, ?)"
    );
    $ins->execute([$fileName, $fileName, $mime, $size, $userId]);
    return (int)$pdo->lastInsertId();
}

// Eingeloggten User ermitteln – primär über die beim Login gesetzte Session-ID
$currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

// Fallback: über den Benutzernamen, falls die Session-ID einmal fehlt
if ($currentUserId === null && $username !== 'Gast') {
    $stmtUser = $pdo->prepare("SELECT id FROM users WHERE username = :u LIMIT 1");
    $stmtUser->execute([':u' => $username]);
    $currentUser   = $stmtUser->fetch();
    $currentUserId = $currentUser ? (int)$currentUser['id'] : null;
}

$message     = '';
$messageType = '';

// POST-Aktionen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Status umschalten
    if ($action === 'toggle_status') {
        $questionId = (int)($_POST['question_id'] ?? 0);
        if ($questionId > 0) {
            $stmt = $pdo->prepare(
                "UPDATE questions SET is_active = 1 - is_active, updated_by = ?, updated_at = NOW() WHERE id = ?"
            );
            $stmt->execute([$currentUserId, $questionId]);
            $message     = 'Status erfolgreich geändert.';
            $messageType = 'success';
        }
    }

    // Frage löschen
    if ($action === 'delete_question') {
        $questionId = (int)($_POST['question_id'] ?? 0);
        if ($questionId > 0) {
            $pdo->prepare("DELETE FROM answer_options WHERE question_id = ?")->execute([$questionId]);
            $pdo->prepare("DELETE FROM questions WHERE id = ?")->execute([$questionId]);
            $message     = 'Frage erfolgreich gelöscht.';
            $messageType = 'success';
        }
    }

    // Frage aktualisieren
    if ($action === 'update_question') {
        $questionId   = (int)($_POST['question_id'] ?? 0);
        $poolId       = (int)($_POST['question_pool'] ?? 0);
        $questionText = trim($_POST['question_text'] ?? '');
        $explanation  = trim($_POST['explanation'] ?? '');
        $isActive     = ($_POST['question_status'] ?? 'inactive') === 'active' ? 1 : 0;

        $answers = [
            'A' => ['text' => trim($_POST['answer_a'] ?? ''),           'exp' => trim($_POST['answer_a_explanation'] ?? '')],
            'B' => ['text' => trim($_POST['answer_b'] ?? ''),           'exp' => trim($_POST['answer_b_explanation'] ?? '')],
            'C' => ['text' => trim($_POST['answer_c'] ?? ''),           'exp' => trim($_POST['answer_c_explanation'] ?? '')],
            'D' => ['text' => trim($_POST['answer_d'] ?? ''),           'exp' => trim($_POST['answer_d_explanation'] ?? '')],
        ];
        $correctAnswers = $_POST['correct_answers'] ?? [];

        $imageId = resolveQuestionImageId($_POST['image_file'] ?? '', $pdo, $imageUploadDir, $imageExtensions, $currentUserId);

        if ($questionId > 0 && $questionText !== '' && $poolId > 0) {
            $stmtQ = $pdo->prepare(
                "UPDATE questions SET question_pool_id = ?, question_text = ?, image_id = ?, explanation = ?,
                 is_active = ?, updated_by = ?, updated_at = NOW() WHERE id = ?"
            );
            $stmtQ->execute([$poolId, $questionText, $imageId, $explanation, $isActive, $currentUserId, $questionId]);

            // Antworten: löschen und neu einsetzen
            $pdo->prepare("DELETE FROM answer_options WHERE question_id = ?")->execute([$questionId]);

            $stmtIns = $pdo->prepare(
                "INSERT INTO answer_options (question_id, sort_order, answer_text, is_correct, explanation, created_by)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $sort = 1;
            foreach ($answers as $letter => $ans) {
                if ($ans['text'] !== '') {
                    $stmtIns->execute([
                        $questionId, $sort, $ans['text'],
                        in_array($letter, $correctAnswers) ? 1 : 0,
                        $ans['exp'], $currentUserId
                    ]);
                    $sort++;
                }
            }

            $message     = 'Frage erfolgreich aktualisiert.';
            $messageType = 'success';
        } else {
            $message     = 'Pflichtfelder fehlen (Pool, Fragetext).';
            $messageType = 'error';
        }
    }

    // Neue Frage erstellen
    if ($action === 'create_question') {
        $poolId       = (int)($_POST['question_pool'] ?? 0);
        $questionText = trim($_POST['question_text'] ?? '');
        $explanation  = trim($_POST['explanation'] ?? '');
        // Neue Fragen werden grundsätzlich inaktiv angelegt und müssen per Hand aktiviert werden.
        $isActive     = 0;

        $answers = [
            'A' => ['text' => trim($_POST['answer_a'] ?? ''),  'exp' => trim($_POST['answer_a_explanation'] ?? '')],
            'B' => ['text' => trim($_POST['answer_b'] ?? ''),  'exp' => trim($_POST['answer_b_explanation'] ?? '')],
            'C' => ['text' => trim($_POST['answer_c'] ?? ''),  'exp' => trim($_POST['answer_c_explanation'] ?? '')],
            'D' => ['text' => trim($_POST['answer_d'] ?? ''),  'exp' => trim($_POST['answer_d_explanation'] ?? '')],
        ];
        $correctAnswers = $_POST['correct_answers'] ?? [];

        $hasAnswer = !empty(array_filter(array_column($answers, 'text')));
        $imageId   = resolveQuestionImageId($_POST['image_file'] ?? '', $pdo, $imageUploadDir, $imageExtensions, $currentUserId);

        if ($poolId > 0 && $questionText !== '' && $hasAnswer) {
            $stmtQ = $pdo->prepare(
                "INSERT INTO questions (question_pool_id, question_text, image_id, explanation, created_by, is_active)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmtQ->execute([$poolId, $questionText, $imageId, $explanation, $currentUserId, $isActive]);
            $newId = (int)$pdo->lastInsertId();

            $stmtIns = $pdo->prepare(
                "INSERT INTO answer_options (question_id, sort_order, answer_text, is_correct, explanation, created_by)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $sort = 1;
            foreach ($answers as $letter => $ans) {
                if ($ans['text'] !== '') {
                    $stmtIns->execute([
                        $newId, $sort, $ans['text'],
                        in_array($letter, $correctAnswers) ? 1 : 0,
                        $ans['exp'], $currentUserId
                    ]);
                    $sort++;
                }
            }

            $message     = 'Frage erfolgreich erstellt.';
            $messageType = 'success';
        } else {
            $message     = 'Pflichtfelder fehlen (Pool, Fragetext, mindestens eine Antwort).';
            $messageType = 'error';
        }
    }

    // XLSX importieren
    if ($action === 'import_questions') {
        $poolId = (int)($_POST['import_pool'] ?? 0);

        if ($poolId <= 0) {
            $message     = 'Bitte zuerst einen Fragenpool auswählen.';
            $messageType = 'error';
        } elseif (empty($_FILES['import_file']['tmp_name']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            $message     = 'Keine gültige Datei hochgeladen.';
            $messageType = 'error';
        } else {
            // XLSX nativ parsen (ZIP + XML, kein Composer nötig)
            function parseXlsxRows(string $filePath): array {
                $zip = new ZipArchive();
                if ($zip->open($filePath) !== true) return [];

                // Shared Strings laden
                $sharedStrings = [];
                $ssXml = $zip->getFromName('xl/sharedStrings.xml');
                if ($ssXml !== false) {
                    $ss = new SimpleXMLElement($ssXml);
                    foreach ($ss->si as $si) {
                        // Alle <t>-Elemente zusammenführen (Inline-Formatierung)
                        $text = '';
                        foreach ($si->xpath('.//t') as $t) {
                            $text .= (string)$t;
                        }
                        $sharedStrings[] = $text;
                    }
                }

                // Sheet1 laden
                $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
                $zip->close();
                if ($sheetXml === false) return [];

                $sheet = new SimpleXMLElement($sheetXml);
                $sheet->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                $rows = [];

                foreach ($sheet->xpath('//x:row') as $row) {
                    $rowData = [];
                    foreach ($row->xpath('x:c') as $cell) {
                        // Spaltenindex aus Zelladresse (A1 → 0, B1 → 1 ...)
                        $ref  = (string)$cell['r'];
                        preg_match('/^([A-Z]+)/', $ref, $m);
                        $col  = 0;
                        foreach (str_split($m[1]) as $ch) {
                            $col = $col * 26 + (ord($ch) - ord('A') + 1);
                        }
                        $col--; // 0-basiert

                        $type  = (string)$cell['t'];
                        $value = (string)$cell->v;

                        if ($type === 's') {
                            $value = $sharedStrings[(int)$value] ?? '';
                        } elseif ($type === 'inlineStr') {
                            $value = (string)$cell->is->t;
                        }

                        $rowData[$col] = $value;
                    }
                    $rows[] = $rowData;
                }
                return $rows;
            }

            $tmpPath = $_FILES['import_file']['tmp_name'];
            $allRows = parseXlsxRows($tmpPath);

            // Zeile 0 = Display-Header (★ PFLICHTFELD …), Zeile 1 = Feldnamen, ab Zeile 2 = Daten
            if (count($allRows) < 2) {
                $message     = 'Die Datei enthält keine verwertbaren Zeilen.';
                $messageType = 'error';
            } else {
                // Spalten-Map aus Zeile 1 (technische Namen, vor dem \n)
                $colMap = [];
                foreach ($allRows[1] as $idx => $raw) {
                    $techName = trim(explode("\n", $raw)[0]);
                    if ($techName !== '') {
                        $colMap[$techName] = $idx;
                    }
                }

                $required = ['question_text', 'answer_a', 'correct_answers'];
                // Importierte Fragen werden inaktiv angelegt und müssen per Hand aktiviert werden.
                $stmtQ    = $pdo->prepare(
                    "INSERT INTO questions (question_pool_id, question_text, image_id, explanation, created_by, is_active)
                     VALUES (?, ?, ?, ?, ?, 0)"
                );
                $stmtA = $pdo->prepare(
                    "INSERT INTO answer_options (question_id, sort_order, answer_text, is_correct, explanation, created_by)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );

                $imported  = 0;
                $skipped   = 0;

                foreach (array_slice($allRows, 2) as $row) {
                    // Leere Zeile überspringen
                    if (empty(array_filter($row, fn($v) => trim($v) !== ''))) continue;

                    $get = fn(string $key) => trim($row[$colMap[$key] ?? -1] ?? '');

                    $questionText = $get('question_text');
                    $answerA      = $get('answer_a');
                    $correctRaw   = $get('correct_answers');

                    if ($questionText === '' || $answerA === '' || $correctRaw === '') {
                        $skipped++;
                        continue;
                    }

                    // correct_answers: "A,C" → ['A','C']
                    $correctLetters = array_map('trim', explode(',', strtoupper($correctRaw)));

                    $explanation = $get('question_explanation');
                    $imageId     = resolveQuestionImageId($get('image'), $pdo, $imageUploadDir, $imageExtensions, $currentUserId);
                    $stmtQ->execute([$poolId, $questionText, $imageId, $explanation, $currentUserId]);
                    $newId = (int)$pdo->lastInsertId();

                    $answerDefs = [
                        'A' => ['answer_a', 'answer_a_explanation'],
                        'B' => ['answer_b', 'answer_b_explanation'],
                        'C' => ['answer_c', 'answer_c_explanation'],
                        'D' => ['answer_d', 'answer_d_explanation'],
                    ];

                    $sort = 1;
                    foreach ($answerDefs as $letter => [$textKey, $expKey]) {
                        $ansText = $get($textKey);
                        if ($ansText === '') continue;
                        $stmtA->execute([
                            $newId, $sort, $ansText,
                            in_array($letter, $correctLetters) ? 1 : 0,
                            $get($expKey),
                            $currentUserId
                        ]);
                        $sort++;
                    }

                    $imported++;
                }

                if ($imported > 0) {
                    $message = $imported . ' Frage' . ($imported !== 1 ? 'n' : '') . ' erfolgreich importiert'
                        . ($skipped > 0 ? ', ' . $skipped . ' übersprungen (Pflichtfeld fehlte)' : '') . '.';
                    $messageType = 'success';
                } else {
                    $message     = 'Keine Fragen importiert. Bitte Pflichtfelder prüfen (Fragetext, Antwort A, Richtige Antworten).';
                    $messageType = 'error';
                }
            }
        }
    }
}

// Bei Fehlern das passende Modal nach dem Reload wieder öffnen
$openModalOnError = '';
if ($messageType === 'error' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['action'] ?? '';
    if ($act === 'create_question')      $openModalOnError = 'create';
    elseif ($act === 'import_questions') $openModalOnError = 'import';
}

// Daten laden: alle aktiven Fragenpools
$pools = $pdo->query(
    "SELECT id, name FROM question_pools WHERE is_active = 1 ORDER BY name ASC"
)->fetchAll(PDO::FETCH_ASSOC);

// Pool-Filter aus GET
$selectedPool = isset($_GET['pool']) ? (int)$_GET['pool'] : 0;

// Fragen laden (mit Pool-Name)
if ($selectedPool > 0) {
    $stmtQ = $pdo->prepare("
        SELECT q.id, q.question_pool_id, q.question_text, q.explanation, q.is_active, q.image_id,
               qp.name AS pool_name, mf.file_name AS image_file
        FROM questions q
        INNER JOIN question_pools qp ON qp.id = q.question_pool_id
        LEFT JOIN media_files mf ON mf.id = q.image_id
        WHERE q.question_pool_id = ?
        ORDER BY q.id ASC
    ");
    $stmtQ->execute([$selectedPool]);
} else {
    $stmtQ = $pdo->query("
        SELECT q.id, q.question_pool_id, q.question_text, q.explanation, q.is_active, q.image_id,
               qp.name AS pool_name, mf.file_name AS image_file
        FROM questions q
        INNER JOIN question_pools qp ON qp.id = q.question_pool_id
        LEFT JOIN media_files mf ON mf.id = q.image_id
        ORDER BY q.question_pool_id ASC, q.id ASC
    ");
}
$questions = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

$totalQuestions  = count($questions);
$activeQuestions = count(array_filter($questions, fn($q) => $q['is_active']));

// Antworten für alle Fragen laden (für Modal-Vorbefüllung)
$answersMap = [];
if (!empty($questions)) {
    $ids          = array_column($questions, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmtA = $pdo->prepare(
        "SELECT * FROM answer_options WHERE question_id IN ($placeholders) ORDER BY question_id, sort_order ASC"
    );
    $stmtA->execute($ids);
    foreach ($stmtA->fetchAll(PDO::FETCH_ASSOC) as $ans) {
        $answersMap[$ans['question_id']][] = $ans;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Fragen verwalten</title>
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
                <span class="eyebrow">Fragen-Verwaltung</span>
                <h2>Vorhandene Fragen</h2>
            </div>
            <div class="mq-topbar-actions">
                <button type="button" class="btn-icon btn-edit mq-action-btn" onclick="openImportModal()">
                    Importieren
                </button>
                <button type="button" class="btn-icon btn-add mq-action-btn" onclick="openCreateModal()">
                    + Neue Frage
                </button>
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
            <form method="GET" action="manage_questions.php" class="form-group pool-filter-form mq-filter">
                <label for="poolFilter">Fragenpool</label>
                <select id="poolFilter" name="pool" onchange="this.form.submit()">
                    <option value="">Alle Pools</option>
                    <?php foreach ($pools as $pool): ?>
                        <option value="<?php echo $pool['id']; ?>"
                            <?php echo $selectedPool === (int)$pool['id'] ? 'selected' : ''; ?>>
                            <?php echo e($pool['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <div class="lobby-meta mq-meta">
                <div>
                    <span>Gesamt</span>
                    <strong><?php echo $totalQuestions; ?> Fragen</strong>
                </div>
                <div>
                    <span>Aktiv</span>
                    <strong><?php echo $activeQuestions; ?> aktiv</strong>
                </div>
            </div>
        </div>

        <div class="question-list-header">
            <span class="col-frage">Frage</span>
            <span class="col-qaktion">Aktion</span>
        </div>

        <div class="question-list" id="questionList">

            <?php if (empty($questions)): ?>
                <div class="empty-list-hint">
                    Keine Fragen gefunden.
                </div>
            <?php else: ?>
                <?php foreach ($questions as $q):
                    $qAnswers = $answersMap[$q['id']] ?? [];
                    $qData = [
                        'id'          => $q['id'],
                        'pool'        => $q['question_pool_id'],
                        'text'        => $q['question_text'],
                        'explanation' => $q['explanation'] ?? '',
                        'status'      => $q['is_active'] ? 'active' : 'inactive',
                        'image'       => $q['image_file'] ?? '',
                        'answers'     => array_map(fn($a) => [
                            'answer_text' => $a['answer_text'],
                            'explanation' => $a['explanation'],
                            'is_correct'  => (int)$a['is_correct'],
                            'sort_order'  => (int)$a['sort_order'],
                        ], $qAnswers),
                    ];
                ?>
                    <div class="question-row" data-pool="<?php echo $q['question_pool_id']; ?>" data-active="<?php echo $q['is_active']; ?>" data-tooltip="<?php echo e($q['question_text']); ?>">
                        <div class="col-frage">
                            <?php if (!empty($q['image_file'])): ?>
                                <img class="mq-row-thumb" src="<?php echo e($imageUploadUrl . $q['image_file']); ?>" alt="Bild zur Frage">
                            <?php endif; ?>
                            <div class="mq-frage-texts">
                                <div class="question-text">
                                    <?php echo e($q['question_text']); ?>
                                </div>
                                <div class="question-pool-label"><?php echo e($q['pool_name']); ?></div>
                            </div>
                        </div>

                        <div class="col-qaktion">
                            <form method="POST" action="manage_questions.php<?php echo $selectedPool ? '?pool=' . $selectedPool : ''; ?>" class="inline-form">
                                <input type="hidden" name="action" value="toggle_status">
                                <input type="hidden" name="question_id" value="<?php echo $q['id']; ?>">
                                <button type="submit" class="btn-icon <?php echo $q['is_active'] ? 'btn-toggle-active' : 'btn-toggle-inactive'; ?>">
                                    <?php echo $q['is_active'] ? '● Aktiv' : '○ Inaktiv'; ?>
                                </button>
                            </form>
                            <button type="button" class="btn-icon btn-edit"
                                data-question="<?php echo e(json_encode($qData)); ?>"
                                onclick="openEditQuestionModal(this)">
                                Bearbeiten
                            </button>
                            <button type="button" class="btn-icon btn-delete"
                                onclick="openDeleteQuestionModal(<?php echo $q['id']; ?>, this)"
                                data-text="<?php echo e($q['question_text']); ?>"
                                aria-label="Frage löschen" title="Frage löschen">
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

        <?php if (!empty($questions)): ?>
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
            <a href="<?php echo $role === 'admin' ? 'admin_area.php' : 'teacher_area.php'; ?>">
                ← Zurück zum <?php echo $role === 'admin' ? 'Adminbereich' : 'Lehrerbereich'; ?>
            </a>
        </div>

    </section>

</main>

<!-- Modal: Neue Frage hinzufügen -->
<div class="modal-overlay" id="createQuestionModal">
    <div class="modal modal-large">
        <div class="modal-title">Neue Frage hinzufügen</div>
        <div class="modal-subtitle">Wähle einen Fragenpool und erfasse die Frage mit Antworten.</div>

        <form method="POST" action="manage_questions.php<?php echo $selectedPool ? '?pool=' . $selectedPool : ''; ?>">
            <input type="hidden" name="action" value="create_question">

            <div class="modal-field">
                <label>Fragenpool</label>
                <select name="question_pool" id="createQuestionPool" required>
                    <option value="">Bitte auswählen</option>
                    <?php foreach ($pools as $pool): ?>
                        <option value="<?php echo $pool['id']; ?>"><?php echo e($pool['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="modal-field">
                <label>Fragetext</label>
                <input type="text" name="question_text" id="createQuestionText"
                    placeholder="z. B. Was macht die Funktion htmlspecialchars()?" required>
            </div>

            <div class="modal-field-pair">
                <div class="modal-field">
                    <label>Antwort A</label>
                    <input type="text" name="answer_a" placeholder="Antwortmöglichkeit A">
                </div>
                <div class="modal-field">
                    <label>Erklärung A</label>
                    <input type="text" name="answer_a_explanation" placeholder="Warum ist Antwort A richtig oder falsch?">
                </div>
            </div>

            <div class="modal-field-pair">
                <div class="modal-field">
                    <label>Antwort B</label>
                    <input type="text" name="answer_b" placeholder="Antwortmöglichkeit B">
                </div>
                <div class="modal-field">
                    <label>Erklärung B</label>
                    <input type="text" name="answer_b_explanation" placeholder="Warum ist Antwort B richtig oder falsch?">
                </div>
            </div>

            <div class="modal-field-pair">
                <div class="modal-field">
                    <label>Antwort C</label>
                    <input type="text" name="answer_c" placeholder="Antwortmöglichkeit C">
                </div>
                <div class="modal-field">
                    <label>Erklärung C</label>
                    <input type="text" name="answer_c_explanation" placeholder="Warum ist Antwort C richtig oder falsch?">
                </div>
            </div>

            <div class="modal-field-pair">
                <div class="modal-field">
                    <label>Antwort D</label>
                    <input type="text" name="answer_d" placeholder="Antwortmöglichkeit D">
                </div>
                <div class="modal-field">
                    <label>Erklärung D</label>
                    <input type="text" name="answer_d_explanation" placeholder="Warum ist Antwort D richtig oder falsch?">
                </div>
            </div>

            <div class="modal-field">
                <label>Richtige Antworten</label>
                <div class="modal-checkboxes">
                    <label class="modal-checkbox-label"><input type="checkbox" name="correct_answers[]" value="A"> Antwort A</label>
                    <label class="modal-checkbox-label"><input type="checkbox" name="correct_answers[]" value="B"> Antwort B</label>
                    <label class="modal-checkbox-label"><input type="checkbox" name="correct_answers[]" value="C"> Antwort C</label>
                    <label class="modal-checkbox-label"><input type="checkbox" name="correct_answers[]" value="D"> Antwort D</label>
                </div>
            </div>

            <div class="modal-field">
                <label>Allgemeine Erklärung</label>
                <input type="text" name="explanation" placeholder="Optionale allgemeine Erklärung zur gesamten Frage">
            </div>

            <div class="modal-field">
                <label>Bild (optional)</label>
                <select name="image_file" id="createImageSelect" onchange="updateImagePreview('create')">
                    <option value="">Kein Bild</option>
                    <?php foreach ($availableImages as $img): ?>
                        <option value="<?php echo e($img); ?>"><?php echo e($img); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="mq-image-preview" id="createImagePreview"></div>
                <span class="modal-hint">Bilder zuerst unter <a href="manage_media.php" target="_blank">Medien verwalten</a> hochladen, dann hier auswählen.</span>
            </div>

            <div class="modal-field">
                <span class="modal-hint">Neue Fragen werden inaktiv angelegt und müssen anschließend in der Liste per Hand aktiviert werden.</span>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeCreateModal()">Abbrechen</button>
                <button type="submit" class="btn-icon btn-edit">Frage erstellen</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Fragen importieren -->
<div class="modal-overlay" id="importModal">
    <div class="modal modal-large">
        <div class="import-header">
            <div>
                <div class="modal-title">Fragen importieren</div>
                <div class="modal-subtitle">XLSX-Datei hochladen und mehrere Fragen auf einmal importieren.</div>
            </div>
            <a href="../../Uploads/Vorlagen/fragen_import_vorlage.xlsx" class="import-download-btn" download>
                Vorlage herunterladen
            </a>
        </div>

        <form method="POST" action="manage_questions.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="import_questions">

            <div class="modal-field">
                <label>Fragenpool</label>
                <select id="import_pool" name="import_pool" onchange="checkImportReady()">
                    <option value="">Bitte auswählen</option>
                    <?php foreach ($pools as $pool): ?>
                        <option value="<?php echo $pool['id']; ?>"><?php echo e($pool['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="import-drop-zone" id="importDropZone">
                <input type="file" name="import_file" id="importFile" accept=".xlsx" hidden>
                <div class="import-drop-content" id="importDropContent">
                    <div class="import-drop-icon">XLS</div>
                    <p>XLSX-Datei hierher ziehen oder <span class="import-browse-link" onclick="document.getElementById('importFile').click()">auswählen</span></p>
                    <span class="import-drop-hint">Pflichtfelder: Fragetext, Antwort A, Richtige Antworten</span>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeImportModal()">Abbrechen</button>
                <button type="submit" class="btn-icon btn-edit import-submit" id="importSubmit" disabled>Importieren</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit-Modal Frage -->
<div class="modal-overlay" id="editQuestionModal">
    <div class="modal modal-large">
        <div class="modal-title">Frage bearbeiten</div>
        <div class="modal-subtitle" id="editQuestionSubtitle">Fragedaten anpassen</div>

        <form method="POST" action="manage_questions.php<?php echo $selectedPool ? '?pool=' . $selectedPool : ''; ?>">
            <input type="hidden" name="action" value="update_question">
            <input type="hidden" name="question_id" id="editQuestionId">

            <div class="modal-field">
                <label>Fragenpool</label>
                <select name="question_pool" id="editQuestionPool">
                    <?php foreach ($pools as $pool): ?>
                        <option value="<?php echo $pool['id']; ?>"><?php echo e($pool['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="modal-field">
                <label>Fragetext</label>
                <input type="text" name="question_text" id="editQuestionText" required>
            </div>

            <div class="modal-field-pair">
                <div class="modal-field">
                    <label>Antwort A</label>
                    <input type="text" name="answer_a" id="editAnswerA" placeholder="Antwortmöglichkeit A">
                </div>
                <div class="modal-field">
                    <label>Erklärung A</label>
                    <input type="text" name="answer_a_explanation" id="editAnswerAExp" placeholder="Erklärung zu Antwort A">
                </div>
            </div>

            <div class="modal-field-pair">
                <div class="modal-field">
                    <label>Antwort B</label>
                    <input type="text" name="answer_b" id="editAnswerB" placeholder="Antwortmöglichkeit B">
                </div>
                <div class="modal-field">
                    <label>Erklärung B</label>
                    <input type="text" name="answer_b_explanation" id="editAnswerBExp" placeholder="Erklärung zu Antwort B">
                </div>
            </div>

            <div class="modal-field-pair">
                <div class="modal-field">
                    <label>Antwort C</label>
                    <input type="text" name="answer_c" id="editAnswerC" placeholder="Antwortmöglichkeit C">
                </div>
                <div class="modal-field">
                    <label>Erklärung C</label>
                    <input type="text" name="answer_c_explanation" id="editAnswerCExp" placeholder="Erklärung zu Antwort C">
                </div>
            </div>

            <div class="modal-field-pair">
                <div class="modal-field">
                    <label>Antwort D</label>
                    <input type="text" name="answer_d" id="editAnswerD" placeholder="Antwortmöglichkeit D">
                </div>
                <div class="modal-field">
                    <label>Erklärung D</label>
                    <input type="text" name="answer_d_explanation" id="editAnswerDExp" placeholder="Erklärung zu Antwort D">
                </div>
            </div>

            <div class="modal-field">
                <label>Richtige Antworten</label>
                <div class="modal-checkboxes">
                    <label class="modal-checkbox-label"><input type="checkbox" name="correct_answers[]" value="A" id="editCorrectA"> Antwort A</label>
                    <label class="modal-checkbox-label"><input type="checkbox" name="correct_answers[]" value="B" id="editCorrectB"> Antwort B</label>
                    <label class="modal-checkbox-label"><input type="checkbox" name="correct_answers[]" value="C" id="editCorrectC"> Antwort C</label>
                    <label class="modal-checkbox-label"><input type="checkbox" name="correct_answers[]" value="D" id="editCorrectD"> Antwort D</label>
                </div>
            </div>

            <div class="modal-field">
                <label>Allgemeine Erklärung</label>
                <input type="text" name="explanation" id="editExplanation" placeholder="Optionale allgemeine Erklärung">
            </div>

            <div class="modal-field">
                <label>Bild (optional)</label>
                <select name="image_file" id="editImageSelect" onchange="updateImagePreview('edit')">
                    <option value="">Kein Bild</option>
                    <?php foreach ($availableImages as $img): ?>
                        <option value="<?php echo e($img); ?>"><?php echo e($img); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="mq-image-preview" id="editImagePreview"></div>
                <span class="modal-hint">Bilder zuerst unter <a href="manage_media.php" target="_blank">Medien verwalten</a> hochladen, dann hier auswählen.</span>
            </div>

            <div class="modal-field">
                <label>Status</label>
                <select name="question_status" id="editQuestionStatus">
                    <option value="active">Aktiv</option>
                    <option value="inactive">Inaktiv</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeEditQuestionModal()">Abbrechen</button>
                <button type="submit" class="btn-icon btn-edit">Speichern</button>
            </div>
        </form>
    </div>
</div>

<!-- Lösch-Bestätigung Frage -->
<div class="modal-overlay" id="deleteQuestionModal">
    <div class="modal">
        <div class="modal-title">Frage löschen</div>
        <div class="modal-subtitle" id="deleteQuestionSubtitle">Möchtest du diese Frage wirklich löschen?</div>

        <form method="POST" action="manage_questions.php<?php echo $selectedPool ? '?pool=' . $selectedPool : ''; ?>">
            <input type="hidden" name="action" value="delete_question">
            <input type="hidden" name="question_id" id="deleteQuestionId">

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeDeleteQuestionModal()">Nein, abbrechen</button>
                <button type="submit" class="btn-danger">Ja, löschen</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Drag & Drop Import
    const dropZone    = document.getElementById('importDropZone');
    const fileInput   = document.getElementById('importFile');
    const dropContent = document.getElementById('importDropContent');
    const submitBtn   = document.getElementById('importSubmit');

    function checkImportReady() {
        const poolSelected = document.getElementById('import_pool').value !== '';
        const fileSelected = fileInput.files && fileInput.files.length > 0;
        submitBtn.disabled = !(poolSelected && fileSelected);
    }

    function setFile(file) {
        if (!file) return;
        dropContent.innerHTML = `
            <div class="import-drop-icon">XLS</div>
            <p><strong>${file.name}</strong></p>
            <span class="import-drop-hint">${(file.size / 1024).toFixed(1)} KB — bereit zum Import</span>
        `;
        dropZone.classList.add('has-file');
        checkImportReady();
    }

    fileInput.addEventListener('change', () => setFile(fileInput.files[0]));
    dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file && file.name.endsWith('.xlsx')) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            setFile(file);
        }
    });
    dropZone.addEventListener('click', () => fileInput.click());

    // Edit-Modal: Daten aus data-question JSON befüllen
    function openEditQuestionModal(btn) {
        const data    = JSON.parse(btn.getAttribute('data-question'));
        const letters = ['A', 'B', 'C', 'D'];

        document.getElementById('editQuestionId').value     = data.id;
        document.getElementById('editQuestionPool').value   = data.pool;
        document.getElementById('editQuestionText').value   = data.text;
        document.getElementById('editExplanation').value    = data.explanation || '';
        document.getElementById('editQuestionStatus').value = data.status;
        document.getElementById('editQuestionSubtitle').textContent = 'Frage #' + data.id;

        // Antwortfelder leeren und befüllen
        letters.forEach((letter, i) => {
            const ans = data.answers[i] || {};
            document.getElementById('editAnswer' + letter).value    = ans.answer_text || '';
            document.getElementById('editAnswer' + letter + 'Exp').value = ans.explanation || '';
            document.getElementById('editCorrect' + letter).checked = ans.is_correct === 1;
        });

        // Bild vorbelegen
        document.getElementById('editImageSelect').value = data.image || '';
        updateImagePreview('edit');

        document.getElementById('editQuestionModal').classList.add('active');
    }

    function closeEditQuestionModal() {
        document.getElementById('editQuestionModal').classList.remove('active');
    }

    document.getElementById('editQuestionModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditQuestionModal();
    });

    // Lösch-Bestätigung
    function openDeleteQuestionModal(id, btn) {
        document.getElementById('deleteQuestionId').value = id;
        const text = btn.getAttribute('data-text') || '';
        document.getElementById('deleteQuestionSubtitle').textContent =
            text ? '„' + text + '" wirklich löschen? Das kann nicht rückgängig gemacht werden.'
                 : 'Möchtest du diese Frage wirklich löschen? Das kann nicht rückgängig gemacht werden.';
        document.getElementById('deleteQuestionModal').classList.add('active');
    }

    function closeDeleteQuestionModal() {
        document.getElementById('deleteQuestionModal').classList.remove('active');
    }

    document.getElementById('deleteQuestionModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteQuestionModal();
    });

    // Bild-Vorschau im Create-/Edit-Modal aktualisieren
    const mqImageBase = '<?php echo $imageUploadUrl; ?>';
    function updateImagePreview(which) {
        const sel = document.getElementById(which === 'create' ? 'createImageSelect' : 'editImageSelect');
        const box = document.getElementById(which === 'create' ? 'createImagePreview' : 'editImagePreview');
        if (!sel || !box) return;
        const val = sel.value;
        if (val) {
            box.innerHTML = '<img src="' + mqImageBase + encodeURIComponent(val) + '" alt="Bildvorschau">';
            box.classList.add('has-image');
        } else {
            box.innerHTML = '';
            box.classList.remove('has-image');
        }
    }

    // Create- & Import-Modal öffnen/schließen
    function openCreateModal()  { updateImagePreview('create'); document.getElementById('createQuestionModal').classList.add('active'); }
    function closeCreateModal() { document.getElementById('createQuestionModal').classList.remove('active'); }
    function openImportModal()  { document.getElementById('importModal').classList.add('active'); }
    function closeImportModal() { document.getElementById('importModal').classList.remove('active'); }

    document.getElementById('createQuestionModal').addEventListener('click', function(e) {
        if (e.target === this) closeCreateModal();
    });
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) closeImportModal();
    });

    // Bei Validierungsfehler das passende Modal wieder öffnen
    <?php if ($openModalOnError === 'create'): ?>openCreateModal();<?php endif; ?>
    <?php if ($openModalOnError === 'import'): ?>openImportModal();<?php endif; ?>

    // Pagination der Fragenliste
    (function () {
        const list = document.getElementById('questionList');
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

</body>
</html>
