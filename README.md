# 🎓 damago Quizsystem

Willkommen beim **damago Quizsystem**! 🚀  
Diese interaktive, webbasierte Echtzeit-Quizplattform wurde speziell für Lernumgebungen, Schulungen und den gemeinsamen Spaß am Wissen entwickelt. Das System verbindet Hosts (Dozenten/Moderatoren) und Teilnehmer (User/Gäste) in einem packenden, synchronisierten Quiz-Erlebnis.

---

## ✨ Highlights & Kernfunktionen

### ⚡ Live-Synchronisation (Echtzeit-Erlebnis)
Das Herzstück des Systems ist die permanente Synchronisation zwischen dem Host und den Spielern. Durch ein ressourcenschonendes Polling-Verfahren im Hintergrund bleiben alle Bildschirme im exakt gleichen Takt:
* **Sofortige Reaktion:** Sobald der Host eine Frage weiterschaltet oder die Auflösung aktiviert, lädt die Seite aller Teilnehmer automatisch neu.
* **Live-Ranking:** Der Punktestand wird nach jeder Frage dynamisch berechnet und im Leaderboard in Echtzeit aktualisiert.
* **Maßgeblicher Server-Timer:** Der Countdown läuft serverseitig. Spät beitretende oder die Seite neu ladende Clients sehen sekundengenau dieselbe verbleibende Restzeit.

### 👑 Host-Moderation & Verwaltung
Als Host (Dozent oder Administrator) behältst du jederzeit die volle Kontrolle über die laufende Quizrunde:
* **Live-Spieler-Kick:** Störende oder inaktive Spieler können per Klick direkt aus der Lobby geworfen werden. Das System erkennt den Kick über das Polling innerhalb von 1,5 Sekunden, zerstört die Session des Spielers und leitet ihn automatisch aus dem Spiel.
* **Moderations-Modus:** Hosts können einstellen, ob sie selbst mitspielen (`host_plays = 'yes'`) oder das Spiel rein als Moderator auf einem Beamer oder Screen übertragen (`host_plays = 'no'`).
* **Sicherer Spielabbruch:** Eine laufende Runde kann vom Host jederzeit abgebrochen werden, wodurch alle Teilnehmer sicher zurück zum Dashboard geleitet werden.

### 🛡️ Sicherheit & Rollenarchitektur
Das System verfügt über ein striktes Sicherheitskonzept:
* **Rollenbasiertes Dashboard:** Automatische Zuweisung von Rechten und Oberflächen für *Administratoren*, *Dozenten*, *registrierte User* und *Gäste*.
* **CSRF-Schutz:** Jedes Formular und jede Statusänderung wird durch kryptografische Tokens gegen Cross-Site-Request-Forgery abgesichert.
* **SQL-Injection-Schutz:** Konsequente Nutzung von PDO-Prepared-Statements mit deaktivierter Emulation für maximale Datenbanksicherheit.
* **Session-Fixation-Schutz:** Bei jedem Login wird die Session-ID regeneriert, um Session-Hijacking effektiv zu verhindern.

---

## 🛠️ Technologie-Stack
* **Backend:** PHP 8+ (mit PDO-Erweiterung)
* **Datenbank:** MySQL / MariaDB (inkl. Fremdschlüssel-Beziehungen)
* **Frontend:** HTML5, CSS3, Vanilla JavaScript (Fetch API / Async Polling)
* **Syntax-Highlighting:** Highlight.js für ansprechende Code-Darstellung in Fragen und Antworten

---

## 🚀 Installation & Setup

Das Quizsystem lässt sich in wenigen Schritten in einer lokalen Entwicklungsumgebung (z. B. XAMPP) oder auf einem Webserver installieren.

### 1. Projekt klonen
Bringe das Projekt in das Stammverzeichnis deines Webservers (z. B. in den `htdocs`-Ordner deiner XAMPP-Installation):
```bash
git clone [https://github.com/dein-username/damago-quizsystem.git](https://github.com/dein-username/damago-quizsystem.git) `
```
### 2. Datenbank importieren
1. Öffne dein Datenbank-Verwaltungstool (z. B. **phpMyAdmin**).
2. Erstelle eine neue Datenbank mit dem Namen `damago_quiz` und der Kollation `utf8mb4_unicode_ci`.
3. Klicke auf **Importieren** und wähle die Datei `damago_quiz.sql` aus dem Projektverzeichnis aus, um die Tabellenstruktur, vordefinierte Rollen, Avatare und Beispiel-Fragen zu laden.

### 3. Datenbankverbindung anpassen
Öffne die Datei `db.php` im Texteditor und passe die Zugangsdaten für deine MySQL-Datenbank an:

```php
$host = 'localhost';
$db   = 'damago_quiz';
$user = 'root';     // Dein MySQL-Benutzername (Standard bei XAMPP: root)
$pass = '';         // Dein MySQL-Passwort (Standard bei XAMPP: leer)
$port = '3306';
$charset = 'utf8mb4';
```
## 🎮 Nutzung des Systems

1. **Anmeldung:** Rufe das Projekt im Browser auf (z. B. `http://localhost/damago/login.html`). Verwende einen der im SQL-Dump hinterlegten Test-Accounts (Admin, Dozent oder User) oder betrete das System unkompliziert als Gast.
2. **Quiz eröffnen (Host):** Navigiere im Dashboard auf "Quiz eröffnen". Wähle einen Fragenpool sowie den Punkte-Modus (*All-or-Nothing* oder *Teilpunkte*) aus. Du erhältst einen eindeutigen Join-Code für deine Lobby.
3. **Quiz beitreten (Spieler):** Spieler klicken im Dashboard auf "Quiz beitreten", wählen einen Namen sowie einen Avatar und geben den Join-Code des Hosts ein.
4. **Das Spiel starten:** Sobald alle Spieler in der Live-Rangliste auftauchen, startet der Host das Spiel. Von hier an übernimmt die automatisierte Synchronisations-Engine die Steuerung des gemeinsamen Quiz-Abends!

## 📁 Struktur der Kernkomponenten

* **`init.php`** - Zentraler Einstiegspunkt: Initialisiert Sessions, setzt Sicherheits-Cookies und stellt den CSRF-Schutz bereit.
* **`db.php`** - Erstellt die sichere PDO-Datenbankverbindung mit strengem Fehlerhandling.
* **`dashboard.php`** - Das rollenbasierte Kontrollzentrum für alle Nutzertypen.
* **`check_login.php`** - Validiert Benutzerdaten mittels sicherer Passwort-Verifizierung (`password_verify`).
* **`game.php`** - Die spielentscheidende Oberfläche: Regelt die Anzeige der Fragen, die Timer-Intervalle und steuert das asynchrone Polling.
* **`check_next_question.php`** - Die API-Schnittstelle im Hintergrund, die Spieler über Fragenwechsel, Kicks oder Spielabbrüche informiert.
* **`logout.php`** - Meldet Benutzer sicher ab, löscht Session-Cookies und zerstört die serverseitige Session restlos.

Viel Spaß beim Hosten, Quizzen und Lernen! 🎉
