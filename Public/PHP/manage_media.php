<?php
require_once 'init.php';

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function formatFileSize($bytes)
{
    $bytes = (int) $bytes;

    if ($bytes >= 1024 * 1024) {
        return round($bytes / 1024 / 1024, 2) . ' MB';
    }

    if ($bytes >= 1024) {
        return round($bytes / 1024, 1) . ' KB';
    }

    return $bytes . ' B';
}

$currentRole = $role ?? ($_SESSION['role'] ?? ($_SESSION['user']['role'] ?? 'guest'));

if (!in_array($currentRole, ['admin', 'teacher'])) {
    header("Location: dashboard.php");
    exit;
}

$uploadDir = __DIR__ . '/../uploads/questions/';
$uploadUrl = '../uploads/questions/';
$maxFileSize = 20 * 1024 * 1024;

$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
$allowedMimeTypes = [
    'image/jpeg',
    'image/png',
    'image/webp'
];

$successMessage = '';
$errorMessage = '';

/*
    Später durch Datenbank ersetzen:
    SELECT id, name FROM question_pools WHERE is_active = 1 ORDER BY name ASC
*/
$questionPools = [];

/*
    Solange die Datenbank noch nicht angebunden ist, bekommen Dateien
    automatisch den Pool 0 = Noch nicht zugeordnet.
*/
$unassignedPoolId = 0;
$unassignedPoolName = 'Noch nicht zugeordnet';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

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
                        $successMessage = 'Das Bild wurde erfolgreich hochgeladen.';
                    } else {
                        $errorMessage = 'Das Bild konnte nicht gespeichert werden.';
                    }
                }
            }
        }
    }
}

$images = [];

if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $filePath = $uploadDir . $file;

        if (in_array($extension, $allowedExtensions, true) && is_file($filePath)) {
            $images[] = [
                'file_name' => $file,
                'extension' => strtoupper($extension),
                'file_size' => filesize($filePath),
                'pool_id' => $unassignedPoolId,
                'pool_name' => $unassignedPoolName,
                'uploaded_at' => filemtime($filePath)
            ];
        }
    }

    usort($images, function ($a, $b) {
        return $b['uploaded_at'] <=> $a['uploaded_at'];
    });
}

$totalImages = count($images);
$totalSize = array_sum(array_map(fn($img) => (int) $img['file_size'], $images));

$backTarget = $currentRole === 'admin' ? 'admin_area.php' : 'teacher_area.php';
$backLabel = $currentRole === 'admin' ? 'Adminbereich' : 'Dozentenbereich';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medien verwalten | damago Quizsystem</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>

<body class="manage-questions-page media-page">

<div class="page-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>

<?php include_once 'topbar.php'; ?>

<main class="play-layout">

    <section class="ranking-panel">

        <div class="ranking-header">
            <span class="eyebrow">Medien-Verwaltung</span>
            <h2>Medien verwalten</h2>
            <p>
                Lade Bilder hoch und ordne sie später einem Fragenpool zu. Beim Erstellen einer neuen Frage
                sollen nur passende Bilder angezeigt werden.
            </p>
        </div>

        <?php if (!empty($successMessage)): ?>
            <div class="import-section import-message import-message-success">
                <span class="import-message-text">
                    <?php echo e($successMessage); ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div class="import-section import-message import-message-error">
                <span class="import-message-text">
                    <?php echo e($errorMessage); ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="lobby-meta mq-meta">
            <div>
                <span>Bilder</span>
                <strong><?php echo $totalImages; ?> vorhanden</strong>
            </div>

            <div>
                <span>Speicher</span>
                <strong><?php echo e(formatFileSize($totalSize)); ?></strong>
            </div>
        </div>

        <div class="import-section">

            <div class="import-header">
                <div>
                    <h3>Bild hochladen</h3>
                    <p>
                        Wähle einen Fragenpool und lade ein Bild hoch. Die echte Pool-Zuordnung
                        wird später über die Datenbank ergänzt.
                    </p>
                </div>
            </div>

            <form action="manage_media.php" method="POST" enctype="multipart/form-data" class="questions-form">

                <div class="form-group">
                    <label for="questionPool">Fragenpool</label>
                    <select id="questionPool" name="question_pool_id">
                        <option value="">Fragenpool später auswählen</option>

                        <?php foreach ($questionPools as $pool): ?>
                            <option value="<?php echo (int) $pool['id']; ?>">
                                <?php echo e($pool['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="import-drop-zone">
                    <input type="file" name="image" id="imageFile" accept=".jpg,.jpeg,.png,.webp" hidden required>

                    <div class="import-drop-content">
                        <div class="import-drop-icon">IMG</div>
                        <div>
                            <p>Bilddatei auswählen oder später per Drag & Drop erweitern</p>
                            <span class="import-drop-hint">
                                Erlaubt: jpg, jpeg, png, webp · maximal 20 MB
                            </span>
                        </div>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="selectImageButton">
                        Datei auswählen
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Bild hochladen
                    </button>
                </div>

            </form>

        </div>

        <div class="dashboard-footer-links">
            <a href="<?php echo e($backTarget); ?>">
                ← Zurück zum <?php echo e($backLabel); ?>
            </a>
        </div>

    </section>

    <aside class="ranking-panel">

        <div class="ranking-header">
            <span class="eyebrow">Bilder</span>
            <h2>Hochgeladene Bilder</h2>
            <p>
                Wähle einen Fragenpool aus und bestimme, wie viele Bilder pro Seite angezeigt werden.
            </p>
        </div>

        <div class="mq-toolbar">
            <form class="form-group pool-filter-form mq-filter" onsubmit="return false;">
                <label for="mediaPoolFilter">Fragenpool</label>
                <select id="mediaPoolFilter">
                    <option value="">Alle Fragenpools</option>
                    <option value="0">Noch nicht zugeordnet</option>

                    <?php foreach ($questionPools as $pool): ?>
                        <option value="<?php echo (int) $pool['id']; ?>">
                            <?php echo e($pool['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <form class="form-group pool-filter-form mq-filter" onsubmit="return false;">
                <label for="mediaPerPage">Anzeige</label>
                <select id="mediaPerPage">
                    <option value="10" selected>10 Bilder</option>
                    <option value="20">20 Bilder</option>
                    <option value="30">30 Bilder</option>
                </select>
            </form>
        </div>

        <div class="question-list-header">
            <span class="col-frage">Bild</span>
            <span class="col-qaktion">Aktion</span>
        </div>

        <div class="question-list" id="mediaList">

            <?php if (empty($images)): ?>

                <div class="empty-list-hint" id="mediaEmptyHint">
                    Keine Bilder vorhanden.
                </div>

            <?php else: ?>

                <?php foreach ($images as $image): ?>
                    <div
                        class="question-row media-row"
                        data-pool="<?php echo (int) $image['pool_id']; ?>"
                        data-tooltip="<?php echo e($image['file_name']); ?>"
                    >

                        <div class="col-frage">
                            <img
                                src="<?php echo e($uploadUrl . $image['file_name']); ?>"
                                alt="Hochgeladenes Bild"
                                class="mq-row-thumb"
                            >

                            <div class="mq-frage-texts">
                                <div class="question-text">
                                    <?php echo e($image['file_name']); ?>
                                </div>

                                <div class="mq-dept-tags">
                                    <span class="mq-dept-tag is-empty">
                                        <?php echo e($image['pool_name']); ?>
                                    </span>
                                </div>

                                <div class="question-pool-label">
                                    <?php echo e($image['extension']); ?> · <?php echo e(formatFileSize($image['file_size'])); ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-qaktion">
                            <a
                                href="<?php echo e($uploadUrl . $image['file_name']); ?>"
                                target="_blank"
                                class="btn-icon btn-edit"
                            >
                                Anzeigen
                            </a>

                            <button type="button" class="btn-icon btn-edit" title="Später Pool-Zuordnung bearbeiten">
                                Bearbeiten
                            </button>

                            <button type="button" class="btn-icon btn-delete" title="Später Bild löschen">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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

        <?php if (!empty($images)): ?>
            <div class="mq-pagination" id="mediaPagination">
                <div class="mq-pagination-info">
                    <span id="mediaRangeInfo"></span>
                </div>

                <div class="mq-pagination-controls">
                    <div class="mq-pager">
                        <button type="button" class="btn-icon mq-pager-btn" id="mediaPrev" aria-label="Vorherige Seite">‹</button>
                        <span class="mq-pager-pages" id="mediaPages"></span>
                        <button type="button" class="btn-icon mq-pager-btn" id="mediaNext" aria-label="Nächste Seite">›</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </aside>

</main>

<script>
    const imageDropZone = document.querySelector('.import-drop-zone');
    const imageFileInput = document.getElementById('imageFile');
    const imageDropContent = document.querySelector('.import-drop-content');
    const selectImageButton = document.getElementById('selectImageButton');

    if (imageDropZone && imageFileInput && imageDropContent) {
        imageDropZone.addEventListener('click', function () {
            imageFileInput.click();
        });

        if (selectImageButton) {
            selectImageButton.addEventListener('click', function () {
                imageFileInput.click();
            });
        }

        imageFileInput.addEventListener('change', function () {
            const file = imageFileInput.files[0];

            if (!file) {
                return;
            }

            imageDropContent.innerHTML = `
                <div class="import-drop-icon">IMG</div>
                <div>
                    <p><strong>${file.name}</strong></p>
                    <span class="import-drop-hint">${(file.size / 1024).toFixed(1)} KB · bereit zum Hochladen</span>
                </div>
            `;

            imageDropZone.classList.add('has-file');
        });
    }

    (function () {
        const mediaList = document.getElementById('mediaList');
        if (!mediaList) return;

        const rows = Array.from(mediaList.querySelectorAll('.media-row'));
        const poolFilter = document.getElementById('mediaPoolFilter');
        const perPageSelect = document.getElementById('mediaPerPage');
        const pagination = document.getElementById('mediaPagination');
        const rangeInfo = document.getElementById('mediaRangeInfo');
        const pagesBox = document.getElementById('mediaPages');
        const prevBtn = document.getElementById('mediaPrev');
        const nextBtn = document.getElementById('mediaNext');

        if (!rows.length) {
            if (pagination) pagination.style.display = 'none';
            return;
        }

        let currentPage = 1;

        function getFilteredRows() {
            const selectedPool = poolFilter ? poolFilter.value : '';

            return rows.filter(row => {
                if (selectedPool === '') {
                    return true;
                }

                return row.dataset.pool === selectedPool;
            });
        }

        function getPerPage() {
            return parseInt(perPageSelect.value, 10) || 10;
        }

        function getTotalPages(filteredRows) {
            return Math.max(1, Math.ceil(filteredRows.length / getPerPage()));
        }

        function createPageList(total, current) {
            const out = [];

            for (let i = 1; i <= total; i++) {
                if (i === 1 || i === total || (i >= current - 1 && i <= current + 1)) {
                    out.push(i);
                } else if (out[out.length - 1] !== '…') {
                    out.push('…');
                }
            }

            return out;
        }

        function renderMediaList() {
            const filteredRows = getFilteredRows();
            const perPage = getPerPage();
            const totalPages = getTotalPages(filteredRows);

            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            rows.forEach(row => {
                row.style.display = 'none';
            });

            const start = (currentPage - 1) * perPage;
            const end = start + perPage;
            const visibleRows = filteredRows.slice(start, end);

            visibleRows.forEach(row => {
                row.style.display = '';
            });

            if (rangeInfo) {
                if (filteredRows.length === 0) {
                    rangeInfo.textContent = '0 Bilder gefunden';
                } else {
                    rangeInfo.textContent = (start + 1) + '–' + Math.min(end, filteredRows.length) + ' von ' + filteredRows.length;
                }
            }

            if (pagesBox) {
                pagesBox.innerHTML = '';

                createPageList(totalPages, currentPage).forEach(page => {
                    if (page === '…') {
                        const span = document.createElement('span');
                        span.className = 'mq-pager-ellipsis';
                        span.textContent = '…';
                        pagesBox.appendChild(span);
                    } else {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'btn-icon mq-pager-btn mq-page-num' + (page === currentPage ? ' is-active' : '');
                        button.textContent = page;
                        button.addEventListener('click', function () {
                            currentPage = page;
                            renderMediaList();
                        });
                        pagesBox.appendChild(button);
                    }
                });
            }

            if (prevBtn) {
                prevBtn.disabled = currentPage <= 1;
            }

            if (nextBtn) {
                nextBtn.disabled = currentPage >= totalPages;
            }

            if (pagination) {
                pagination.style.display = filteredRows.length > 0 ? '' : 'none';
            }
        }

        if (poolFilter) {
            poolFilter.addEventListener('change', function () {
                currentPage = 1;
                renderMediaList();
            });
        }

        if (perPageSelect) {
            perPageSelect.addEventListener('change', function () {
                currentPage = 1;
                renderMediaList();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                if (currentPage > 1) {
                    currentPage--;
                    renderMediaList();
                }
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                const totalPages = getTotalPages(getFilteredRows());

                if (currentPage < totalPages) {
                    currentPage++;
                    renderMediaList();
                }
            });
        }

        renderMediaList();
    })();
</script>

</body>
</html>