<?php
require_once 'init.php';
require_once 'db.php'; // PDO-Verbindung

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Rolle prüfen
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

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Bild hochladen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        $errorMessage = 'Bitte wähle eine Bilddatei aus.';
    } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errorMessage = 'Beim Hochladen ist ein Fehler aufgetreten.';
    } else {
        $file = $_FILES['image'];

        if ($file['size'] > $maxFileSize) {
            $errorMessage = 'Die Datei ist zu groß. Maximal erlaubt sind 20 MB.';
        } else {
            $originalName = $file['name'];
            $tmpPath = $file['tmp_name'];
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            if (!in_array($extension, $allowedExtensions, true)) {
                $errorMessage = 'Dieser Dateityp ist nicht erlaubt. Erlaubt sind jpg, jpeg, png und webp.';
            } else {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($tmpPath);

                if (!in_array($mimeType, $allowedMimeTypes, true)) {
                    $errorMessage = 'Die Datei wurde nicht als gültiges Bild erkannt.';
                } else {
                    $newFileName = bin2hex(random_bytes(16)) . '.' . $extension;
                    $targetPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($tmpPath, $targetPath)) {
                        // DB-Eintrag
                        $userId = $_SESSION['user_id'] ?? null; // int für created_by
                        if (!$userId) {
                            $errorMessage = 'Fehler: User nicht angemeldet.';
                        } else {
                            $stmt = $pdo->prepare("
                                INSERT INTO media_files
                                (file_name, original_name, mime_type, file_size, created_by)
                                VALUES (:file_name, :original_name, :mime_type, :file_size, :created_by)
                            ");
                            $success = $stmt->execute([
                                ':file_name' => $newFileName,
                                ':original_name' => $originalName,
                                ':mime_type' => $extension,
                                ':file_size' => $file['size'],
                                ':created_by' => $userId
                            ]);

                            if ($success) {
                                $successMessage = 'Das Bild wurde erfolgreich hochgeladen und in der DB gespeichert.';
                            } else {
                                $error = $stmt->errorInfo();
                                $errorMessage = 'Fehler beim Speichern in der DB: ' . $error[2];
                            }
                        }
                    } else {
                        $errorMessage = 'Das Bild konnte nicht gespeichert werden.';
                    }
                }
            }
        }
    }
}

// Bilder aus DB laden
$stmt = $pdo->query("SELECT * FROM media_files ORDER BY created_at DESC");
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
<body class="auth-page">

    <div class="page-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <?php include_once 'topbar.php'; ?>

    <main class="auth-layout dashboard-auth-layout">

        <section class="auth-info">
            <h1>Medien verwalten</h1>
            <p>
                Lade Bilder für Fragen hoch und verwalte vorhandene Medien.
                Erlaubt sind jpg, jpeg, png und webp bis maximal 20 MB.
            </p>
            <div class="info-list">
                <div>Upload nur für Administratoren</div>
                <div>Speicherort: /Public/Uploads/Questions/</div>
                <div>Originaldateinamen werden nicht übernommen</div>
            </div>
            <div class="dashboard-footer-links">
                <a href="dashboard.php">← Zurück zum Dashboard</a>
            </div>
        </section>

        <section class="dashboard-panel">

            <div class="auth-header">
                <span class="eyebrow">Bilder</span>
                <h2>Bild hochladen</h2>
                <p>Wähle ein Bild aus, das später einer Quizfrage zugeordnet werden kann.</p>
            </div>

            <?php if (!empty($successMessage)): ?>
                <div class="statistics-card">
                    <div class="stat-row">
                        <div class="stat-title">Erfolg</div>
                        <div class="stat-values">
                            <span><?php echo e($successMessage); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
                <div class="statistics-card">
                    <div class="stat-row">
                        <div class="stat-title">Fehler</div>
                        <div class="stat-values">
                            <span><?php echo e($errorMessage); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="statistics-card">
                <form action="manage_media.php" method="POST" enctype="multipart/form-data">
                    <div class="stat-row">
                        <div class="stat-title">Neue Datei</div>
                        <div class="stat-values">
                            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required>
                        </div>
                    </div>
                    <div class="dashboard-footer-links">
<<<<<<< HEAD
                        <button type="submit" class="btn btn-primary">Bild hochladen</button>
=======
                        <button type="submit" class="btn btn-primary">
                            Bild hochladen
                        </button>
>>>>>>> 5997dead2cec9af8d9bdbba6d37131c830cdd83d
                    </div>
                </form>
            </div>

            <div class="auth-header">
                <h2>Hochgeladene Bilder</h2>
                <p>Diese Bilder befinden sich aktuell im Medienordner.</p>
            </div>

            <?php if (empty($images)): ?>
                <div class="statistics-card">
                    <div class="stat-row">
                        <div class="stat-title">Keine Bilder vorhanden</div>
                        <div class="stat-values">
                            <span>Es wurden bisher keine Bilder hochgeladen.</span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="statistics-card">
                    <?php foreach ($images as $image): ?>
                        <div class="stat-row">
                            <div class="stat-title">
                                <img src="<?php echo e($uploadUrl . $image['file_name']); ?>" alt="Hochgeladenes Bild" class="media-thumb">
                            </div>
                            <div class="stat-values">
                                <span><?php echo e($image['original_name']); ?></span>
                                <span><?php echo e(strtoupper($image['mime_type'])); ?></span>
                                <span><a href="<?php echo e($uploadUrl . $image['file_name']); ?>" target="_blank">Anzeigen</a></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </section>

    </main>

    <?php include_once 'footbar.php'; ?>

</body>
</html>