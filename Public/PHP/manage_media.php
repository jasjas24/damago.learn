<?php
require_once 'init.php';
require_once 'db.php'; // PDO-Verbindung

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Rollen prüfen
$currentRole = $role ?? ($_SESSION['role'] ?? ($_SESSION['user']['role'] ?? 'guest'));
if (!in_array($currentRole, ['admin', 'teacher'])) {
    header("Location: dashboard.php");
    exit;
}

// Upload-Ordner
$uploadDir = __DIR__ . '/../Uploads/Questions/';
$uploadUrl = '../Uploads/Questions/';
$maxFileSize = 20 * 1024 * 1024;
$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
$allowedMimeTypes = ['image/jpeg','image/png','image/webp'];

$successMessage = '';
$errorMessage = '';

// Ordner prüfen / erstellen
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

// POST-Aktionen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Löschen
    if ($action === 'delete_media') {
        $mediaId = (int)($_POST['media_id'] ?? 0);
        if ($mediaId > 0) {
            $stmt = $pdo->prepare("SELECT file_name FROM media_files WHERE id = ?");
            $stmt->execute([$mediaId]);
            $file = $stmt->fetch();
            if ($file) {
                $filePath = $uploadDir . $file['file_name'];
                if (is_file($filePath)) unlink($filePath);
                $deletedBy = $_SESSION['user_id'] ?? null;
                $stmtUpd = $pdo->prepare("UPDATE media_files SET deleted_at = NOW(), deleted_by = ? WHERE id = ?");
                $stmtUpd->execute([$deletedBy, $mediaId]);
                $successMessage = 'Bild wurde entfernt und als gelöscht markiert.';
            }
        }
    }

    // Upload
    elseif ($action === 'upload') {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            $errorMessage = 'Bitte wähle eine Bilddatei aus.';
        } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errorMessage = 'Fehler beim Hochladen.';
        } else {
            $file = $_FILES['image'];
            if ($file['size'] > $maxFileSize) $errorMessage = 'Maximal 20 MB erlaubt.';
            else {
                $originalName = $file['name'];
                $tmpPath = $file['tmp_name'];
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                if (!in_array($extension, $allowedExtensions, true)) $errorMessage = 'Dateityp nicht erlaubt.';
                else {
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->file($tmpPath);
                    if (!in_array($mimeType, $allowedMimeTypes, true)) $errorMessage = 'Datei ungültig.';
                    else {
                        $newFileName = bin2hex(random_bytes(16)) . '.' . $extension;
                        $targetPath = $uploadDir . $newFileName;
                        if (move_uploaded_file($tmpPath, $targetPath)) {
                            $userId = $_SESSION['user_id'] ?? null;
                            if (!$userId) $errorMessage = 'User nicht angemeldet.';
                            else {
                                $stmt = $pdo->prepare("
                                    INSERT INTO media_files
                                    (file_name, original_name, mime_type, file_size, created_by)
                                    VALUES (:file_name, :original_name, :mime_type, :file_size, :created_by)
                                ");
                                $success = $stmt->execute([
                                    ':file_name' => $newFileName,
                                    ':original_name' => $originalName,
                                    ':mime_type' => $mimeType,
                                    ':file_size' => $file['size'],
                                    ':created_by' => $userId
                                ]);
                                if ($success) $successMessage = 'Bild erfolgreich hochgeladen.';
                                else $errorMessage = 'Fehler beim Speichern in DB.';
                            }
                        } else $errorMessage = 'Datei konnte nicht gespeichert werden.';
                    }
                }
            }
        }
    }
}

// Bilder laden (nur nicht-gelöschte)
$stmt = $pdo->query("SELECT * FROM media_files WHERE deleted_at IS NULL ORDER BY created_at DESC");
$images = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Medien verwalten | damago Quizsystem</title>
<link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="manage-questions-page">

<?php include_once 'topbar.php'; ?>

<main class="play-layout">
<section class="quiz-main mq-panel">

<div class="mq-topbar">
    <div class="mq-topbar-info">
        <span class="eyebrow">Medienverwaltung</span>
        <h2>Hochgeladene Dateien</h2>
    </div>
    <div class="mq-topbar-actions">
        <a href="dashboard.php" class="back-button">← Zurück zum Dashboard</a>
    </div>
</div>

<?php if ($successMessage): ?><div class="import-message import-message-success"><?php echo e($successMessage); ?></div><?php endif; ?>
<?php if ($errorMessage): ?><div class="import-message import-message-error"><?php echo e($errorMessage); ?></div><?php endif; ?>

<div class="mq-toolbar">
    <form action="manage_media.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="upload">
        <label>Neue Datei:</label>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required>
        <button type="submit" class="btn-icon btn-add">Hochladen</button>
    </form>
</div>

<div class="question-list-header">
    <span class="col-frage">Dateiname</span>
    <span class="col-qaktion">Aktion</span>
</div>

<div class="question-list">
<?php if(empty($images)): ?>
    <div class="empty-list-hint">Keine Dateien vorhanden.</div>
<?php else: ?>
    <?php foreach($images as $img): ?>
        <div class="question-row">
            <div class="col-frage">
                <div><?php echo e($img['original_name']); ?></div>
                <div><?php echo e($img['mime_type']); ?> | <?php echo e(round($img['file_size']/1024,1)); ?> KB</div>
            </div>
            <div class="col-qaktion">
                <form method="POST" action="manage_media.php" class="inline-form">
                    <input type="hidden" name="action" value="delete_media">
                    <input type="hidden" name="media_id" value="<?php echo $img['id']; ?>">
                    <button type="submit" class="btn-icon btn-delete" title="Datei löschen">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                            <path d="M10 11v6M14 11v6"></path>
                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                        </svg>
                    </button>
                </form>
                <a href="<?php echo e($uploadUrl . $img['file_name']); ?>" target="_blank" class="btn-icon btn-view">Anzeigen</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

</section>
</main>

<?php include_once 'footbar.php'; ?>
</body>
</html>