<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Fragen verwalten</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../CSS/style.css">
</head>

<body class="quiz-play-page">

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

        <div class="import-section">
            <div class="import-header">
                <div>
                    <span class="eyebrow">Import</span>
                    <h3>Fragen importieren</h3>
                    <p>XLSX-Datei hochladen und mehrere Fragen auf einmal importieren.</p>
                </div>
                <a href="../../Uploads/Vorlagen/fragen_import_vorlage.xlsx" class="btn-icon btn-edit import-download-btn" download>
                    Vorlage herunterladen
                </a>
            </div>

            <form method="POST" action="#" enctype="multipart/form-data" class="import-form">
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

            <form method="POST" action="#" class="auth-form questions-form">

                <div class="form-group">
                    <label for="question_pool">Fragenpool auswählen</label>
                    <select id="question_pool" name="question_pool">
                        <option value="">Bitte auswählen</option>
                        <option value="1">PHP Grundlagen</option>
                        <option value="2">HTML und CSS</option>
                        <option value="3">JavaScript Grundlagen</option>
                        <option value="4">Datenbanken</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="question_text">Frage</label>
                    <input
                        type="text"
                        id="question_text"
                        name="question_text"
                        placeholder="z. B. Was macht die Funktion htmlspecialchars()?"
                    >
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
                        <li>
                            <label>
                                <input type="checkbox" name="correct_answers[]" value="A">
                                Antwort A ist richtig
                            </label>
                        </li>

                        <li>
                            <label>
                                <input type="checkbox" name="correct_answers[]" value="B">
                                Antwort B ist richtig
                            </label>
                        </li>

                        <li>
                            <label>
                                <input type="checkbox" name="correct_answers[]" value="C">
                                Antwort C ist richtig
                            </label>
                        </li>

                        <li>
                            <label>
                                <input type="checkbox" name="correct_answers[]" value="D">
                                Antwort D ist richtig
                            </label>
                        </li>
                    </ul>
                </div>

                <div class="form-group">
                    <label for="explanation">Allgemeine Erklärung zur Lösung</label>
                    <input
                        type="text"
                        id="explanation"
                        name="explanation"
                        placeholder="Optionale allgemeine Erklärung zur gesamten Frage"
                    >
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
            <p>Klicke eine Frage an, um Details und Aktionen anzuzeigen.</p>
        </div>

        <div class="lobby-meta">
            <div>
                <span>Gesamt</span>
                <strong>4 Fragen</strong>
            </div>

            <div>
                <span>Aktiv</span>
                <strong>3 aktiv</strong>
            </div>
        </div>

        <div class="participant-header">
            <h3>Fragenliste</h3>
            <span>4 Einträge</span>
        </div>

        <div class="ranking-list">

            <details class="ranking-item">
                <summary>
                    <strong>Was macht htmlspecialchars()?</strong>
                    <span class="correct-text">Aktiv</span>
                </summary>

                <div class="ranking-footer">
                    <span>Fragenpool: PHP Grundlagen</span>
                </div>

                <div class="ranking-footer">
                    <span>Wandelt Sonderzeichen in HTML-Entities um und hilft gegen XSS.</span>
                </div>

                <div class="ranking-footer">
                    <span><strong>Richtig:</strong> Antwort A und Antwort B</span>
                </div>

                <div class="lobby-actions">
                    <form method="POST" action="#">
                        <button type="submit" class="btn-secondary">
                            Frage bearbeiten
                        </button>
                    </form>

                    <form method="POST" action="#">
                        <button type="submit" class="btn-secondary">
                            Frage deaktivieren
                        </button>
                    </form>

                    <form method="POST" action="#">
                        <button type="submit" class="btn-secondary">
                            Frage entfernen
                        </button>
                    </form>
                </div>
            </details>

            <details class="ranking-item">
                <summary>
                    <strong>Wofür steht CSS?</strong>
                    <span class="correct-text">Aktiv</span>
                </summary>

                <div class="ranking-footer">
                    <span>Fragenpool: HTML und CSS</span>
                </div>

                <div class="ranking-footer">
                    <span>CSS steht für Cascading Style Sheets und beschreibt die Gestaltung von Webseiten.</span>
                </div>

                <div class="ranking-footer">
                    <span><strong>Richtig:</strong> Antwort A</span>
                </div>

                <div class="lobby-actions">
                    <form method="POST" action="#">
                        <button type="submit" class="btn-secondary">
                            Frage bearbeiten
                        </button>
                    </form>

                    <form method="POST" action="#">
                        <button type="submit" class="btn-secondary">
                            Frage deaktivieren
                        </button>
                    </form>

                    <form method="POST" action="#">
                        <button type="submit" class="btn-secondary">
                            Frage entfernen
                        </button>
                    </form>
                </div>
            </details>

            <details class="ranking-item">
                <summary>
                    <strong>Was ist ein Event Listener?</strong>
                    <span class="wrong-text">Inaktiv</span>
                </summary>

                <div class="ranking-footer">
                    <span>Fragenpool: JavaScript Grundlagen</span>
                </div>

                <div class="ranking-footer">
                    <span>Ein Event Listener wartet auf ein bestimmtes Ereignis, zum Beispiel einen Klick.</span>
                </div>

                <div class="ranking-footer">
                    <span><strong>Richtig:</strong> Antwort A</span>
                </div>

                <div class="lobby-actions">
                    <form method="POST" action="#">
                        <button type="submit" class="btn-secondary">
                            Frage bearbeiten
                        </button>
                    </form>

                    <form method="POST" action="#">
                        <button type="submit" class="btn-secondary">
                            Frage aktivieren
                        </button>
                    </form>

                    <form method="POST" action="#">
                        <button type="submit" class="btn-secondary">
                            Frage entfernen
                        </button>
                    </form>
                </div>
            </details>

            <details class="ranking-item">
                <summary>
                    <strong>Welcher SQL-Befehl liest Daten aus?</strong>
                    <span class="correct-text">Aktiv</span>
                </summary>

                <div class="ranking-footer">
                    <span>Fragenpool: Datenbanken</span>
                </div>

                <div class="ranking-footer">
                    <span>Mit SELECT werden Daten aus einer Datenbanktabelle abgefragt.</span>
                </div>

                <div class="ranking-footer">
                    <span><strong>Richtig:</strong> Antwort A</span>
                </div>

                <div class="lobby-actions">
                    <form method="POST" action="#">
                        <button type="submit" class="btn-secondary">
                            Frage bearbeiten
                        </button>
                    </form>

                    <form method="POST" action="#">
                        <button type="submit" class="btn-secondary">
                            Frage deaktivieren
                        </button>
                    </form>

                    <form method="POST" action="#">
                        <button type="submit" class="btn-secondary">
                            Frage entfernen
                        </button>
                    </form>
                </div>
            </details>

        </div>

    </aside>

</main>

<script>
    const dropZone   = document.getElementById('importDropZone');
    const fileInput  = document.getElementById('importFile');
    const dropContent = document.getElementById('importDropContent');
    const submitBtn  = document.getElementById('importSubmit');

    function setFile(file) {
        if (!file) return;
        dropContent.innerHTML = `
            <div class="import-drop-icon">XLS</div>
            <p><strong>${file.name}</strong></p>
            <span class="import-drop-hint">${(file.size / 1024).toFixed(1)} KB — bereit zum Import</span>
        `;
        submitBtn.disabled = false;
        dropZone.classList.add('has-file');
    }

    fileInput.addEventListener('change', () => setFile(fileInput.files[0]));

    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
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
</script>

</body>
</html>