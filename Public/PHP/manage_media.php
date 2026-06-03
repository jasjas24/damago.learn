<?php
require_once 'init.php';
require_once 'db.php'; // PDO-Verbindung

// Kurzes Kürzel, um Text sicher auszugeben (HTML-Sonderzeichen werden escaped).
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
    csrf_check();
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

// Bilder laden (nur nicht-gelöschte) inkl. zugehörigem Fragenpool-Namen
$stmt = $pdo->query("
    SELECT m.*, p.name AS pool_name
    FROM media_files m
    LEFT JOIN question_pools p ON p.id = m.question_pool_id
    WHERE m.deleted_at IS NULL
    ORDER BY m.created_at DESC
");
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
<body class="manage-questions-page media-page">

<?php include_once 'topbar.php'; ?>

<main class="play-layout">
<section class="quiz-main">

<div class="mq-topbar">
    <div class="mq-topbar-info">
        <span class="eyebrow">Medienverwaltung</span>
        <h2>Hochgeladene Dateien</h2>
        <p>Hier kannst du Bilder für das Quizsystem hochladen und verwalten. Wähle beim Upload einen Fragenpool aus, um die Datei diesem Pool zuzuordnen.</p>
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
        <?php echo csrf_field(); ?>
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
            <label>Neue Datei (JPG, JPEG, PNG, WebP · max. 20 MB):</label>
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required>
        </div>
        <button type="submit" class="btn btn-primary">Hochladen</button>
    </form>
</div>

<?php if(empty($images)): ?>
    <div class="empty-list-hint">Keine Dateien vorhanden.</div>
<?php else: ?>
    <div class="media-list">
        <div class="media-list-head">
            <span>Dateiname</span>
            <span>Fachbereich</span>
            <span class="media-head-action">Aktion</span>
        </div>
        <?php foreach($images as $img): ?>
            <?php $fileUrl = e($uploadUrl . $img['file_name']); ?>
            <div class="media-row">
                <span class="media-row-name" title="<?php echo e($img['original_name']); ?>"><?php echo e($img['original_name']); ?></span>
                <span class="media-row-pool"><?php echo $img['pool_name'] ? e($img['pool_name']) : '— kein Pool —'; ?></span>
                <div class="media-row-actions">
                    <button type="button" class="btn-icon btn-edit"
                            onclick="openMediaModal(<?php echo htmlspecialchars(json_encode($uploadUrl . $img['file_name']), ENT_QUOTES); ?>, <?php echo htmlspecialchars(json_encode($img['original_name']), ENT_QUOTES); ?>)">
                        Anzeigen
                    </button>
                    <form method="POST" action="manage_media.php" onsubmit="return confirm('Datei wirklich löschen?');">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="action" value="delete_media">
                        <input type="hidden" name="media_id" value="<?php echo $img['id']; ?>">
                        <button type="submit" class="btn-icon btn-delete" aria-label="Datei löschen" title="Datei löschen">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                <path d="M10 11v6M14 11v6"></path>
                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

</section>
</main>

<!-- Vorschau-Modal für "Anzeigen" -->
<div id="mediaModal" class="media-modal" hidden>
    <div class="media-modal-backdrop" onclick="closeMediaModal()"></div>
    <div class="media-modal-content">
        <button type="button" class="media-modal-close" onclick="closeMediaModal()" aria-label="Schließen">×</button>
        <img id="mediaModalImg" src="" alt="" class="media-modal-img">
        <div id="mediaModalCaption" class="media-modal-caption"></div>
    </div>
</div>

<script>
    // Öffnet die Bildvorschau im Modal mit dem gewählten Bild.
    function openMediaModal(url, name) {
        document.getElementById('mediaModalImg').src = url;
        document.getElementById('mediaModalImg').alt = name || '';
        document.getElementById('mediaModalCaption').textContent = name || '';
        document.getElementById('mediaModal').hidden = false;
    }
    // Schließt die Bildvorschau wieder.
    function closeMediaModal() {
        const modal = document.getElementById('mediaModal');
        modal.hidden = true;
        document.getElementById('mediaModalImg').src = '';
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMediaModal();
    });
</script>

<?php include_once 'footbar.php'; ?>
</body>
</html>