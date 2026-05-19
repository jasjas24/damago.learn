CREATE DATABASE IF NOT EXISTS damago_learn_quiz_db
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE damago_learn_quiz_db;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `damago_learn_quiz_db`
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
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ;

--
-- Daten für Tabelle `answer_options`
--

INSERT INTO `answer_options` (`id`, `question_id`, `sort_order`, `answer_text`, `is_correct`, `explanation`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'import math', 1, 'Richtig. import math bindet das Modulobjekt unter dem Namen math. Alle Zugriffe müssen qualifiziert erfolgen, zum Beispiel math.sqrt() oder math.ceil().', '2026-05-18 23:16:07', NULL),
(2, 1, 2, 'from math import sqrt', 0, 'Falsch. Diese Anweisung importiert nur den Namen sqrt direkt. Sie lädt nicht den gesamten Modul-Namespace und erfordert für sqrt keinen Zugriff über math.', '2026-05-18 23:16:07', NULL),
(3, 1, 3, 'from math import *', 0, 'Falsch. Diese Anweisung importiert alle öffentlichen Namen direkt in den aktuellen Namespace. Danach ist kein Modul-Präfix wie math erforderlich.', '2026-05-18 23:16:07', NULL),
(4, 1, 4, 'import math as m', 0, 'Falsch. Diese Anweisung importiert das ganze Modul, aber unter dem Alias m. Die Frage verlangt ausdrücklich den Zugriff über den Modulnamen math.', '2026-05-18 23:16:07', NULL),
(5, 2, 1, 'isinstance(d, Dog) returns True.', 0, 'Falsch als Auswahl. Diese Aussage ist korrekt, weil d direkt aus der Klasse Dog erzeugt wurde.', '2026-05-18 23:16:07', NULL),
(6, 2, 2, 'isinstance(d, Animal) returns True.', 0, 'Falsch als Auswahl. Diese Aussage ist korrekt, weil Dog von Animal erbt und d deshalb auch als Animal-Instanz gilt.', '2026-05-18 23:16:07', NULL),
(7, 2, 3, 'isinstance(d, object) returns False.', 1, 'Richtig als Auswahl. Diese Aussage ist fachlich falsch, weil normale Python-Objekte direkt oder indirekt Instanzen von object sind.', '2026-05-18 23:16:07', NULL),
(8, 2, 4, 'isinstance(Animal(), Dog) returns False.', 0, 'Falsch als Auswahl. Diese Aussage ist korrekt, weil ein allgemeines Animal-Objekt kein Dog-Objekt ist.', '2026-05-18 23:16:07', NULL),
(9, 3, 1, 'A single leading underscore is a convention for internal or non-public members.', 1, 'Richtig. Ein einzelner führender Unterstrich signalisiert vor allem eine Konvention für interne oder nicht öffentliche Verwendung.', '2026-05-18 23:16:07', NULL),
(10, 3, 2, 'A double leading underscore can trigger name mangling inside a class.', 1, 'Richtig. Doppelte führende Unterstriche können innerhalb einer Klasse Name Mangling auslösen.', '2026-05-18 23:16:07', NULL),
(11, 3, 3, 'A single leading underscore prevents all access from outside the class.', 0, 'Falsch. Ein einfacher führender Unterstrich verhindert den Zugriff nicht technisch. Er ist hauptsächlich eine Konvention.', '2026-05-18 23:16:07', NULL),
(12, 3, 4, 'A double leading underscore makes an attribute impossible to access under any name.', 0, 'Falsch. Auch ein durch Name Mangling veränderter Name kann über den gemangelten Namen weiterhin erreicht werden.', '2026-05-18 23:16:07', NULL),
(13, 4, 1, 'map() after filter() transforms only the filtered elements.', 1, 'Richtig. Wenn map() auf das Ergebnis von filter() angewendet wird, werden nur die von filter() durchgelassenen Elemente transformiert.', '2026-05-18 23:16:07', NULL),
(14, 4, 2, 'map() and filter() always return lists in Python 3.', 0, 'Falsch. In Python 3 geben map() und filter() Iterator-Objekte zurück. Für eine Liste ist eine explizite Umwandlung mit list() nötig.', '2026-05-18 23:16:07', NULL),
(15, 4, 3, 'list(map(lambda x: x**2, filter(lambda x: x % 2 == 0, [1,2,3,4,5,6]))) returns [4, 16, 36].', 1, 'Richtig. Zuerst filtert filter() die geraden Zahlen 2, 4 und 6. Danach quadriert map() diese Werte zu 4, 16 und 36.', '2026-05-18 23:16:07', NULL),
(16, 4, 4, 'filter() after map() filters the transformed results.', 1, 'Richtig. Wenn filter() nach map() verwendet wird, wird die Filterbedingung auf die bereits transformierten Werte angewendet.', '2026-05-18 23:16:07', NULL),
(17, 5, 1, 'Assigning to self.name creates or updates an attribute on the current instance.', 1, 'Richtig. Eine Zuweisung über self speichert oder aktualisiert ein Attribut auf der konkreten Instanz.', '2026-05-18 23:16:07', NULL),
(18, 5, 2, 'Attributes assigned through self usually appear in the instance __dict__.', 1, 'Richtig. Normale Instanzattribute werden üblicherweise im __dict__ der Instanz abgelegt.', '2026-05-18 23:16:07', NULL),
(19, 5, 3, 'Instance attributes must be declared in the class body before __init__ can assign them.', 0, 'Falsch. Python verlangt keine vorherige Deklaration von Instanzattributen im Klassenrumpf.', '2026-05-18 23:16:07', NULL),
(20, 5, 4, 'The first parameter of __init__ is the class object itself.', 0, 'Falsch. Bei einer Instanzmethode bezeichnet der erste Parameter die neue Instanz, üblicherweise self, nicht das Klassenobjekt.', '2026-05-18 23:16:07', NULL),
(21, 6, 1, 'None', 0, 'Falsch. __name__ ist nicht None, sondern enthält immer einen String.', '2026-05-18 23:16:07', NULL),
(22, 6, 2, '\"__main__\"', 1, 'Richtig. Wird ein Modul direkt ausgeführt, setzt Python __name__ auf den String \"__main__\".', '2026-05-18 23:16:07', NULL),
(23, 6, 3, 'The absolute path of the file', 0, 'Falsch. Der absolute Pfad ist über __file__ verfügbar, nicht über __name__.', '2026-05-18 23:16:07', NULL),
(24, 6, 4, 'The filename without the .py extension', 0, 'Falsch. Bei direkter Ausführung wird nicht der Dateiname ohne Erweiterung verwendet, sondern der spezielle String \"__main__\".', '2026-05-18 23:16:07', NULL),
(25, 7, 1, 'The attribute lookup searches the instance first, then the class, then base classes.', 1, 'Richtig. Python sucht Attribute zuerst in der Instanz, dann in der Klasse der Instanz und danach in den Basisklassen.', '2026-05-18 23:16:07', NULL),
(26, 7, 2, 'AttributeError is raised because class variables cannot be overridden in subclasses.', 0, 'Falsch. Klassenvariablen können in Subklassen überschrieben werden. Das ist ein normales Merkmal von Vererbung.', '2026-05-18 23:16:07', NULL),
(27, 7, 3, 'The output is False because instances always read the base class variable.', 0, 'Falsch. Der Lookup findet AppConfig.debug vor Config.debug, weil AppConfig die Klassenvariable überschreibt.', '2026-05-18 23:16:07', NULL),
(28, 7, 4, 'The output is True because the class variable overridden in the subclass takes precedence.', 1, 'Richtig. AppConfig.debug hat den Wert True und wird vor Config.debug gefunden.', '2026-05-18 23:16:07', NULL),
(29, 8, 1, 'An instance method normally declares self as its first parameter.', 1, 'Richtig. self verweist beim Methodenaufruf auf die aktuelle Instanz.', '2026-05-18 23:16:08', NULL),
(30, 8, 2, 'The name self is a reserved keyword in Python.', 0, 'Falsch. self ist eine starke Konvention, aber kein reserviertes Schlüsselwort in Python.', '2026-05-18 23:16:08', NULL),
(31, 8, 3, 'Calling an instance method through an object supplies the object as the first argument.', 1, 'Richtig. Beim Aufruf obj.method() wird die Instanz automatisch als erstes Argument an die Methode übergeben.', '2026-05-18 23:16:08', NULL),
(32, 8, 4, 'Instance methods can be defined only outside a class body.', 0, 'Falsch. Instanzmethoden werden normalerweise innerhalb der Klassendefinition definiert.', '2026-05-18 23:16:08', NULL),
(33, 9, 1, '-3.0', 0, 'Falsch. floor() gibt in Python 3 einen int zurück und rundet nicht zu -3.0.', '2026-05-18 23:16:08', NULL),
(34, 9, 2, '3', 0, 'Falsch. Diese Antwort ignoriert das Vorzeichen. floor() arbeitet auf der reellen Zahlengeraden.', '2026-05-18 23:16:08', NULL),
(35, 9, 3, '-4', 1, 'Richtig. math.floor(-3.7) liefert die größte ganze Zahl, die kleiner oder gleich -3.7 ist. Das ist -4.', '2026-05-18 23:16:08', NULL),
(36, 9, 4, '-3', 0, 'Falsch. -3 wäre das Ergebnis von math.ceil(-3.7), nicht von floor().', '2026-05-18 23:16:08', NULL),
(37, 10, 1, 'A B C', 1, 'Richtig. Der try-Block gibt zuerst A aus, die ValueError wird im except-Block behandelt und finally läuft immer am Ende.', '2026-05-18 23:16:08', NULL),
(38, 10, 2, 'A C', 0, 'Falsch. Der except-Block wird ausgeführt, daher fehlt B nicht in der Ausgabe.', '2026-05-18 23:16:08', NULL),
(39, 10, 3, 'B C', 0, 'Falsch. A wird bereits vor dem Auslösen der Exception ausgegeben.', '2026-05-18 23:16:08', NULL),
(40, 10, 4, 'A ValueError C', 0, 'Falsch. Die ValueError wird abgefangen und erscheint deshalb nicht als ungefangener Fehler in der Ausgabe.', '2026-05-18 23:16:08', NULL);

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
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `departments`
--

INSERT INTO `departments` (`id`, `parent_id`, `name`, `display_name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, NULL, 'it', 'IT', 'Hauptbereich für IT-Umschulungen und IT-nahe Kurse.', 1, '2026-05-19 00:00:00', NULL),
(2, NULL, 'care', 'Pflege', 'Hauptbereich für Pflege-Umschulungen und pflegebezogene Kurse.', 1, '2026-05-19 00:00:00', NULL),
(3, 1, 'application_development', 'Anwendungsentwicklung', 'Unterbereich der IT für Fachinformatiker(innen) in der Anwendungsentwicklung.', 1, '2026-05-19 00:00:00', '2026-05-19 14:05:15'),
(4, 1, 'system_integration', 'Systemintegration', 'Unterbereich der IT für Fachinformatiker(innen) in der Systemintegration.', 1, '2026-05-19 00:00:00', '2026-05-19 14:05:30');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `questions`
--

CREATE TABLE `questions` (
  `id` int(10) UNSIGNED NOT NULL,
  `question_pool_id` int(10) UNSIGNED NOT NULL,
  `question_text` text NOT NULL,
  `explanation` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ;

--
-- Daten für Tabelle `questions`
--

INSERT INTO `questions` (`id`, `question_pool_id`, `question_text`, `explanation`, `created_by`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Which import statement loads the entire math module so that all its functions must be accessed using the module name as a prefix?', 'Korrekt ist die Variante, bei der das komplette math-Modul unter dem Namen math eingebunden wird.', 1, 1, '2026-05-18 23:16:07', '2026-05-19 01:37:20'),
(2, 1, 'Consider the following code. Which statement about isinstance() and inheritance is incorrect?\r\n\r\nclass Animal:\r\n    pass\r\n\r\nclass Dog(Animal):\r\n    pass\r\n\r\nd = Dog()', 'Gesucht ist die fachlich falsche Aussage über isinstance() und Vererbung.', 1, 1, '2026-05-18 23:16:07', '2026-05-19 01:37:20'),
(3, 1, 'Which two statements about names starting with underscores in classes are correct?', 'Diese Frage prüft Python-Konventionen zu nicht öffentlichen Namen und Name Mangling.', 1, 1, '2026-05-18 23:16:07', '2026-05-19 01:37:20'),
(4, 1, 'Which of the following statements about combining map() and filter() are correct? (Choose three)', 'Diese Frage prüft die Kombination von map() und filter() sowie deren Rückgabewerte in Python 3.', 1, 1, '2026-05-18 23:16:07', '2026-05-19 01:37:20'),
(5, 1, 'Which two statements about instance attributes initialized in __init__ are correct?', 'Diese Frage prüft, wie Instanzattribute in Python über self erzeugt und gespeichert werden.', 1, 1, '2026-05-18 23:16:07', '2026-05-19 01:37:20'),
(6, 1, 'What value does the variable __name__ hold when a Python module is executed directly as the main program, for example python script.py?', 'Diese Frage prüft das Python-Standardmuster if __name__ == \"__main__\".', 1, 1, '2026-05-18 23:16:07', '2026-05-19 01:37:20'),
(7, 1, 'Consider the following code. Which of the following statements about this code are correct?\r\n\r\nclass Config:\r\n    debug = False\r\n\r\nclass AppConfig(Config):\r\n    debug = True\r\n\r\nac = AppConfig()\r\nprint(ac.debug)', 'Diese Frage prüft die Reihenfolge der Attributauflösung bei Instanzen, Klassen und Basisklassen.', 1, 1, '2026-05-18 23:16:07', '2026-05-19 01:37:20'),
(8, 1, 'Which two statements about instance methods are correct?', 'Diese Frage prüft den self-Parameter und den automatischen Methodenaufruf über eine Instanz.', 1, 1, '2026-05-18 23:16:08', '2026-05-19 01:37:20'),
(9, 1, 'What output does the following code produce?\r\n\r\nimport math\r\nprint(math.floor(-3.7))', 'Diese Frage prüft das Verhalten von math.floor() bei negativen Zahlen.', 1, 1, '2026-05-18 23:16:08', '2026-05-19 01:37:20'),
(10, 1, 'Consider the following code. What is printed?\r\n\r\ntry:\r\n    print(\"A\", end=\" \")\r\n    raise ValueError(\"bad\")\r\nexcept ValueError:\r\n    print(\"B\", end=\" \")\r\nfinally:\r\n    print(\"C\")', 'Diese Frage prüft die Ausführungsreihenfolge von try, except und finally.', 1, 1, '2026-05-18 23:16:08', '2026-05-19 01:37:20');

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
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ;

--
-- Daten für Tabelle `question_pools`
--

INSERT INTO `question_pools` (`id`, `name`, `description`, `created_by`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'PCAP Python Grundlagen Testpool', 'Test-Fragenpool mit zehn PCAP-Fragen aus dem Fragenkatalog. Enthält Multiple-Select-Fragen mit exakt vier Antwortmöglichkeiten und deutschen Erklärungen.', 1, 1, '2026-05-18 23:16:07', '2026-05-19 01:37:20');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `question_pool_departments`
--

CREATE TABLE `question_pool_departments` (
  `question_pool_id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `question_pool_departments`
--

INSERT INTO `question_pool_departments` (`question_pool_id`, `department_id`, `created_at`) VALUES
(1, 3, '2026-05-19 00:00:00'),
(1, 4, '2026-05-19 00:00:00');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(30) NOT NULL,
  `display_name` varchar(50) NOT NULL
) ;

--
-- Daten für Tabelle `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`) VALUES
(1, 'student', 'Schüler'),
(2, 'teacher', 'Dozent'),
(3, 'admin', 'Administrator');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `avatar` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `role_id`, `department_id`, `username`, `email`, `password_hash`, `avatar`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, NULL, 'admin', 'admin@damago.learn', '$2y$12$zZ4IIz1ewDWWMfFi0DeQtekSd2.111XdcntHfbWGgWXAT4rEGWmre', NULL, 1, '2026-05-19 01:29:09', '2026-05-19 01:29:52'),
(2, 2, 3, 'Kevin_hoeing', 'Kevin_hoeing@damago.learn', '$2y$12$nRN1d0ZF9EhgWCBwBJduYeLNM8r4y69hQbhWEMDgponqT17LlF3Da', NULL, 1, '2026-05-19 01:29:09', '2026-05-19 11:59:36'),
(3, 1, 3, 'Paul_Schulte', 'Paul_Schulte@damago.learn', '$2y$12$.H39CvtqzhybIbH8suasXOwsqIYHQVK48aJi1vyVXtRzyUG.AQKoO', NULL, 1, '2026-05-19 01:29:09', '2026-05-19 11:59:36'),
(4, 1, 3, 'Marcin_Banaszkiewicz', 'Marcin_Banaszkiewicz@damago.learn', '$2y$12$sC82J9LRjI.ye9RV1yScAOShFWkRLCDfuKmmYgRdrhfTGU35lYPOq', NULL, 1, '2026-05-19 01:29:09', '2026-05-19 11:59:36'),
(5, 1, 3, 'Pascal_Arndt', 'Pascal_Arndt@damago.learn', '$2y$12$FH3SZiEtOqcXsb1bo6QqmeYIlhS.m0.vw449lXXRlx01k/nTaEthC', NULL, 1, '2026-05-19 01:29:09', '2026-05-19 11:59:36');

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
  ADD KEY `idx_answer_options_is_correct` (`is_correct`);

--
-- Indizes für die Tabelle `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_departments_name` (`name`),
  ADD KEY `idx_departments_parent_id` (`parent_id`),
  ADD KEY `idx_departments_is_active` (`is_active`);

--
-- Indizes für die Tabelle `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_questions_question_pool_id` (`question_pool_id`),
  ADD KEY `idx_questions_created_by` (`created_by`),
  ADD KEY `idx_questions_is_active` (`is_active`);

--
-- Indizes für die Tabelle `question_pools`
--
ALTER TABLE `question_pools`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_question_pools_created_by` (`created_by`),
  ADD KEY `idx_question_pools_name` (`name`);

--
-- Indizes für die Tabelle `question_pool_departments`
--
ALTER TABLE `question_pool_departments`
  ADD PRIMARY KEY (`question_pool_id`,`department_id`),
  ADD KEY `idx_question_pool_departments_department_id` (`department_id`);

--
-- Indizes für die Tabelle `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_roles_name` (`name`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_username` (`username`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_role_id` (`role_id`),
  ADD KEY `idx_users_department_id` (`department_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `answer_options`
--
ALTER TABLE `answer_options`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `question_pools`
--
ALTER TABLE `question_pools`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `answer_options`
--
ALTER TABLE `answer_options`
  ADD CONSTRAINT `fk_answer_options_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `fk_departments_parent` FOREIGN KEY (`parent_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_questions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_questions_question_pool` FOREIGN KEY (`question_pool_id`) REFERENCES `question_pools` (`id`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `question_pools`
--
ALTER TABLE `question_pools`
  ADD CONSTRAINT `fk_question_pools_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `question_pool_departments`
--
ALTER TABLE `question_pool_departments`
  ADD CONSTRAINT `fk_qpd_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_qpd_question_pool` FOREIGN KEY (`question_pool_id`) REFERENCES `question_pools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
