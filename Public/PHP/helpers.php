<?php
/**
 * Gemeinsame Hilfsfunktionen zur sicheren Text-Darstellung.
 *
 * Fragen, Antworten und Erklärungen werden von Autoren als reiner Text gepflegt.
 * Enthält ein Text Programmcode, kann er im Markdown-Stil ausgezeichnet werden:
 *
 *   - Mehrzeiliger Code zwischen ```sprache ... ``` (Fenced Code Block)
 *         ```python
 *         print("Hallo")
 *         ```
 *   - Inline-Code zwischen einfachen Backticks:  `math.sqrt()`
 *
 * Sicherheit: Sämtlicher Text wird zuerst per htmlspecialchars() escaped.
 * Anschließend werden NUR die kontrollierten Tags <pre>, <code> und <br>
 * eingefügt. Es gelangt also kein unkontrolliertes HTML aus der Datenbank
 * in die Seite (kein XSS).
 */

if (!function_exists('render_inline_text')) {
    /**
     * Rendert einen Textabschnitt ohne Code-Blöcke:
     * Inline-Code (`...`) wird zu <code>, Zeilenumbrüche zu <br>.
     * Eignet sich für Stellen, an denen kein Block-Element erlaubt ist
     * (z. B. innerhalb von <button>).
     */
    function render_inline_text(string $text): string
    {
        if ($text === '') {
            return '';
        }

        // Abschnitt an Inline-Code (`...`) aufteilen, Trenner mit erfassen.
        $parts = preg_split('/(`[^`\n]+`)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $html  = '';

        foreach ($parts as $part) {
            if (strlen($part) >= 2 && $part[0] === '`' && substr($part, -1) === '`') {
                $inner = substr($part, 1, -1);
                $html .= '<code class="inline-code">' . htmlspecialchars($inner, ENT_QUOTES) . '</code>';
            } else {
                $html .= nl2br(htmlspecialchars($part, ENT_QUOTES));
            }
        }

        return $html;
    }
}

if (!function_exists('render_rich_text')) {
    /**
     * Rendert einen vollständigen Text inklusive mehrzeiliger Code-Blöcke.
     * Gibt fertiges, sicheres HTML zurück.
     */
    function render_rich_text(?string $text): string
    {
        $text = (string) $text;
        if ($text === '') {
            return '';
        }

        // Zeilenenden vereinheitlichen (Datenbank speichert teils \r\n).
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        $out     = '';
        $offset  = 0;
        $pattern = '/```[ \t]*([A-Za-z0-9+#._-]*)[ \t]*\n(.*?)```/s';

        if (preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $i => $full) {
                $start = $full[1];

                // Text vor dem Code-Block als normaler Fließtext.
                $out .= render_inline_text(substr($text, $offset, $start - $offset));

                $lang = trim($matches[1][$i][0]);
                $code = rtrim($matches[2][$i][0], "\n");

                $langClass = $lang !== ''
                    ? ' class="language-' . htmlspecialchars($lang, ENT_QUOTES) . '"'
                    : '';

                $out .= '<pre class="code-block"><code' . $langClass . '>'
                      . htmlspecialchars($code, ENT_QUOTES)
                      . '</code></pre>';

                $offset = $start + strlen($full[0]);
            }
        }

        // Restlichen Text nach dem letzten Code-Block anhängen.
        $out .= render_inline_text(substr($text, $offset));

        return $out;
    }
}
