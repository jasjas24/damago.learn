<?php
/**
 * Gemeinsame Hilfsfunktionen für die Avatar-Auswahl.
 *
 * Die Avatar-Bilder liegen als PNG-Dateien unter /Public/Uploads/Avatare/.
 * Spieler (eingeloggt, Gast oder mitspielender Host) wählen beim Beitritt
 * bzw. beim Erstellen der Lobby genau einen Avatar aus. Gespeichert wird
 * nur der reine Dateiname in der Spalte lobby_players.avatar.
 */

// Web-Pfad zu den Avatar-Bildern, relativ zu den Seiten in /Public/PHP/.
if (!defined('DAMAGO_AVATAR_URL')) {
    define('DAMAGO_AVATAR_URL', '../Uploads/Avatare/');
}

// Pfad zum Avatar-Ordner im Dateisystem.
if (!defined('DAMAGO_AVATAR_DIR')) {
    define('DAMAGO_AVATAR_DIR', __DIR__ . '/../Uploads/Avatare/');
}

if (!function_exists('damago_available_avatars')) {
    /**
     * Liest alle verfügbaren Avatar-Dateinamen aus dem Avatar-Ordner.
     * Gibt eine alphabetisch sortierte Liste reiner Dateinamen zurück.
     *
     * @return string[]
     */
    function damago_available_avatars(): array
    {
        $files = glob(DAMAGO_AVATAR_DIR . '*.png') ?: [];
        $names = array_map('basename', $files);
        sort($names);
        return $names;
    }
}

if (!function_exists('damago_is_valid_avatar')) {
    /**
     * Prüft, ob ein übergebener Avatar-Dateiname tatsächlich existiert.
     * Schützt vor manipulierten Eingaben (z. B. Pfad-Injektion).
     */
    function damago_is_valid_avatar(?string $name): bool
    {
        if ($name === null || $name === '') {
            return false;
        }
        // Nur den reinen Dateinamen zulassen, niemals Pfade.
        if (basename($name) !== $name) {
            return false;
        }
        return in_array($name, damago_available_avatars(), true);
    }
}

if (!function_exists('damago_render_avatar_picker')) {
    /**
     * Gibt das HTML für die Avatar-Auswahl aus (Pflichtfeld).
     * Die Auswahl erfolgt über versteckte Radio-Buttons; das jeweils
     * gewählte Bild wird per CSS hervorgehoben.
     *
     * @param string $selected Aktuell gewählter Dateiname (für erneute Anzeige nach Fehler)
     */
    function damago_render_avatar_picker(string $selected = ''): void
    {
        $avatars = damago_available_avatars();
        ?>
        <div class="form-group avatar-group">
            <label>Avatar wählen <span class="avatar-required">*</span></label>
            <p class="avatar-hint">Wähle einen Avatar – ohne Avatar kann die Lobby nicht betreten werden.</p>
            <div class="avatar-picker">
                <?php foreach ($avatars as $file): ?>
                    <label class="avatar-option">
                        <input
                            type="radio"
                            name="avatar"
                            value="<?php echo htmlspecialchars($file, ENT_QUOTES); ?>"
                            <?php echo $file === $selected ? 'checked' : ''; ?>
                            required
                        >
                        <img
                            src="<?php echo DAMAGO_AVATAR_URL . rawurlencode($file); ?>"
                            alt="Avatar"
                            class="avatar-thumb"
                            loading="lazy"
                        >
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}
