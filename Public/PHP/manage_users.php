<?php
require_once 'init.php';
require_once 'db.php';

/** @var string $username */
/** @var string $role */
/** @var \PDO $pdo */

if ($role !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// ID des eingeloggten Admins ermitteln
$stmtAdmin = $pdo->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
$stmtAdmin->execute([':username' => $username]);
$adminUser  = $stmtAdmin->fetch();
$adminId    = $adminUser ? (int)$adminUser['id'] : null;

$message     = '';
$messageType = '';

// POST-Aktionen verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Status umschalten
    if ($action === 'toggle_status') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId > 0 && $userId !== $adminId) {
            $stmt = $pdo->prepare(
                "UPDATE users SET is_active = 1 - is_active, updated_by = :updatedBy WHERE id = :id"
            );
            $stmt->execute([':updatedBy' => $adminId, ':id' => $userId]);
            $message     = 'Status erfolgreich geändert.';
            $messageType = 'success';
        } elseif ($userId === $adminId) {
            $message     = 'Du kannst deinen eigenen Account nicht deaktivieren.';
            $messageType = 'error';
        }
    }

    // Benutzerdaten aktualisieren
    if ($action === 'update_user') {
        $userId      = (int)($_POST['user_id'] ?? 0);
        $newUsername = trim($_POST['new_username'] ?? '');
        $newEmail    = trim($_POST['new_email'] ?? '');
        $newRoleId   = (int)($_POST['role_id'] ?? 1);
        $newPassword = $_POST['new_password'] ?? '';

        if ($userId > 0 && $newUsername !== '' && $newEmail !== '') {
            // Benutzername-Duplikat prüfen
            $checkStmt = $pdo->prepare(
                "SELECT id FROM users WHERE username = :username AND id != :id LIMIT 1"
            );
            $checkStmt->execute([':username' => $newUsername, ':id' => $userId]);

            if ($checkStmt->fetch()) {
                $message     = 'Dieser Benutzername ist bereits vergeben.';
                $messageType = 'error';
            } else {
                if ($newPassword !== '') {
                    $hash = password_hash($newPassword, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare(
                        "UPDATE users SET username = :username, email = :email, role_id = :roleId,
                         password_hash = :hash, updated_by = :updatedBy WHERE id = :id"
                    );
                    $stmt->execute([
                        ':username'   => $newUsername,
                        ':email'      => $newEmail,
                        ':roleId'     => $newRoleId,
                        ':hash'       => $hash,
                        ':updatedBy'  => $adminId,
                        ':id'         => $userId
                    ]);
                } else {
                    $stmt = $pdo->prepare(
                        "UPDATE users SET username = :username, email = :email, role_id = :roleId,
                         updated_by = :updatedBy WHERE id = :id"
                    );
                    $stmt->execute([
                        ':username'  => $newUsername,
                        ':email'     => $newEmail,
                        ':roleId'    => $newRoleId,
                        ':updatedBy' => $adminId,
                        ':id'        => $userId
                    ]);
                }
                $message     = 'Benutzerdaten erfolgreich gespeichert.';
                $messageType = 'success';
            }
        } else {
            $message     = 'Bitte alle Pflichtfelder ausfüllen.';
            $messageType = 'error';
        }
    }

    // Neuen Benutzer anlegen
    if ($action === 'create_user') {
        $newUsername = trim($_POST['new_username'] ?? '');
        $newEmail    = trim($_POST['new_email'] ?? '');
        $newRoleId   = (int)($_POST['role_id'] ?? 1);
        $newPassword = $_POST['new_password'] ?? '';

        if ($newUsername !== '' && $newEmail !== '' && $newPassword !== '') {
            // Benutzername-Duplikat prüfen
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
            $checkStmt->execute([':username' => $newUsername]);
            if ($checkStmt->fetch()) {
                $message     = 'Dieser Benutzername ist bereits vergeben.';
                $messageType = 'error';
            } else {
                $hash = password_hash($newPassword, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare(
                    "INSERT INTO users (username, email, password_hash, role_id, is_active, created_by)
                     VALUES (:username, :email, :hash, :roleId, 1, :createdBy)"
                );
                $stmt->execute([
                    ':username'  => $newUsername,
                    ':email'     => $newEmail,
                    ':hash'      => $hash,
                    ':roleId'    => $newRoleId,
                    ':createdBy' => $adminId
                ]);
                $message     = 'Benutzer erfolgreich angelegt.';
                $messageType = 'success';
            }
        } else {
            $message     = 'Bitte alle Pflichtfelder ausfüllen (Passwort ist Pflicht).';
            $messageType = 'error';
        }
    }
}

// Alle Nutzer laden
$usersStmt = $pdo->query(
    "SELECT u.id, u.username, u.email, u.is_active, u.created_at, u.role_id,
            r.display_name AS role_name
     FROM users u
     LEFT JOIN roles r ON u.role_id = r.id
     ORDER BY u.created_at DESC"
);
$users = $usersStmt->fetchAll();

// Alle Rollen für Dropdown laden
$rolesStmt = $pdo->query("SELECT id, display_name FROM roles ORDER BY id");
$roles = $rolesStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzerverwaltung | damago Quizsystem</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="auth-page">

    <div class="page-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <?php include_once 'topbar.php'; ?>

    <main class="auth-layout dashboard-auth-layout manage-users-layout">

        <section class="dashboard-panel">
            <div class="archive-head">
                <span class="eyebrow">Benutzerverwaltung</span>
                <a href="admin_area.php" class="back-button">← Zurück zum Adminbereich</a>
            </div>

            <div class="auth-header">
                <h2>Alle Benutzer</h2>
                <p>Verwalte Benutzerkonten, weise Rollen zu und behalte den Überblick über alle registrierten Benutzer im System.</p>
            </div>

            <?php if ($message): ?>
                <div class="feedback-banner feedback-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="user-list-toolbar">
                <span class="user-count"><?php echo count($users); ?> Benutzer registriert</span>
                <button type="button" class="btn-icon btn-create" onclick="openCreateModal()">
                    + Benutzer anlegen
                </button>
            </div>

            <div class="user-list-header">
                <span class="col-benutzer">Benutzer</span>
                <span class="col-rolle">Rolle</span>
                <span class="col-aktion">Aktion</span>
            </div>

            <div class="user-list">
                <?php if (empty($users)): ?>
                    <div class="dashboard-action-card dashboard-action-card-empty">
                        <div class="dashboard-action-icon">NV</div>
                        <div>
                            <h3>Keine Benutzer gefunden</h3>
                            <p>Es sind noch keine Benutzer im System registriert.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <div class="user-row">
                            <div class="user-info col-benutzer">
                                <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
                                <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>

                            <div class="col-rolle">
                                <span class="badge badge-role">
                                    <?php echo htmlspecialchars($user['role_name'] ?? 'Unbekannt'); ?>
                                </span>
                            </div>

                            <div class="col-aktion">
                                <!-- Status umschalten -->
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit"
                                        class="btn-icon <?php echo $user['is_active'] ? 'btn-toggle-active' : 'btn-toggle-inactive'; ?>"
                                        <?php echo ($user['id'] === $adminId) ? 'disabled title="Eigener Account"' : ''; ?>>
                                        <?php echo $user['is_active'] ? '● Aktiv' : '○ Inaktiv'; ?>
                                    </button>
                                </form>

                                <!-- Bearbeiten -->
                                <button type="button"
                                    class="btn-icon btn-edit"
                                    onclick="openEditModal(
                                        <?php echo $user['id']; ?>,
                                        '<?php echo addslashes(htmlspecialchars($user['username'])); ?>',
                                        '<?php echo addslashes(htmlspecialchars($user['email'])); ?>',
                                        <?php echo $user['role_id']; ?>
                                    )">
                                    Bearbeiten
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($users)): ?>
                <div class="mq-pagination" id="muPagination">
                    <div class="mq-pagination-info">
                        <span id="muRangeInfo"></span>
                    </div>
                    <div class="mq-pagination-controls">
                        <label class="mq-perpage">
                            Pro Seite
                            <select id="muPerPage">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </label>
                        <div class="mq-pager">
                            <button type="button" class="btn-icon mq-pager-btn" id="muPrev" aria-label="Vorherige Seite">‹</button>
                            <span class="mq-pager-pages" id="muPages"></span>
                            <button type="button" class="btn-icon mq-pager-btn" id="muNext" aria-label="Nächste Seite">›</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </section>

    </main>

    <!-- Edit-Modal -->
    <div class="modal-overlay" id="editModal">
        <div class="modal">
            <div class="modal-title">Nutzer bearbeiten</div>
            <div class="modal-subtitle" id="modalSubtitle">Daten anpassen</div>

            <form method="POST">
                <input type="hidden" name="action" value="update_user">
                <input type="hidden" name="user_id" id="editUserId">

                <div class="modal-field">
                    <label>Benutzername</label>
                    <input type="text" name="new_username" id="editUsername" required>
                </div>

                <div class="modal-field">
                    <label>E-Mail-Adresse</label>
                    <input type="email" name="new_email" id="editEmail" required>
                </div>

                <div class="modal-field">
                    <label>Rolle</label>
                    <select name="role_id" id="editRoleId">
                        <?php foreach ($roles as $r): ?>
                            <option value="<?php echo $r['id']; ?>">
                                <?php echo htmlspecialchars($r['display_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="modal-field">
                    <label>Neues Passwort</label>
                    <input type="password" name="new_password" id="editPassword" placeholder="Leer lassen = unverändert">
                    <span class="modal-hint">Nur ausfüllen, wenn das Passwort geändert werden soll.</span>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeEditModal()">Abbrechen</button>
                    <button type="submit" class="btn-icon btn-edit">Speichern</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, username, email, roleId) {
            document.getElementById('editUserId').value   = id;
            document.getElementById('editUsername').value = username;
            document.getElementById('editEmail').value    = email;
            document.getElementById('editRoleId').value   = roleId;
            document.getElementById('editPassword').value = '';
            document.getElementById('modalSubtitle').textContent = 'Benutzer: ' + username;
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // Modal schließen bei Klick auf Overlay
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });

        function openCreateModal() {
            document.getElementById('createModal').classList.add('active');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.remove('active');
        }

        document.getElementById('createModal').addEventListener('click', function(e) {
            if (e.target === this) closeCreateModal();
        });

        // Pagination der Benutzerliste (10 / 25 / 50 pro Seite)
        (function () {
            const rows = Array.from(document.querySelectorAll('.user-list .user-row'));
            const bar  = document.getElementById('muPagination');
            if (!rows.length || !bar) { if (bar) bar.style.display = 'none'; return; }

            const perPageSel = document.getElementById('muPerPage');
            const rangeInfo  = document.getElementById('muRangeInfo');
            const pagesBox   = document.getElementById('muPages');
            const prevBtn    = document.getElementById('muPrev');
            const nextBtn    = document.getElementById('muNext');

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

    <!-- Create-Modal -->
    <div class="modal-overlay" id="createModal">
        <div class="modal">
            <div class="modal-title">Benutzer anlegen</div>
            <div class="modal-subtitle">Neues Benutzerkonto erstellen</div>

            <form method="POST">
                <input type="hidden" name="action" value="create_user">

                <div class="modal-field">
                    <label>Benutzername</label>
                    <input type="text" name="new_username" required>
                </div>

                <div class="modal-field">
                    <label>E-Mail-Adresse</label>
                    <input type="email" name="new_email" required>
                </div>

                <div class="modal-field">
                    <label>Rolle</label>
                    <select name="role_id">
                        <?php foreach ($roles as $r): ?>
                            <option value="<?php echo $r['id']; ?>">
                                <?php echo htmlspecialchars($r['display_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="modal-field">
                    <label>Passwort</label>
                    <input type="password" name="new_password" required>
                    <span class="modal-hint">Wird verschlüsselt gespeichert.</span>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeCreateModal()">Abbrechen</button>
                    <button type="submit" class="btn-icon btn-edit">Anlegen</button>
                </div>
            </form>
        </div>
    </div>

    <?php include_once 'footbar.php'; ?>

</body>
</html>
