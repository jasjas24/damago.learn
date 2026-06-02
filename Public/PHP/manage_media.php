<?php
require_once 'init.php';
require_once 'db.php'; // PDO-Verbindung

function e($value) {
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

// Fragenpools laden
$stmtPools = $pdo->query("SELECT id, name FROM question_pools WHERE is_active = 1 ORDER BY name ASC");
$pools = $stmtPools->fetchAll(PDO::FETCH_ASSOC);

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
        $poolId = (int)($_POST['question_pool_id'] ?? 0);
        if ($poolId <= 0) {
            $errorMessage = 'Bitte wähle einen Fragenpool aus.';
        } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
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
                                    (file_name, original_name, mime_type, file_size, created_by, question_pool_id)
                                    VALUES (:file_name, :original_name, :mime_type, :file_size, :created_by, :pool_id)
                                ");
                                $success = $stmt->execute([
                                    ':file_name' => $newFileName,
                                    ':original_name' => $originalName,
                                    ':mime_type' => $mimeType,
                                    ':file_size' => $file['size'],
                                    ':created_by' => $userId,
                                    ':pool_id' => $poolId
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
<title>Medienverwaltung | damago Quizsystem</title>
<link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="manage-questions-page">

<?php include_once 'topbar.php'; ?>

<main class="play-layout">
<section class="quiz-main">

<div class="mq-topbar">
    <div class="mq-topbar-info">
        <span class="eyebrow">Medienverwaltung</span>
        <h2>Hochgeladene Dateien</h2>
    </div>
    <div class="mq-topbar-actions mq-topbar-actions-stacked">
        <a href="dashboard.php" class="back-button">← Zurück zum Dashboard</a>
    </div>
</div>

<?php if ($successMessage): ?>
<div class="alert-success"><?php echo e($successMessage); ?></div>
<?php endif; ?>
<?php if ($errorMessage): ?>
<div class="alert-error"><?php echo e($errorMessage); ?></div>
<?php endif; ?>

<div class="mq-toolbar">
    <form action="manage_media.php" method="POST" enctype="multipart/form-data" class="questions-form">
        <input type="hidden" name="action" value="upload">
        <div class="form-group">
            <label>Fragenpool auswählen *</label>
            <select name="question_pool_id" required>
                <option value="">Bitte auswählen</option>
                <?php foreach($pools as $pool): ?>
                <option value="<?php echo $pool['id']; ?>"><?php echo e($pool['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Neue Datei:</label>
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required>
        </div>
        <button type="submit" class="btn btn-primary">Hochladen</button>
    </form>
</div>

<div class="question-list">
<?php if(empty($images)): ?>
    <div class="empty-list-hint">Keine Dateien vorhanden.</div>
<?php else: ?>
    <?php foreach($images as $img): ?>
    <div class="question-card">
        <div class="col-frage">
            <div><?php echo e($img['original_name']); ?></div>
            <div><?php echo e($img['mime_type']); ?> | <?php echo e(round($img['file_size']/1024,1)); ?> KB</div>
        </div>
        <div class="col-qaktion mq-action-row">
            <form method="POST" action="manage_media.php">
                <input type="hidden" name="action" value="delete_media">
                <input type="hidden" name="media_id" value="<?php echo $img['id']; ?>">
                <button type="submit" class="btn-icon btn-toggle-inactive" title="Datei löschen">🗑</button>
            </form>
            <a href="<?php echo e($uploadUrl . $img['file_name']); ?>" target="_blank" class="btn-icon btn-edit">Anzeigen</a>
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