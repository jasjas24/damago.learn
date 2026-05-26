<?php
require_once 'init.php';

$userId = null;

if (isset($_SESSION['user_id'])) {
    $userId = (int) $_SESSION['user_id'];
} elseif (isset($_SESSION['user']['id'])) {
    $userId = (int) $_SESSION['user']['id'];
}

$currentUsername = $username ?? ($_SESSION['username'] ?? ($_SESSION['user']['username'] ?? 'Benutzer'));
$currentRole = $role ?? ($_SESSION['role'] ?? ($_SESSION['user']['role'] ?? 'guest'));

if ($userId <= 0 || $currentRole === 'guest') {
    $loginTarget = file_exists(__DIR__ . '/login.php') ? 'login.php' : '../login.html';
    header("Location: " . $loginTarget);
    exit;
}

$statistics = [];
$databaseNotice = '';

if (isset($pdo) && $pdo instanceof PDO) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                topic,
                correct_answers,
                final_score,
                reached_place,
                total_players,
                played_at
            FROM game_history
            WHERE user_id = :user_id
            ORDER BY played_at DESC
        ");

        $stmt->execute([
            ':user_id' => $userId
        ]);

        $statistics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $databaseNotice = 'Die Datenbanktabelle für die Spielhistorie ist noch nicht vollständig eingerichtet.';
    }
} else {
    $databaseNotice = 'Die Datenbankverbindung ist noch nicht aktiv.';
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function formatDateTime($dateTime)
{
    if (empty($dateTime)) {
        return '-';
    }

    $timestamp = strtotime($dateTime);

    if ($timestamp === false) {
        return e($dateTime);
    }

    return date('d.m.Y H:i', $timestamp);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lernfortschritt | damago Quizsystem</title>
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
            <h1>Lernfortschritt</h1>

            <p>
                Hier siehst du deine persönliche Historie aller dauerhaft gespeicherten Quizrunden.
            </p>

            <div class="info-list">
                <div>
                    Angemeldet als: <?php echo e($currentUsername); ?>
                </div>
                <div>
                    Sichtbarkeit: Nur deine eigenen Ergebnisse
                </div>
                <div>
                    Gäste: Keine dauerhafte persönliche Historie
                </div>
            </div>

            <div class="dashboard-footer-links">
                <a href="dashboard.php">← Zurück zum Dashboard</a>
            </div>
        </section>

        <section class="dashboard-panel">

            <div class="auth-header">
                <span class="eyebrow">Historie</span>
                <h2>Deine gespielten Quizrunden</h2>
                <p>
                    Gespeichert werden Thema, richtige Antworten, Punktzahl, Platzierung,
                    Teilnehmeranzahl und Datum/Uhrzeit.
                </p>
            </div>

            <?php if (!empty($databaseNotice)): ?>
                <div class="statistics-card">
                    <div class="stat-row">
                        <div class="stat-title">Hinweis</div>
                        <div class="stat-values">
                            <span><?php echo e($databaseNotice); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($statistics)): ?>

                <div class="statistics-card">
                    <div class="stat-row">
                        <div class="stat-title">Noch keine Historie vorhanden</div>
                        <div class="stat-values">
                            <span>Du hast bisher noch kein dauerhaft gespeichertes Quiz abgeschlossen.</span>
                        </div>
                    </div>
                </div>

            <?php else: ?>

                <div class="statistics-card">

                    <?php foreach ($statistics as $stat): ?>

                        <div class="stat-row">
                            <div class="stat-title">
                                <?php echo e($stat['topic']); ?>
                            </div>

                            <div class="stat-values">
                                <span>Richtig: <?php echo e($stat['correct_answers']); ?></span>
                                <span>Punkte: <?php echo e($stat['final_score']); ?></span>
                                <span>Platz: <?php echo e($stat['reached_place']); ?></span>
                                <span>Teilnehmer: <?php echo e($stat['total_players']); ?></span>
                                <span><?php echo formatDateTime($stat['played_at']); ?></span>
                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </main>

</body>
</html>