/**
 * Berechnet die Punkte für eine Frage basierend auf dem gewählten Modus.
 * * @param {Array} selectedIds - Die IDs der vom Spieler angeklickten Antworten (z.B. [12, 14])
 * @param {Array} correctIds - Die IDs der wirklich richtigen Antworten aus der DB (z.B. [12, 15])
 * @param {string} pointMode - Der Spielmodus aus der Lobby-Einstellung (z.B. 'all_or_nothing')
 * @returns {number} Die ergatterten Punkte (1000 oder 0)
 */
function calculateQuestionPoints(selectedIds, correctIds, pointMode) {
    // UNSCHÄRFE ELIMINIEREN: Beide Arrays zwingend in echte Zahlen-Arrays umwandeln!
    const selected = Array.from(selectedIds).map(Number);
    const correct = Array.from(correctIds).map(Number);

    // MODUS 1: Ganz oder gar nicht (All or Nothing)
    if (pointMode === 'all_or_nothing') {
        
        // 1. Check: Stimmt die Anzahl der gewählten Antworten?
        if (selected.length !== correct.length) {
            return 0; 
        }

        // 2. Check: Ist jede gewählte ID in den korrekten IDs enthalten?
        // Da selected und correct jetzt beide aus reinen Numbers bestehen, greift .includes() perfekt!
        const allCorrect = selected.every(id => correct.includes(id));

        return allCorrect ? 1000 : 0;
    }

    return 0;
}