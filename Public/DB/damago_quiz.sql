-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 22. Mai 2026 um 15:17
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `damago_quiz`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `answer_options`
--

CREATE TABLE `answer_options` (
  `id` int(10) UNSIGNED NOT NULL,
  `question_id` int(10) UNSIGNED NOT NULL,
  `sort_order` tinyint(3) UNSIGNED NOT NULL,
  `answer_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `explanation` text NOT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `answer_options`
--

INSERT INTO `answer_options` (`id`, `question_id`, `sort_order`, `answer_text`, `is_correct`, `explanation`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES
(1, 1, 1, 'import math', 1, 'Richtig. import math bindet das Modulobjekt unter dem Namen math. Alle Zugriffe müssen qualifiziert erfolgen, zum Beispiel math.sqrt() oder math.ceil().', 1, '2026-05-18 23:16:07', NULL, NULL),
(2, 1, 2, 'from math import sqrt', 0, 'Falsch. Diese Anweisung importiert nur den Namen sqrt direkt. Sie lädt nicht den gesamten Modul-Namespace und erfordert für sqrt keinen Zugriff über math.', 1, '2026-05-18 23:16:07', NULL, NULL),
(3, 1, 3, 'from math import *', 0, 'Falsch. Diese Anweisung importiert alle öffentlichen Namen direkt in den aktuellen Namespace. Danach ist kein Modul-Präfix wie math erforderlich.', 1, '2026-05-18 23:16:07', NULL, NULL),
(4, 1, 4, 'import math as m', 0, 'Falsch. Diese Anweisung importiert das ganze Modul, aber unter dem Alias m. Die Frage verlangt ausdrücklich den Zugriff über den Modulnamen math.', 1, '2026-05-18 23:16:07', NULL, NULL),
(5, 2, 1, 'isinstance(d, Dog) returns True.', 0, 'Falsch als Auswahl. Diese Aussage ist korrekt, weil d direkt aus der Klasse Dog erzeugt wurde.', 1, '2026-05-18 23:16:07', NULL, NULL),
(6, 2, 2, 'isinstance(d, Animal) returns True.', 0, 'Falsch als Auswahl. Diese Aussage ist korrekt, weil Dog von Animal erbt und d deshalb auch als Animal-Instanz gilt.', 1, '2026-05-18 23:16:07', NULL, NULL),
(7, 2, 3, 'isinstance(d, object) returns False.', 1, 'Richtig als Auswahl. Diese Aussage ist fachlich falsch, weil normale Python-Objekte direkt oder indirekt Instanzen von object sind.', 1, '2026-05-18 23:16:07', NULL, NULL),
(8, 2, 4, 'isinstance(Animal(), Dog) returns False.', 0, 'Falsch als Auswahl. Diese Aussage ist korrekt, weil ein allgemeines Animal-Objekt kein Dog-Objekt ist.', 1, '2026-05-18 23:16:07', NULL, NULL),
(9, 3, 1, 'A single leading underscore is a convention for internal or non-public members.', 1, 'Richtig. Ein einzelner führender Unterstrich signalisiert vor allem eine Konvention für interne oder nicht öffentliche Verwendung.', 1, '2026-05-18 23:16:07', NULL, NULL),
(10, 3, 2, 'A double leading underscore can trigger name mangling inside a class.', 1, 'Richtig. Doppelte führende Unterstriche können innerhalb einer Klasse Name Mangling auslösen.', 1, '2026-05-18 23:16:07', NULL, NULL),
(11, 3, 3, 'A single leading underscore prevents all access from outside the class.', 0, 'Falsch. Ein einfacher führender Unterstrich verhindert den Zugriff nicht technisch. Er ist hauptsächlich eine Konvention.', 1, '2026-05-18 23:16:07', NULL, NULL),
(12, 3, 4, 'A double leading underscore makes an attribute impossible to access under any name.', 0, 'Falsch. Auch ein durch Name Mangling veränderter Name kann über den gemangelten Namen weiterhin erreicht werden.', 1, '2026-05-18 23:16:07', NULL, NULL),
(13, 4, 1, 'map() after filter() transforms only the filtered elements.', 1, 'Richtig. Wenn map() auf das Ergebnis von filter() angewendet wird, werden nur die von filter() durchgelassenen Elemente transformiert.', 1, '2026-05-18 23:16:07', NULL, NULL),
(14, 4, 2, 'map() and filter() always return lists in Python 3.', 0, 'Falsch. In Python 3 geben map() und filter() Iterator-Objekte zurück. Für eine Liste ist eine explizite Umwandlung mit list() nötig.', 1, '2026-05-18 23:16:07', NULL, NULL),
(15, 4, 3, 'list(map(lambda x: x**2, filter(lambda x: x % 2 == 0, [1,2,3,4,5,6]))) returns [4, 16, 36].', 1, 'Richtig. Zuerst filtert filter() die geraden Zahlen 2, 4 und 6. Danach quadriert map() diese Werte zu 4, 16 und 36.', 1, '2026-05-18 23:16:07', NULL, NULL),
(16, 4, 4, 'filter() after map() filters the transformed results.', 1, 'Richtig. Wenn filter() nach map() verwendet wird, wird die Filterbedingung auf die bereits transformierten Werte angewendet.', 1, '2026-05-18 23:16:07', NULL, NULL),
(17, 5, 1, 'Assigning to self.name creates or updates an attribute on the current instance.', 1, 'Richtig. Eine Zuweisung über self speichert oder aktualisiert ein Attribut auf der konkreten Instanz.', 1, '2026-05-18 23:16:07', NULL, NULL),
(18, 5, 2, 'Attributes assigned through self usually appear in the instance __dict__.', 1, 'Richtig. Normale Instanzattribute werden üblicherweise im __dict__ der Instanz abgelegt.', 1, '2026-05-18 23:16:07', NULL, NULL),
(19, 5, 3, 'Instance attributes must be declared in the class body before __init__ can assign them.', 0, 'Falsch. Python verlangt keine vorherige Deklaration von Instanzattributen im Klassenrumpf.', 1, '2026-05-18 23:16:07', NULL, NULL),
(20, 5, 4, 'The first parameter of __init__ is the class object itself.', 0, 'Falsch. Bei einer Instanzmethode bezeichnet der erste Parameter die neue Instanz, üblicherweise self, nicht das Klassenobjekt.', 1, '2026-05-18 23:16:07', NULL, NULL),
(21, 6, 1, 'None', 0, 'Falsch. __name__ ist nicht None, sondern enthält immer einen String.', 1, '2026-05-18 23:16:07', NULL, NULL),
(22, 6, 2, '\"__main__\"', 1, 'Richtig. Wird ein Modul direkt ausgeführt, setzt Python __name__ auf den String \"__main__\".', 1, '2026-05-18 23:16:07', NULL, NULL),
(23, 6, 3, 'The absolute path of the file', 0, 'Falsch. Der absolute Pfad ist über __file__ verfügbar, nicht über __name__.', 1, '2026-05-18 23:16:07', NULL, NULL),
(24, 6, 4, 'The filename without the .py extension', 0, 'Falsch. Bei direkter Ausführung wird nicht der Dateiname ohne Erweiterung verwendet, sondern der spezielle String \"__main__\".', 1, '2026-05-18 23:16:07', NULL, NULL),
(25, 7, 1, 'The attribute lookup searches the instance first, then the class, then base classes.', 1, 'Richtig. Python sucht Attribute zuerst in der Instanz, dann in der Klasse der Instanz und danach in den Basisklassen.', 1, '2026-05-18 23:16:07', NULL, NULL),
(26, 7, 2, 'AttributeError is raised because class variables cannot be overridden in subclasses.', 0, 'Falsch. Klassenvariablen können in Subklassen überschrieben werden. Das ist ein normales Merkmal von Vererbung.', 1, '2026-05-18 23:16:07', NULL, NULL),
(27, 7, 3, 'The output is False because instances always read the base class variable.', 0, 'Falsch. Der Lookup findet AppConfig.debug vor Config.debug, weil AppConfig die Klassenvariable überschreibt.', 1, '2026-05-18 23:16:07', NULL, NULL),
(28, 7, 4, 'The output is True because the class variable overridden in the subclass takes precedence.', 1, 'Richtig. AppConfig.debug hat den Wert True und wird vor Config.debug gefunden.', 1, '2026-05-18 23:16:07', NULL, NULL),
(29, 8, 1, 'An instance method normally declares self as its first parameter.', 1, 'Richtig. self verweist beim Methodenaufruf auf die aktuelle Instanz.', 1, '2026-05-18 23:16:08', NULL, NULL),
(30, 8, 2, 'The name self is a reserved keyword in Python.', 0, 'Falsch. self ist eine starke Konvention, aber kein reserviertes Schlüsselwort in Python.', 1, '2026-05-18 23:16:08', NULL, NULL),
(31, 8, 3, 'Calling an instance method through an object supplies the object as the first argument.', 1, 'Richtig. Beim Aufruf obj.method() wird die Instanz automatisch als erstes Argument an die Methode übergeben.', 1, '2026-05-18 23:16:08', NULL, NULL),
(32, 8, 4, 'Instance methods can be defined only outside a class body.', 0, 'Falsch. Instanzmethoden werden normalerweise innerhalb der Klassendefinition definiert.', 1, '2026-05-18 23:16:08', NULL, NULL),
(33, 9, 1, '-3.0', 0, 'Falsch. floor() gibt in Python 3 einen int zurück und rundet nicht zu -3.0.', 1, '2026-05-18 23:16:08', NULL, NULL),
(34, 9, 2, '3', 0, 'Falsch. Diese Antwort ignoriert das Vorzeichen. floor() arbeitet auf der reellen Zahlengeraden.', 1, '2026-05-18 23:16:08', NULL, NULL),
(35, 9, 3, '-4', 1, 'Richtig. math.floor(-3.7) liefert die größte ganze Zahl, die kleiner oder gleich -3.7 ist. Das ist -4.', 1, '2026-05-18 23:16:08', NULL, NULL),
(36, 9, 4, '-3', 0, 'Falsch. -3 wäre das Ergebnis von math.ceil(-3.7), nicht von floor().', 1, '2026-05-18 23:16:08', NULL, NULL),
(37, 10, 1, 'A B C', 1, 'Richtig. Der try-Block gibt zuerst A aus, die ValueError wird im except-Block behandelt und finally läuft immer am Ende.', 1, '2026-05-18 23:16:08', NULL, NULL),
(38, 10, 2, 'A C', 0, 'Falsch. Der except-Block wird ausgeführt, daher fehlt B nicht in der Ausgabe.', 1, '2026-05-18 23:16:08', NULL, NULL),
(39, 10, 3, 'B C', 0, 'Falsch. A wird bereits vor dem Auslösen der Exception ausgegeben.', 1, '2026-05-18 23:16:08', NULL, NULL),
(40, 10, 4, 'A ValueError C', 0, 'Falsch. Die ValueError wird abgefangen und erscheint deshalb nicht als ungefangener Fehler in der Ausgabe.', 1, '2026-05-18 23:16:08', NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `departments`
--

CREATE TABLE `departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(80) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `departments`
--

INSERT INTO `departments` (`id`, `parent_id`, `name`, `display_name`, `description`, `is_active`, `created_by`, `created_at`, `updated_at`, `updated_by`) VALUES
(1, NULL, 'it', 'IT', 'Hauptbereich für IT-Umschulungen und IT-nahe Kurse.', 1, 1, '2026-05-19 00:00:00', NULL, NULL),
(2, NULL, 'care', 'Pflege', 'Hauptbereich für Pflege-Umschulungen und pflegebezogene Kurse.', 1, 1, '2026-05-19 00:00:00', NULL, NULL),
(3, 1, 'application_development', 'Anwendungsentwicklung', 'Unterbereich der IT für Fachinformatiker(innen) in der Anwendungsentwicklung.', 1, 1, '2026-05-19 00:00:00', NULL, NULL),
(4, 1, 'system_integration', 'Systemintegration', 'Unterbereich der IT für Fachinformatiker(innen) in der Systemintegration.', 1, 1, '2026-05-19 00:00:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `games`
--

CREATE TABLE `games` (
  `id` int(10) UNSIGNED NOT NULL,
  `question_pool_id` int(10) UNSIGNED NOT NULL,
  `host_user_id` int(10) UNSIGNED DEFAULT NULL,
  `host_name` varchar(50) NOT NULL,
  `join_code` varchar(6) NOT NULL,
  `host_token_hash` char(64) NOT NULL,
  `question_count` smallint(5) UNSIGNED NOT NULL,
  `time_limit_seconds` smallint(5) UNSIGNED NOT NULL,
  `score_mode_id` tinyint(3) UNSIGNED NOT NULL,
  `status_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `host_plays` tinyint(1) NOT NULL DEFAULT 0,
  `current_question_position` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `started_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `game_statuses`
--

CREATE TABLE `game_statuses` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `code` varchar(30) NOT NULL,
  `display_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `game_statuses`
--

INSERT INTO `game_statuses` (`id`, `code`, `display_name`) VALUES
(1, 'lobby', 'Lobby offen'),
(2, 'running', 'Spiel läuft'),
(3, 'reveal', 'Lösung wird angezeigt'),
(4, 'finished', 'Spiel beendet'),
(5, 'aborted', 'Spiel abgebrochen');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lobby_players`
--

CREATE TABLE `lobby_players` (
  `id` int(11) NOT NULL,
  `lobby_id` int(11) NOT NULL,
  `player_name` varchar(50) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `lobby_players`
--

INSERT INTO `lobby_players` (`id`, `lobby_id`, `player_name`, `joined_at`) VALUES
(1, 1, 'Gast', '2026-05-22 09:03:06'),
(2, 2, 'Gast', '2026-05-22 09:11:10'),
(3, 2, 'test', '2026-05-22 09:11:19'),
(4, 2, 'testspieler', '2026-05-22 09:14:14'),
(5, 3, 'testspieler', '2026-05-22 09:22:10'),
(6, 4, 'testspieler', '2026-05-22 09:23:21'),
(7, 5, 'Gast', '2026-05-22 09:24:29'),
(8, 5, 'testspieler', '2026-05-22 09:24:55'),
(9, 6, 'Gast', '2026-05-22 09:44:52'),
(10, 6, 'Gast-9543', '2026-05-22 09:45:28'),
(11, 6, 'test', '2026-05-22 09:50:22'),
(12, 7, 'test', '2026-05-22 09:51:26'),
(13, 8, 'test', '2026-05-22 10:55:25'),
(14, 8, 'Gast-2803', '2026-05-22 10:55:55'),
(15, 9, 'test', '2026-05-22 10:58:51'),
(16, 10, 'Gast', '2026-05-22 11:01:32'),
(17, 10, 'Gast-9358', '2026-05-22 11:01:54'),
(18, 11, 'test', '2026-05-22 11:18:10'),
(19, 12, 'Gast', '2026-05-22 11:37:52'),
(20, 13, 'Gast', '2026-05-22 11:53:05'),
(21, 13, 'Gast-6593', '2026-05-22 11:53:29'),
(22, 14, 'test', '2026-05-22 11:56:28'),
(23, 14, 'Gast-6914', '2026-05-22 11:56:53'),
(24, 15, 'Gast', '2026-05-22 11:57:50'),
(25, 15, 'Gast-5687', '2026-05-22 11:58:13'),
(26, 16, 'Gast', '2026-05-22 12:50:28'),
(27, 17, 'test', '2026-05-22 12:58:24'),
(28, 18, 'test', '2026-05-22 13:09:42'),
(29, 19, 'test', '2026-05-22 13:10:21'),
(30, 20, 'Gast', '2026-05-22 13:10:48');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lobby_questions`
--

CREATE TABLE `lobby_questions` (
  `id` int(11) NOT NULL,
  `lobby_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `lobby_questions`
--

INSERT INTO `lobby_questions` (`id`, `lobby_id`, `question_id`, `sort_order`) VALUES
(1, 8, 10, 0),
(2, 8, 2, 1),
(3, 8, 6, 2),
(4, 8, 4, 3),
(5, 8, 9, 4),
(6, 8, 8, 5),
(7, 8, 1, 6),
(8, 8, 7, 7),
(9, 8, 3, 8),
(10, 8, 5, 9),
(11, 10, 4, 0),
(12, 10, 5, 1),
(13, 10, 7, 2),
(14, 10, 8, 3),
(15, 10, 3, 4),
(16, 10, 9, 5),
(17, 10, 10, 6),
(18, 10, 1, 7),
(19, 10, 6, 8),
(20, 10, 2, 9),
(21, 11, 6, 0),
(22, 11, 4, 1),
(23, 11, 10, 2),
(24, 11, 8, 3),
(25, 11, 7, 4),
(26, 11, 9, 5),
(27, 11, 3, 6),
(28, 11, 2, 7),
(29, 11, 1, 8),
(30, 11, 5, 9),
(31, 12, 9, 0),
(32, 12, 7, 1),
(33, 12, 8, 2),
(34, 12, 2, 3),
(35, 12, 1, 4),
(36, 12, 10, 5),
(37, 12, 3, 6),
(38, 12, 5, 7),
(39, 12, 6, 8),
(40, 12, 4, 9),
(41, 13, 4, 0),
(42, 13, 9, 1),
(43, 13, 2, 2),
(44, 13, 8, 3),
(45, 13, 1, 4),
(46, 13, 10, 5),
(47, 13, 5, 6),
(48, 13, 3, 7),
(49, 13, 7, 8),
(50, 13, 6, 9),
(51, 15, 6, 0),
(52, 15, 9, 1),
(53, 15, 7, 2),
(54, 15, 4, 3),
(55, 15, 5, 4),
(56, 15, 3, 5),
(57, 15, 8, 6),
(58, 15, 10, 7),
(59, 15, 2, 8),
(60, 15, 1, 9),
(61, 17, 8, 0),
(62, 17, 10, 1),
(63, 17, 1, 2),
(64, 17, 9, 3),
(65, 17, 3, 4),
(66, 17, 4, 5),
(67, 17, 5, 6),
(68, 17, 2, 7),
(69, 17, 7, 8),
(70, 17, 6, 9),
(71, 20, 1, 0),
(72, 20, 7, 1),
(73, 20, 6, 2),
(74, 20, 4, 3),
(75, 20, 10, 4),
(76, 20, 5, 5),
(77, 20, 3, 6),
(78, 20, 9, 7),
(79, 20, 8, 8),
(80, 20, 2, 9);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `media_files`
--

CREATE TABLE `media_files` (
  `id` int(10) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_size` int(10) UNSIGNED NOT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `participants`
--

CREATE TABLE `participants` (
  `id` int(10) UNSIGNED NOT NULL,
  `game_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `display_name` varchar(50) NOT NULL,
  `avatar` varchar(100) DEFAULT NULL,
  `session_token_hash` char(64) NOT NULL,
  `is_host_player` tinyint(1) NOT NULL DEFAULT 0,
  `is_removed` tinyint(1) NOT NULL DEFAULT 0,
  `joined_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_seen_at` datetime DEFAULT NULL,
  `removed_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `questions`
--

CREATE TABLE `questions` (
  `id` int(10) UNSIGNED NOT NULL,
  `question_pool_id` int(10) UNSIGNED NOT NULL,
  `question_text` text NOT NULL,
  `image_id` int(10) UNSIGNED DEFAULT NULL,
  `explanation` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `questions`
--

INSERT INTO `questions` (`id`, `question_pool_id`, `question_text`, `image_id`, `explanation`, `created_by`, `is_active`, `created_at`, `updated_at`, `updated_by`) VALUES
(1, 1, 'Which import statement loads the entire math module so that all its functions must be accessed using the module name as a prefix?', NULL, 'Korrekt ist die Variante, bei der das komplette math-Modul unter dem Namen math eingebunden wird.', 1, 1, '2026-05-18 23:16:07', NULL, NULL),
(2, 1, 'Consider the following code. Which statement about isinstance() and inheritance is incorrect?\r\n\r\nclass Animal:\r\n    pass\r\n\r\nclass Dog(Animal):\r\n    pass\r\n\r\nd = Dog()', NULL, 'Gesucht ist die fachlich falsche Aussage über isinstance() und Vererbung.', 1, 1, '2026-05-18 23:16:07', NULL, NULL),
(3, 1, 'Which two statements about names starting with underscores in classes are correct?', NULL, 'Diese Frage prüft Python-Konventionen zu nicht öffentlichen Namen und Name Mangling.', 1, 1, '2026-05-18 23:16:07', NULL, NULL),
(4, 1, 'Which of the following statements about combining map() and filter() are correct? (Choose three)', NULL, 'Diese Frage prüft die Kombination von map() und filter() sowie deren Rückgabewerte in Python 3.', 1, 1, '2026-05-18 23:16:07', NULL, NULL),
(5, 1, 'Which two statements about instance attributes initialized in __init__ are correct?', NULL, 'Diese Frage prüft, wie Instanzattribute in Python über self erzeugt und gespeichert werden.', 1, 1, '2026-05-18 23:16:07', NULL, NULL),
(6, 1, 'What value does the variable __name__ hold when a Python module is executed directly as the main program, for example python script.py?', NULL, 'Diese Frage prüft das Python-Standardmuster if __name__ == \"__main__\".', 1, 1, '2026-05-18 23:16:07', NULL, NULL),
(7, 1, 'Consider the following code. Which of the following statements about this code are correct?\r\n\r\nclass Config:\r\n    debug = False\r\n\r\nclass AppConfig(Config):\r\n    debug = True\r\n\r\nac = AppConfig()\r\nprint(ac.debug)', NULL, 'Diese Frage prüft die Reihenfolge der Attributauflösung bei Instanzen, Klassen und Basisklassen.', 1, 1, '2026-05-18 23:16:07', NULL, NULL),
(8, 1, 'Which two statements about instance methods are correct?', NULL, 'Diese Frage prüft den self-Parameter und den automatischen Methodenaufruf über eine Instanz.', 1, 1, '2026-05-18 23:16:08', NULL, NULL),
(9, 1, 'What output does the following code produce?\r\n\r\nimport math\r\nprint(math.floor(-3.7))', NULL, 'Diese Frage prüft das Verhalten von math.floor() bei negativen Zahlen.', 1, 1, '2026-05-18 23:16:08', NULL, NULL),
(10, 1, 'Consider the following code. What is printed?\r\n\r\ntry:\r\n    print(\"A\", end=\" \")\r\n    raise ValueError(\"bad\")\r\nexcept ValueError:\r\n    print(\"B\", end=\" \")\r\nfinally:\r\n    print(\"C\")', NULL, 'Diese Frage prüft die Ausführungsreihenfolge von try, except und finally.', 1, 1, '2026-05-18 23:16:08', NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `question_pools`
--

CREATE TABLE `question_pools` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `question_pools`
--

INSERT INTO `question_pools` (`id`, `name`, `description`, `created_by`, `is_active`, `created_at`, `updated_at`, `updated_by`) VALUES
(1, 'PCAP Python Grundlagen Testpool', 'Test-Fragenpool mit zehn PCAP-Fragen aus dem Fragenkatalog. Enthält Multiple-Select-Fragen mit exakt vier Antwortmöglichkeiten und deutschen Erklärungen.', 1, 1, '2026-05-18 23:16:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `question_pool_departments`
--

CREATE TABLE `question_pool_departments` (
  `question_pool_id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `question_pool_departments`
--

INSERT INTO `question_pool_departments` (`question_pool_id`, `department_id`, `created_at`, `created_by`) VALUES
(1, 3, '2026-05-19 00:00:00', 1),
(1, 4, '2026-05-19 00:00:00', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `quiz_lobbies`
--

CREATE TABLE `quiz_lobbies` (
  `id` int(11) NOT NULL,
  `join_code` varchar(5) NOT NULL,
  `host_name` varchar(255) NOT NULL,
  `question_pool` varchar(100) DEFAULT NULL,
  `question_count` int(11) DEFAULT NULL,
  `time_limit` int(11) DEFAULT NULL,
  `point_mode` varchar(50) DEFAULT NULL,
  `host_plays` varchar(3) DEFAULT NULL,
  `is_started` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `quiz_lobbies`
--

INSERT INTO `quiz_lobbies` (`id`, `join_code`, `host_name`, `question_pool`, `question_count`, `time_limit`, `point_mode`, `host_plays`, `is_started`, `created_at`) VALUES
(1, 'JSQK2', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-22 09:03:06'),
(2, 'G5G34', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-22 09:11:10'),
(3, 'TYHBH', 'testspieler', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-22 09:22:10'),
(4, 'AZ5EY', 'testspieler', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-22 09:23:21'),
(5, '9RAAC', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-22 09:24:29'),
(6, 'T4SLK', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-22 09:44:52'),
(7, 'YSKQN', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'no', 1, '2026-05-22 09:51:12'),
(8, 'A4PWY', 'test', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 10:55:25'),
(9, '8M2KW', 'test', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 10:58:51'),
(10, 'NJBZ7', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 11:01:32'),
(11, 'AMNPW', 'test', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 11:18:10'),
(12, 'KCJTH', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 11:37:52'),
(13, 'FLU4M', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 11:53:05'),
(14, 'Q3MHQ', 'test', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 11:56:28'),
(15, 'HWAF2', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 11:57:50'),
(16, 'H8WTR', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 12:50:28'),
(17, 'JFE6P', 'test', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 12:58:24'),
(18, 'YESYC', 'test', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-22 13:09:42'),
(19, 'D7MZZ', 'test', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 13:10:21'),
(20, 'P4CD5', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 13:10:48');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(30) NOT NULL,
  `display_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`) VALUES
(1, 'student', 'Schüler'),
(2, 'teacher', 'Dozent'),
(3, 'admin', 'Administrator');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `score_modes`
--

CREATE TABLE `score_modes` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `code` varchar(30) NOT NULL,
  `display_name` varchar(50) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `score_modes`
--

INSERT INTO `score_modes` (`id`, `code`, `display_name`, `description`) VALUES
(1, 'all_or_nothing', 'Ganz oder gar nicht', 'Es gibt nur Punkte, wenn die Auswahl exakt richtig ist. Jede falsche oder unvollständige Auswahl ergibt 0 Punkte.'),
(2, 'partial_points', 'Teilpunkte', 'Exakt richtige Auswahl ergibt volle Punkte. Eine teilweise richtige Auswahl ohne falsche Antwort ergibt Teilpunkte. Sobald eine falsche Antwort gewählt wurde, gibt es 0 Punkte.'),
(3, 'time_bonus', 'Zeitbonus', 'Bei richtiger Auswahl gibt es 500 Grundpunkte plus Zeitbonus aus der verbleibenden Zeit. Falsche Antworten ergeben 0 Punkte.');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `avatar_image_id` int(10) UNSIGNED DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `role_id`, `department_id`, `avatar_image_id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 3, NULL, NULL, 'admin', 'admin@damago.learn', '$2y$12$zZ4IIz1ewDWWMfFi0DeQtekSd2.111XdcntHfbWGgWXAT4rEGWmre', 1, '2026-05-19 01:29:09', NULL, NULL, NULL),
(2, 2, 3, NULL, 'Kevin_hoeing', 'Kevin_hoeing@damago.learn', '$2y$12$nRN1d0ZF9EhgWCBwBJduYeLNM8r4y69hQbhWEMDgponqT17LlF3Da', 1, '2026-05-19 01:29:09', NULL, NULL, NULL),
(3, 1, 3, NULL, 'Paul_Schulte', 'Paul_Schulte@damago.learn', '$2y$12$.H39CvtqzhybIbH8suasXOwsqIYHQVK48aJi1vyVXtRzyUG.AQKoO', 1, '2026-05-19 01:29:09', NULL, NULL, NULL),
(4, 1, 3, NULL, 'Marcin_Banaszkiewicz', 'Marcin_Banaszkiewicz@damago.learn', '$2y$12$sC82J9LRjI.ye9RV1yScAOShFWkRLCDfuKmmYgRdrhfTGU35lYPOq', 1, '2026-05-19 01:29:09', NULL, NULL, NULL),
(5, 1, 3, NULL, 'Pascal_Arndt', 'Pascal_Arndt@damago.learn', '$2y$12$FH3SZiEtOqcXsb1bo6QqmeYIlhS.m0.vw449lXXRlx01k/nTaEthC', 1, '2026-05-19 01:29:09', NULL, NULL, NULL),
(6, 3, NULL, NULL, 'test', 'test@test.de', '$2y$10$sDaz15LWYvRjfKQjcApXm.uXzkED2Za32Q9IhjvdiDA.SsbSpJ49i', 1, '2026-05-22 09:36:58', '2026-05-22 09:49:26', NULL, NULL),
(7, 3, NULL, NULL, 'Pascal-A.', 'test3@test.de', '$2y$10$ZtewH.f61IE75yj/aF0omez5rUhgsnOklu7g/Hu/q7NktOioY4mjq', 1, '2026-05-22 09:58:37', '2026-05-22 10:00:06', NULL, NULL),
(9, 1, NULL, NULL, 'testspieler', 'test1@test.de', '$2y$10$taKvc2PU2Gu7E9DlMkTWrubQIB8v.b8cXpY4M8mOi9vVWzp331gEy', 1, '2026-05-22 11:13:55', NULL, NULL, NULL);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `answer_options`
--
ALTER TABLE `answer_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_answer_options_question_sort_order` (`question_id`,`sort_order`),
  ADD KEY `idx_answer_options_question_id` (`question_id`),
  ADD KEY `idx_answer_options_is_correct` (`is_correct`),
  ADD KEY `idx_answer_options_created_by` (`created_by`),
  ADD KEY `idx_answer_options_updated_by` (`updated_by`);

--
-- Indizes für die Tabelle `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_departments_name` (`name`),
  ADD KEY `idx_departments_parent_id` (`parent_id`),
  ADD KEY `idx_departments_is_active` (`is_active`),
  ADD KEY `idx_departments_created_by` (`created_by`),
  ADD KEY `idx_departments_updated_by` (`updated_by`);

--
-- Indizes für die Tabelle `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_games_join_code` (`join_code`),
  ADD UNIQUE KEY `uq_games_host_token_hash` (`host_token_hash`),
  ADD KEY `idx_games_question_pool_id` (`question_pool_id`),
  ADD KEY `idx_games_host_user_id` (`host_user_id`),
  ADD KEY `idx_games_score_mode_id` (`score_mode_id`),
  ADD KEY `idx_games_status_id` (`status_id`),
  ADD KEY `idx_games_created_by` (`created_by`),
  ADD KEY `idx_games_updated_by` (`updated_by`);

--
-- Indizes für die Tabelle `game_statuses`
--
ALTER TABLE `game_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_game_statuses_code` (`code`);

--
-- Indizes für die Tabelle `lobby_players`
--
ALTER TABLE `lobby_players`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lobby_id` (`lobby_id`);

--
-- Indizes für die Tabelle `lobby_questions`
--
ALTER TABLE `lobby_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lobby_id` (`lobby_id`);

--
-- Indizes für die Tabelle `media_files`
--
ALTER TABLE `media_files`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_media_files_file_name` (`file_name`),
  ADD KEY `idx_media_files_created_by` (`created_by`),
  ADD KEY `idx_media_files_updated_by` (`updated_by`);

--
-- Indizes für die Tabelle `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_participants_session_token_hash` (`session_token_hash`),
  ADD UNIQUE KEY `uq_participants_game_display_name` (`game_id`,`display_name`),
  ADD UNIQUE KEY `uq_participants_game_user` (`game_id`,`user_id`),
  ADD KEY `idx_participants_game_id` (`game_id`),
  ADD KEY `idx_participants_user_id` (`user_id`),
  ADD KEY `idx_participants_is_removed` (`is_removed`);

--
-- Indizes für die Tabelle `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_questions_question_pool_id` (`question_pool_id`),
  ADD KEY `idx_questions_created_by` (`created_by`),
  ADD KEY `idx_questions_is_active` (`is_active`),
  ADD KEY `idx_questions_image_id` (`image_id`),
  ADD KEY `idx_questions_updated_by` (`updated_by`);

--
-- Indizes für die Tabelle `question_pools`
--
ALTER TABLE `question_pools`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_question_pools_created_by` (`created_by`),
  ADD KEY `idx_question_pools_name` (`name`),
  ADD KEY `idx_question_pools_is_active` (`is_active`),
  ADD KEY `idx_question_pools_updated_by` (`updated_by`);

--
-- Indizes für die Tabelle `question_pool_departments`
--
ALTER TABLE `question_pool_departments`
  ADD PRIMARY KEY (`question_pool_id`,`department_id`),
  ADD KEY `idx_question_pool_departments_department_id` (`department_id`),
  ADD KEY `idx_qpd_created_by` (`created_by`);

--
-- Indizes für die Tabelle `quiz_lobbies`
--
ALTER TABLE `quiz_lobbies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `join_code` (`join_code`);

--
-- Indizes für die Tabelle `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_roles_name` (`name`);

--
-- Indizes für die Tabelle `score_modes`
--
ALTER TABLE `score_modes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_score_modes_code` (`code`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_username` (`username`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_role_id` (`role_id`),
  ADD KEY `idx_users_department_id` (`department_id`),
  ADD KEY `idx_users_avatar_image_id` (`avatar_image_id`),
  ADD KEY `idx_users_created_by` (`created_by`),
  ADD KEY `idx_users_updated_by` (`updated_by`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `answer_options`
--
ALTER TABLE `answer_options`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT für Tabelle `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `games`
--
ALTER TABLE `games`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `game_statuses`
--
ALTER TABLE `game_statuses`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `lobby_players`
--
ALTER TABLE `lobby_players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT für Tabelle `lobby_questions`
--
ALTER TABLE `lobby_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT für Tabelle `media_files`
--
ALTER TABLE `media_files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT für Tabelle `question_pools`
--
ALTER TABLE `question_pools`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `quiz_lobbies`
--
ALTER TABLE `quiz_lobbies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT für Tabelle `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `score_modes`
--
ALTER TABLE `score_modes`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `answer_options`
--
ALTER TABLE `answer_options`
  ADD CONSTRAINT `fk_answer_options_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_answer_options_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_answer_options_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints der Tabelle `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `fk_departments_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_departments_parent` FOREIGN KEY (`parent_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_departments_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints der Tabelle `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `fk_games_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_games_host_user` FOREIGN KEY (`host_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_games_question_pool` FOREIGN KEY (`question_pool_id`) REFERENCES `question_pools` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_games_score_mode` FOREIGN KEY (`score_mode_id`) REFERENCES `score_modes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_games_status` FOREIGN KEY (`status_id`) REFERENCES `game_statuses` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_games_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints der Tabelle `lobby_players`
--
ALTER TABLE `lobby_players`
  ADD CONSTRAINT `lobby_players_ibfk_1` FOREIGN KEY (`lobby_id`) REFERENCES `quiz_lobbies` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `lobby_questions`
--
ALTER TABLE `lobby_questions`
  ADD CONSTRAINT `lobby_questions_ibfk_1` FOREIGN KEY (`lobby_id`) REFERENCES `quiz_lobbies` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `media_files`
--
ALTER TABLE `media_files`
  ADD CONSTRAINT `fk_media_files_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_media_files_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints der Tabelle `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `fk_participants_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_participants_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints der Tabelle `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_questions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_questions_image` FOREIGN KEY (`image_id`) REFERENCES `media_files` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_questions_question_pool` FOREIGN KEY (`question_pool_id`) REFERENCES `question_pools` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_questions_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints der Tabelle `question_pools`
--
ALTER TABLE `question_pools`
  ADD CONSTRAINT `fk_question_pools_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_question_pools_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints der Tabelle `question_pool_departments`
--
ALTER TABLE `question_pool_departments`
  ADD CONSTRAINT `fk_qpd_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_qpd_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_qpd_question_pool` FOREIGN KEY (`question_pool_id`) REFERENCES `question_pools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_avatar_image` FOREIGN KEY (`avatar_image_id`) REFERENCES `media_files` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
