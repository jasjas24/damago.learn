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

// Eingeloggten User ermitteln
$stmtUser = $pdo->prepare("SELECT id FROM users WHERE username = :u LIMIT 1");
$stmtUser->execute([':u' => $username]);
$currentUser   = $stmtUser->fetch();
$currentUserId = $currentUser ? (int)$currentUser['id'] : null;

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

        if ($questionId > 0 && $questionText !== '' && $poolId > 0) {
            $stmtQ = $pdo->prepare(
                "UPDATE questions SET question_pool_id = ?, question_text = ?, explanation = ?,
                 is_active = ?, updated_by = ?, updated_at = NOW() WHERE id = ?"
            );
            $stmtQ->execute([$poolId, $questionText, $explanation, $isActive, $currentUserId, $questionId]);

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
        $isActive     = ($_POST['question_status'] ?? 'active') === 'active' ? 1 : 0;

        $answers = [
            'A' => ['text' => trim($_POST['answer_a'] ?? ''),  'exp' => trim($_POST['answer_a_explanation'] ?? '')],
            'B' => ['text' => trim($_POST['answer_b'] ?? ''),  'exp' => trim($_POST['answer_b_explanation'] ?? '')],
            'C' => ['text' => trim($_POST['answer_c'] ?? ''),  'exp' => trim($_POST['answer_c_explanation'] ?? '')],
            'D' => ['text' => trim($_POST['answer_d'] ?? ''),  'exp' => trim($_POST['answer_d_explanation'] ?? '')],
        ];
        $correctAnswers = $_POST['correct_answers'] ?? [];

        $hasAnswer = !empty(array_filter(array_column($answers, 'text')));

        if ($poolId > 0 && $questionText !== '' && $hasAnswer) {
            $stmtQ = $pdo->prepare(
                "INSERT INTO questions (question_pool_id, question_text, explanation, created_by, is_active)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmtQ->execute([$poolId, $questionText, $explanation, $currentUserId, $isActive]);
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
                $stmtQ    = $pdo->prepare(
                    "INSERT INTO questions (question_pool_id, question_text, explanation, created_by, is_active)
                     VALUES (?, ?, ?, ?, 1)"
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
                    $stmtQ->execute([$poolId, $questionText, $explanation, $currentUserId]);
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

// Daten laden: alle aktiven Fragenpools
$pools = $pdo->query(
    "SELECT id, name FROM question_pools WHERE is_active = 1 ORDER BY name ASC"
)->fetchAll(PDO::FETCH_ASSOC);

// Pool-Filter aus GET
$selectedPool = isset($_GET['pool']) ? (int)$_GET['pool'] : 0;

// Fragen laden (mit Pool-Name)
if ($selectedPool > 0) {
    $stmtQ = $pdo->prepare("
        SELECT q.id, q.question_pool_id, q.question_text, q.explanation, q.is_active,
               qp.name AS pool_name
        FROM questions q
        INNER JOIN question_pools qp ON qp.id = q.question_pool_id
        WHERE q.question_pool_id = ?
        ORDER BY q.id ASC
    ");
    $stmtQ->execute([$selectedPool]);
} else {
    $stmtQ = $pdo->query("
        SELECT q.id, q.question_pool_id, q.question_text, q.explanation, q.is_active,
               qp.name AS pool_name
        FROM questions q
        INNER JOIN question_pools qp ON qp.id = q.question_pool_id
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

    <section class="quiz-main">

        <div class="quiz-topline">
            <span class="eyebrow">Fragen-Verwaltung</span>
        </div>

        <?php if ($message): ?>
            <div class="import-section" style="padding: 12px 20px; margin-bottom: 0;
                background: <?php echo $messageType === 'success' ? 'rgba(34,197,94,0.10)' : 'rgba(239,68,68,0.10)'; ?>;
                border-color: <?php echo $messageType === 'success' ? 'rgba(34,197,94,0.30)' : 'rgba(239,68,68,0.30)'; ?>;">
                <span style="color: <?php echo $messageType === 'success' ? '#86efac' : '#fca5a5'; ?>; font-size: 14px; font-weight: 600;">
                    <?php echo e($message); ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="import-section">
            <div class="import-header">
                <div>
                    <span class="eyebrow">Import</span>
                    <h3>Fragen importieren</h3>
                    <p>XLSX-Datei hochladen und mehrere Fragen auf einmal importieren.</p>
                </div>
                <a href="../../Uploads/Vorlagen/fragen_import_vorlage.xlsx" class="import-download-btn" download>
                    Vorlage herunterladen
                </a>
            </div>

            <form method="POST" action="manage_questions.php" enctype="multipart/form-data" class="import-form">
                <input type="hidden" name="action" value="import_questions">

                <div class="form-group" style="margin-bottom: 12px;">
                    <label for="import_pool">Fragenpool auswählen</label>
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
                <button type="submit" class="btn-icon btn-edit import-submit" id="importSubmit" disabled>
                    Importieren
                </button>
            </form>
        </div>

        <div class="import-section">
            <div class="auth-header">
                <span class="eyebrow">Frage erstellen</span>
                <h2>Neue Frage hinzufügen</h2>
                <p>Wähle zuerst einen Fragenpool aus und erfasse danach die Frage mit Antworten.</p>
            </div>

            <form method="POST" action="manage_questions.php" class="auth-form questions-form">
                <input type="hidden" name="action" value="create_question">

                <div class="form-group">
                    <label for="question_pool">Fragenpool auswählen</label>
                    <select id="question_pool" name="question_pool" required>
                        <option value="">Bitte auswählen</option>
                        <?php foreach ($pools as $pool): ?>
                            <option value="<?php echo $pool['id']; ?>"><?php echo e($pool['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="question_text">Frage</label>
                    <input type="text" id="question_text" name="question_text"
                        placeholder="z. B. Was macht die Funktion htmlspecialchars()?" required>
                </div>

                <div class="answer-pair">
                    <div class="form-group">
                        <label for="answer_a">Antwort A</label>
                        <input type="text" id="answer_a" name="answer_a" placeholder="Antwortmöglichkeit A">
                    </div>
                    <div class="form-group">
                        <label for="answer_a_explanation">Erklärung zu Antwort A</label>
                        <input type="text" id="answer_a_explanation" name="answer_a_explanation" placeholder="Warum ist Antwort A richtig oder falsch?">
                    </div>
                </div>

                <div class="answer-pair">
                    <div class="form-group">
                        <label for="answer_b">Antwort B</label>
                        <input type="text" id="answer_b" name="answer_b" placeholder="Antwortmöglichkeit B">
                    </div>
                    <div class="form-group">
                        <label for="answer_b_explanation">Erklärung zu Antwort B</label>
                        <input type="text" id="answer_b_explanation" name="answer_b_explanation" placeholder="Warum ist Antwort B richtig oder falsch?">
                    </div>
                </div>

                <div class="answer-pair">
                    <div class="form-group">
                        <label for="answer_c">Antwort C</label>
                        <input type="text" id="answer_c" name="answer_c" placeholder="Antwortmöglichkeit C">
                    </div>
                    <div class="form-group">
                        <label for="answer_c_explanation">Erklärung zu Antwort C</label>
                        <input type="text" id="answer_c_explanation" name="answer_c_explanation" placeholder="Warum ist Antwort C richtig oder falsch?">
                    </div>
                </div>

                <div class="answer-pair">
                    <div class="form-group">
                        <label for="answer_d">Antwort D</label>
                        <input type="text" id="answer_d" name="answer_d" placeholder="Antwortmöglichkeit D">
                    </div>
                    <div class="form-group">
                        <label for="answer_d_explanation">Erklärung zu Antwort D</label>
                        <input type="text" id="answer_d_explanation" name="answer_d_explanation" placeholder="Warum ist Antwort D richtig oder falsch?">
                    </div>
                </div>

                <div class="lobby-hints">
                    <h3>Richtige Antwort auswählen</h3>
                    <ul>
                        <li><label><input type="checkbox" name="correct_answers[]" value="A"> Antwort A ist richtig</label></li>
                        <li><label><input type="checkbox" name="correct_answers[]" value="B"> Antwort B ist richtig</label></li>
                        <li><label><input type="checkbox" name="correct_answers[]" value="C"> Antwort C ist richtig</label></li>
                        <li><label><input type="checkbox" name="correct_answers[]" value="D"> Antwort D ist richtig</label></li>
                    </ul>
                </div>

                <div class="form-group">
                    <label for="explanation">Allgemeine Erklärung zur Lösung</label>
                    <input type="text" id="explanation" name="explanation"
                        placeholder="Optionale allgemeine Erklärung zur gesamten Frage">
                </div>

                <div class="form-group">
                    <label for="question_status">Status</label>
                    <select id="question_status" name="question_status">
                        <option value="active" selected>Aktiv</option>
                        <option value="inactive">Inaktiv</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Frage erstellen
                </button>
            </form>
        </div>

    </section>

    <aside class="ranking-panel">

        <div class="ranking-header">
            <span class="eyebrow">Übersicht</span>
            <h2>Vorhandene Fragen</h2>
            <p>Fragenpool wählen, um die Liste zu filtern.</p>
        </div>

        <form method="GET" action="manage_questions.php" class="form-group" style="margin-bottom: 16px;">
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

        <div class="lobby-meta">
            <div>
                <span>Gesamt</span>
                <strong><?php echo $totalQuestions; ?> Fragen</strong>
            </div>
            <div>
                <span>Aktiv</span>
                <strong><?php echo $activeQuestions; ?> aktiv</strong>
            </div>
        </div>

        <div class="question-list-header">
            <span class="col-frage">Frage</span>
            <span class="col-qstatus">Status</span>
            <span class="col-qaktion">Aktion</span>
        </div>

        <div class="question-list">

            <?php if (empty($questions)): ?>
                <div style="padding: 20px 12px; color: rgba(255,255,255,0.4); font-size: 13px; text-align: center;">
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
                            <div class="question-text">
                                <?php echo e($q['question_text']); ?>
                            </div>
                            <div class="question-pool-label"><?php echo e($q['pool_name']); ?></div>
                        </div>

                        <div class="col-qstatus">
                            <span class="badge <?php echo $q['is_active'] ? 'badge-active' : 'badge-inactive'; ?>">
                                <?php echo $q['is_active'] ? 'Aktiv' : 'Inaktiv'; ?>
                            </span>
                        </div>

                        <div class="col-qaktion">
                            <form method="POST" action="manage_questions.php<?php echo $selectedPool ? '?pool=' . $selectedPool : ''; ?>" style="margin:0;">
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
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>

    </aside>

</main>

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

        document.getElementById('editQuestionModal').classList.add('active');
    }

    function closeEditQuestionModal() {
        document.getElementById('editQuestionModal').classList.remove('active');
    }

    document.getElementById('editQuestionModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditQuestionModal();
    });
</script>

</body>
</html>
