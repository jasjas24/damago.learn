-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 01. Jun 2026 um 12:03
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `damago_quiz`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `damago_quiz`;

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
(45, 12, 1, '42', 1, 'Richtig. 6 × 7 = 42.', 1, '2026-05-26 10:00:00', NULL, NULL),
(46, 12, 2, '36', 0, 'Falsch. 36 ist das Ergebnis von 6 × 6.', 1, '2026-05-26 10:00:00', NULL, NULL),
(47, 12, 3, '48', 0, 'Falsch. 48 ist das Ergebnis von 6 × 8.', 1, '2026-05-26 10:00:00', NULL, NULL),
(48, 12, 4, '35', 0, 'Falsch. 35 ist das Ergebnis von 5 × 7.', 1, '2026-05-26 10:00:00', NULL, NULL),
(49, 13, 1, '72', 1, 'Richtig. 8 × 9 = 72.', 1, '2026-05-26 10:00:00', NULL, NULL),
(50, 13, 2, '63', 0, 'Falsch. 63 ist das Ergebnis von 7 × 9.', 1, '2026-05-26 10:00:00', NULL, NULL),
(51, 13, 3, '81', 0, 'Falsch. 81 ist das Ergebnis von 9 × 9.', 1, '2026-05-26 10:00:00', NULL, NULL),
(52, 13, 4, '64', 0, 'Falsch. 64 ist das Ergebnis von 8 × 8.', 1, '2026-05-26 10:00:00', NULL, NULL),
(53, 14, 1, '20', 1, 'Richtig. 4 × 5 = 20.', 1, '2026-05-26 10:00:00', NULL, NULL),
(54, 14, 2, '16', 0, 'Falsch. 16 ist das Ergebnis von 4 × 4.', 1, '2026-05-26 10:00:00', NULL, NULL),
(55, 14, 3, '25', 0, 'Falsch. 25 ist das Ergebnis von 5 × 5.', 1, '2026-05-26 10:00:00', NULL, NULL),
(56, 14, 4, '18', 0, 'Falsch. 18 ist das Ergebnis von 3 × 6 oder 2 × 9.', 1, '2026-05-26 10:00:00', NULL, NULL),
(57, 15, 1, '56', 1, 'Richtig. 7 × 8 = 56.', 1, '2026-05-26 10:00:00', NULL, NULL),
(58, 15, 2, '49', 0, 'Falsch. 49 ist das Ergebnis von 7 × 7.', 1, '2026-05-26 10:00:00', NULL, NULL),
(59, 15, 3, '64', 0, 'Falsch. 64 ist das Ergebnis von 8 × 8.', 1, '2026-05-26 10:00:00', NULL, NULL),
(60, 15, 4, '54', 0, 'Falsch. 54 ist das Ergebnis von 6 × 9.', 1, '2026-05-26 10:00:00', NULL, NULL),
(61, 16, 1, '54', 1, 'Richtig. 9 × 6 = 54.', 1, '2026-05-26 10:00:00', NULL, NULL),
(62, 16, 2, '63', 0, 'Falsch. 63 ist das Ergebnis von 9 × 7.', 1, '2026-05-26 10:00:00', NULL, NULL),
(63, 16, 3, '48', 0, 'Falsch. 48 ist das Ergebnis von 6 × 8.', 1, '2026-05-26 10:00:00', NULL, NULL),
(64, 16, 4, '45', 0, 'Falsch. 45 ist das Ergebnis von 9 × 5.', 1, '2026-05-26 10:00:00', NULL, NULL),
(65, 17, 1, '25', 1, 'Richtig. 5 × 5 = 25. Quadratzahl der 5.', 1, '2026-05-26 10:00:00', NULL, NULL),
(66, 17, 2, '20', 0, 'Falsch. 20 ist das Ergebnis von 4 × 5.', 1, '2026-05-26 10:00:00', NULL, NULL),
(67, 17, 3, '30', 0, 'Falsch. 30 ist das Ergebnis von 5 × 6.', 1, '2026-05-26 10:00:00', NULL, NULL),
(68, 17, 4, '10', 0, 'Falsch. 10 ist das Ergebnis von 5 × 2.', 1, '2026-05-26 10:00:00', NULL, NULL),
(69, 18, 1, '27', 1, 'Richtig. 3 × 9 = 27.', 1, '2026-05-26 10:00:00', NULL, NULL),
(70, 18, 2, '21', 0, 'Falsch. 21 ist das Ergebnis von 3 × 7.', 1, '2026-05-26 10:00:00', NULL, NULL),
(71, 18, 3, '24', 0, 'Falsch. 24 ist das Ergebnis von 3 × 8.', 1, '2026-05-26 10:00:00', NULL, NULL),
(72, 18, 4, '30', 0, 'Falsch. 30 ist das Ergebnis von 3 × 10.', 1, '2026-05-26 10:00:00', NULL, NULL),
(73, 19, 1, '36', 1, 'Richtig. 6 × 6 = 36. Quadratzahl der 6.', 1, '2026-05-26 10:00:00', NULL, NULL),
(74, 19, 2, '30', 0, 'Falsch. 30 ist das Ergebnis von 5 × 6.', 1, '2026-05-26 10:00:00', NULL, NULL),
(75, 19, 3, '42', 0, 'Falsch. 42 ist das Ergebnis von 6 × 7.', 1, '2026-05-26 10:00:00', NULL, NULL),
(76, 19, 4, '32', 0, 'Falsch. 32 ist das Ergebnis von 4 × 8.', 1, '2026-05-26 10:00:00', NULL, NULL),
(77, 20, 1, '28', 1, 'Richtig. 7 × 4 = 28.', 1, '2026-05-26 10:00:00', NULL, NULL),
(78, 20, 2, '21', 0, 'Falsch. 21 ist das Ergebnis von 7 × 3.', 1, '2026-05-26 10:00:00', NULL, NULL),
(79, 20, 3, '32', 0, 'Falsch. 32 ist das Ergebnis von 4 × 8.', 1, '2026-05-26 10:00:00', NULL, NULL),
(80, 20, 4, '24', 0, 'Falsch. 24 ist das Ergebnis von 4 × 6.', 1, '2026-05-26 10:00:00', NULL, NULL),
(81, 21, 1, 'Rot', 1, 'Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.', 1, '2026-05-26 12:00:00', NULL, NULL),
(82, 21, 2, 'Gelb', 1, 'Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.', 1, '2026-05-26 12:00:00', NULL, NULL),
(83, 21, 3, 'Grün', 1, 'Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.', 1, '2026-05-26 12:00:00', NULL, NULL),
(84, 21, 4, 'Lila', 0, 'Falsch. Lila gibt es an einer Ampel nicht.', 1, '2026-05-26 12:00:00', NULL, NULL),
(85, 22, 1, 'Fliegen', 1, 'Richtig! Die meisten Vögel können fliegen.', 1, '2026-05-26 12:00:00', NULL, NULL),
(86, 22, 2, 'Singen', 1, 'Richtig! Viele Vögel machen schöne Gesänge.', 1, '2026-05-26 12:00:00', NULL, NULL),
(87, 22, 3, 'Bellen', 0, 'Falsch. Bellen machen Hunde, keine Vögel.', 1, '2026-05-26 12:00:00', NULL, NULL),
(88, 22, 4, 'Miauen', 0, 'Falsch. Miauen machen Katzen, keine Vögel.', 1, '2026-05-26 12:00:00', NULL, NULL),
(89, 23, 1, 'Schmetterling', 1, 'Richtig! Schmetterlinge haben bunte Flügel.', 1, '2026-05-26 12:00:00', NULL, NULL),
(90, 23, 2, 'Vogel', 1, 'Richtig! Vögel haben Flügel zum Fliegen.', 1, '2026-05-26 12:00:00', NULL, NULL),
(91, 23, 3, 'Hund', 0, 'Falsch. Hunde haben keine Flügel.', 1, '2026-05-26 12:00:00', NULL, NULL),
(92, 23, 4, 'Fisch', 0, 'Falsch. Fische haben Flossen, aber keine Flügel.', 1, '2026-05-26 12:00:00', NULL, NULL),
(93, 24, 1, 'Wasser', 1, 'Richtig! Ohne Wasser geht eine Pflanze ein.', 1, '2026-05-26 12:00:00', NULL, NULL),
(94, 24, 2, 'Sonne', 1, 'Richtig! Pflanzen brauchen Licht zum Wachsen.', 1, '2026-05-26 12:00:00', NULL, NULL),
(95, 24, 3, 'Erde', 1, 'Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.', 1, '2026-05-26 12:00:00', NULL, NULL),
(96, 24, 4, 'Fernseher', 0, 'Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.', 1, '2026-05-26 12:00:00', NULL, NULL),
(97, 25, 1, '1', 1, 'Richtig! 1 ist kleiner als 5.', 1, '2026-05-26 12:00:00', NULL, NULL),
(98, 25, 2, '3', 1, 'Richtig! 3 ist kleiner als 5.', 1, '2026-05-26 12:00:00', NULL, NULL),
(99, 25, 3, '6', 0, 'Falsch. 6 ist größer als 5.', 1, '2026-05-26 12:00:00', NULL, NULL),
(100, 25, 4, '8', 0, 'Falsch. 8 ist größer als 5.', 1, '2026-05-26 12:00:00', NULL, NULL),
(101, 26, 1, 'Schwimmen', 1, 'Richtig! Schwimmen ist die Hauptsache im Schwimmbad.', 1, '2026-05-26 12:00:00', NULL, NULL),
(102, 26, 2, 'Tauchen', 1, 'Richtig! Unter Wasser tauchen macht viel Spaß.', 1, '2026-05-26 12:00:00', NULL, NULL),
(103, 26, 3, 'Kochen', 0, 'Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.', 1, '2026-05-26 12:00:00', NULL, NULL),
(104, 26, 4, 'Schlafen', 0, 'Falsch. Im Schwimmbad schläft man nicht – man plantscht!', 1, '2026-05-26 12:00:00', NULL, NULL),
(105, 27, 1, 'Sonne', 1, 'Richtig! Die Sonne ist das hellste Licht am Himmel.', 1, '2026-05-26 12:00:00', NULL, NULL),
(106, 27, 2, 'Lampe', 1, 'Richtig! Eine Lampe leuchtet wenn man sie einschaltet.', 1, '2026-05-26 12:00:00', NULL, NULL),
(107, 27, 3, 'Taschenlampe', 1, 'Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.', 1, '2026-05-26 12:00:00', NULL, NULL),
(108, 27, 4, 'Stein', 0, 'Falsch. Ein normaler Stein leuchtet nicht.', 1, '2026-05-26 12:00:00', NULL, NULL),
(109, 28, 1, 'Eis', 1, 'Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.', 1, '2026-05-26 12:00:00', NULL, NULL),
(110, 28, 2, 'Schnee', 1, 'Richtig! Schnee ist kalt – man braucht eine warme Jacke.', 1, '2026-05-26 12:00:00', NULL, NULL),
(111, 28, 3, 'Feuer', 0, 'Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!', 1, '2026-05-26 12:00:00', NULL, NULL),
(112, 28, 4, 'Sonne', 0, 'Falsch. Die Sonne wärmt uns, sie ist nicht kalt.', 1, '2026-05-26 12:00:00', NULL, NULL),
(113, 29, 1, 'Fisch', 1, 'Richtig! Fische leben im Wasser und können nicht an Land.', 1, '2026-05-26 12:00:00', NULL, NULL),
(114, 29, 2, 'Delfin', 1, 'Richtig! Delfine leben im Meer und sind sehr klug.', 1, '2026-05-26 12:00:00', NULL, NULL),
(115, 29, 3, 'Hund', 0, 'Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.', 1, '2026-05-26 12:00:00', NULL, NULL),
(116, 29, 4, 'Katze', 0, 'Falsch. Katzen mögen kein Wasser und leben an Land.', 1, '2026-05-26 12:00:00', NULL, NULL),
(117, 30, 1, 'Lesen', 1, 'Richtig! Bücher sind zum Lesen da.', 1, '2026-05-26 12:00:00', NULL, NULL),
(118, 30, 2, 'Anschauen', 1, 'Richtig! Besonders Bilderbücher schaut man gerne an.', 1, '2026-05-26 12:00:00', NULL, NULL),
(119, 30, 3, 'Essen', 0, 'Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!', 1, '2026-05-26 12:00:00', NULL, NULL),
(120, 30, 4, 'Trinken', 0, 'Falsch. Ein Buch kann man nicht trinken.', 1, '2026-05-26 12:00:00', NULL, NULL),
(133, 11, 1, '12', 1, 'Richtig. 3 × 4 = 12.', 1, '2026-06-01 11:11:27', NULL, NULL),
(134, 11, 2, '9', 0, 'Falsch. 9 wäre das Ergebnis von 3 × 3, nicht 3 × 4.', 1, '2026-06-01 11:11:27', NULL, NULL),
(135, 11, 3, '16', 0, 'Falsch. 16 ist das Ergebnis von 4 × 4.', 1, '2026-06-01 11:11:27', NULL, NULL),
(136, 11, 4, '11', 0, 'Falsch. 11 ist keine Zahl aus der Dreier- oder Vierreihe im kleinen Einmaleins.', 1, '2026-06-01 11:11:27', NULL, NULL),
(137, 10, 1, 'A B C', 1, 'Richtig. Der try-Block gibt zuerst A aus, die ValueError wird im except-Block behandelt und finally läuft immer am Ende.', 1, '2026-06-01 11:37:26', NULL, NULL),
(138, 10, 2, 'A C', 0, 'Falsch. Der except-Block wird ausgeführt, daher fehlt B nicht in der Ausgabe.', 1, '2026-06-01 11:37:26', NULL, NULL),
(139, 10, 3, 'B C', 0, 'Falsch. A wird bereits vor dem Auslösen der Exception ausgegeben.', 1, '2026-06-01 11:37:26', NULL, NULL),
(140, 10, 4, 'A ValueError C', 0, 'Falsch. Die ValueError wird abgefangen und erscheint deshalb nicht als ungefangener Fehler in der Ausgabe.', 1, '2026-06-01 11:37:26', NULL, NULL);

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
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `points` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `lobby_players`
--

INSERT INTO `lobby_players` (`id`, `lobby_id`, `player_name`, `joined_at`, `points`) VALUES
(1, 1, 'Gast', '2026-05-22 09:03:06', 0),
(2, 2, 'Gast', '2026-05-22 09:11:10', 0),
(3, 2, 'test', '2026-05-22 09:11:19', 0),
(4, 2, 'testspieler', '2026-05-22 09:14:14', 0),
(5, 3, 'testspieler', '2026-05-22 09:22:10', 0),
(6, 4, 'testspieler', '2026-05-22 09:23:21', 0),
(7, 5, 'Gast', '2026-05-22 09:24:29', 0),
(8, 5, 'testspieler', '2026-05-22 09:24:55', 0),
(9, 6, 'Gast', '2026-05-22 09:44:52', 0),
(10, 6, 'Gast-9543', '2026-05-22 09:45:28', 0),
(11, 6, 'test', '2026-05-22 09:50:22', 0),
(12, 7, 'test', '2026-05-22 09:51:26', 0),
(13, 8, 'test', '2026-05-22 10:55:25', 0),
(14, 8, 'Gast-2803', '2026-05-22 10:55:55', 0),
(15, 9, 'test', '2026-05-22 10:58:51', 0),
(16, 10, 'Gast', '2026-05-22 11:01:32', 0),
(17, 10, 'Gast-9358', '2026-05-22 11:01:54', 0),
(18, 11, 'test', '2026-05-22 11:18:10', 0),
(19, 12, 'Gast', '2026-05-22 11:37:52', 0),
(20, 13, 'Gast', '2026-05-22 11:53:05', 0),
(21, 13, 'Gast-6593', '2026-05-22 11:53:29', 0),
(22, 14, 'test', '2026-05-22 11:56:28', 0),
(23, 14, 'Gast-6914', '2026-05-22 11:56:53', 0),
(24, 15, 'Gast', '2026-05-22 11:57:50', 0),
(25, 15, 'Gast-5687', '2026-05-22 11:58:13', 0),
(26, 16, 'Gast', '2026-05-22 12:50:28', 0),
(27, 17, 'test', '2026-05-22 12:58:24', 0),
(28, 18, 'test', '2026-05-22 13:09:42', 0),
(29, 19, 'test', '2026-05-22 13:10:21', 0),
(30, 20, 'Gast', '2026-05-22 13:10:48', 0),
(31, 21, 'Gast', '2026-05-22 13:30:46', 0),
(32, 21, 'test', '2026-05-22 13:31:19', 0),
(33, 22, 'Gast', '2026-05-22 13:45:01', 0),
(34, 22, 'test', '2026-05-22 13:45:52', 0),
(35, 23, 'Gast', '2026-05-22 13:50:14', 0),
(36, 23, 'test', '2026-05-22 13:50:39', 0),
(37, 24, 'Gast', '2026-05-22 13:54:07', 0),
(38, 24, 'test', '2026-05-22 13:54:16', 0),
(39, 25, 'Gast', '2026-05-22 13:56:31', 0),
(40, 25, 'test', '2026-05-22 13:57:11', 0),
(41, 26, 'Gast', '2026-05-22 13:58:06', 0),
(42, 26, 'test', '2026-05-22 13:58:32', 0),
(43, 27, 'test', '2026-05-23 08:31:07', 0),
(44, 27, 'Gast-1887', '2026-05-23 08:31:34', 0),
(45, 28, 'Gast', '2026-05-23 08:37:18', 0),
(46, 28, 'Gast-9719', '2026-05-23 08:37:41', 0),
(47, 29, 'Gast', '2026-05-26 06:00:13', 0),
(48, 29, 'test', '2026-05-26 06:00:56', 0),
(49, 30, 'Gast', '2026-05-26 06:29:18', 0),
(50, 30, 'Gast-8292', '2026-05-26 06:29:45', 0),
(51, 31, 'Gast', '2026-05-26 06:30:50', 0),
(52, 31, 'Gast-7443', '2026-05-26 06:31:12', 0),
(53, 32, 'Gast', '2026-05-26 07:58:22', 0),
(54, 33, 'Gast', '2026-05-26 08:07:07', 0),
(55, 34, 'Gast', '2026-05-26 08:08:42', 0),
(56, 35, 'Gast', '2026-05-26 08:10:23', 0),
(57, 36, 'Gast', '2026-05-26 08:19:11', 2000),
(58, 37, 'Gast', '2026-05-26 08:50:31', 3000),
(59, 37, 'Gast-5151', '2026-05-26 08:50:46', 3000),
(60, 38, 'Gast', '2026-05-26 08:54:41', 0),
(61, 38, 'Gast-4856', '2026-05-26 08:54:48', 0),
(62, 38, 'Gast-5434', '2026-05-26 09:05:06', 0),
(63, 39, 'Gast', '2026-05-26 09:05:49', 0),
(64, 40, 'Gast', '2026-05-26 09:05:59', 0),
(65, 41, 'Gast', '2026-05-26 09:06:46', 0),
(66, 42, 'Gast', '2026-05-26 09:09:33', 0),
(67, 42, 'Gast-4050', '2026-05-26 09:10:26', 0),
(68, 42, 'Gast-2432', '2026-05-26 09:10:47', 0),
(69, 43, 'Gast', '2026-05-26 09:12:40', 1000),
(70, 43, 'Gast-4701', '2026-05-26 09:12:46', 2000),
(71, 44, 'Gast', '2026-05-26 09:28:51', 4000),
(72, 45, 'Gast', '2026-05-26 09:59:01', 1000),
(73, 46, 'Gast', '2026-05-26 10:00:10', 7000),
(74, 47, 'Gast', '2026-05-26 10:05:21', 0),
(75, 48, 'Gast', '2026-05-26 10:05:49', 0),
(76, 49, 'Gast', '2026-05-26 10:06:08', 3000),
(77, 50, 'Gast', '2026-05-26 10:10:18', 0),
(78, 50, 'test', '2026-05-26 10:10:58', 0),
(79, 51, 'Gast', '2026-05-26 10:11:41', 0),
(80, 51, 'test', '2026-05-26 10:11:53', 0),
(81, 52, 'Gast', '2026-05-26 10:12:38', 0),
(82, 53, 'Gast', '2026-05-26 10:13:00', 1000),
(83, 54, 'Gast', '2026-05-26 10:51:40', 0),
(84, 54, 'test', '2026-05-26 10:51:51', 0),
(85, 54, 'Gast-3103', '2026-05-26 10:57:26', 0),
(86, 55, 'Gast', '2026-05-26 10:57:56', 0),
(87, 55, 'test', '2026-05-26 10:58:08', 0),
(88, 56, 'Gast', '2026-05-26 11:03:46', 0),
(89, 56, 'test', '2026-05-26 11:04:10', 0),
(90, 57, 'Gast', '2026-05-26 11:06:06', 0),
(91, 57, 'test', '2026-05-26 11:06:21', 0),
(92, 58, 'Gast', '2026-05-26 11:09:05', 0),
(93, 58, 'test', '2026-05-26 11:09:26', 0),
(94, 59, 'Gast', '2026-05-26 11:09:59', 0),
(95, 59, 'test', '2026-05-26 11:10:15', 0),
(96, 60, 'Gast', '2026-05-26 11:13:58', 0),
(97, 60, 'test', '2026-05-26 11:14:22', 0),
(98, 61, 'Gast', '2026-05-26 11:15:12', 2000),
(99, 61, 'Gast-2111 (Gast)', '2026-05-26 11:15:52', 2000),
(100, 62, 'test', '2026-05-26 11:17:24', 0),
(101, 62, 'Gast-4675 (Gast)', '2026-05-26 11:17:31', 500),
(102, 63, 'test', '2026-05-26 11:21:39', 0),
(103, 63, 'Gast-9767', '2026-05-26 11:22:01', 1000),
(104, 64, 'Gast', '2026-05-26 11:37:06', 2000),
(105, 64, 'test', '2026-05-26 11:37:13', 1000),
(106, 65, 'Gast', '2026-05-26 11:55:22', 500),
(107, 65, 'Gast-9754', '2026-05-26 11:55:35', 500),
(108, 66, 'Gast', '2026-05-26 11:56:44', 0),
(109, 66, 'test', '2026-05-26 11:57:07', 0),
(110, 67, 'Gast', '2026-05-26 12:00:53', 0),
(111, 68, 'Gast', '2026-05-26 12:12:11', 0),
(112, 69, 'Gast', '2026-05-26 12:13:07', 0),
(113, 70, 'Gast', '2026-05-26 12:15:05', 0),
(114, 70, 'Gast-3064', '2026-05-26 12:15:11', 0),
(115, 71, 'test', '2026-05-26 12:16:23', 0),
(116, 71, 'Gast-7915', '2026-05-26 12:16:46', 3000),
(117, 72, 'Gast', '2026-05-26 12:48:03', 0),
(118, 72, 'Gast-5971', '2026-05-26 12:48:11', 0),
(119, 73, 'Gast', '2026-05-26 12:48:57', 0),
(120, 73, 'Gast-6232', '2026-05-26 12:49:05', 0),
(121, 74, 'Gast', '2026-05-26 12:50:20', 2000),
(122, 74, 'Gast-3089', '2026-05-26 12:50:27', 1500),
(123, 75, 'Gast', '2026-05-26 12:53:06', 1500),
(124, 75, 'test', '2026-05-26 12:53:12', 1000),
(125, 76, 'Gast', '2026-05-26 13:08:05', 1000),
(126, 76, 'test', '2026-05-26 13:08:12', 1500),
(127, 77, 'Gast', '2026-05-26 13:16:22', 0),
(128, 78, 'Gast', '2026-05-26 13:18:08', 0),
(129, 79, 'Gast', '2026-05-27 07:30:47', 0),
(130, 80, 'Gast', '2026-05-27 07:31:53', 1000),
(131, 81, 'admin', '2026-05-27 09:09:00', 0),
(132, 82, 'Gast', '2026-05-27 09:11:32', 0),
(133, 83, 'admin', '2026-05-27 10:00:14', 0),
(134, 84, 'admin', '2026-05-27 10:00:27', 0),
(135, 85, 'admin', '2026-05-27 10:06:18', 0),
(136, 86, 'Gast', '2026-05-27 11:19:25', 1000),
(137, 87, 'Gast', '2026-05-27 11:21:30', 0),
(138, 88, 'Ralf_Reichts', '2026-05-28 07:53:10', 2000),
(139, 89, 'Gast', '2026-05-28 10:01:37', 0),
(140, 90, 'Ralf_Reichts', '2026-05-28 10:03:31', 0),
(141, 91, 'admin', '2026-05-28 13:05:01', 0),
(142, 91, 'Volker', '2026-05-28 13:05:46', 0),
(143, 92, 'Gast', '2026-05-28 13:09:22', 1500),
(144, 93, 'Gast', '2026-05-28 14:04:42', 1000),
(145, 94, 'admin', '2026-05-28 14:12:15', 1000),
(146, 95, 'Gast', '2026-05-28 14:13:30', 0),
(147, 96, 'Gast', '2026-05-29 11:05:02', 1000),
(148, 97, 'Gast', '2026-05-29 11:45:09', 3000),
(149, 98, 'Gast', '2026-05-29 14:05:22', 0),
(150, 99, 'Gast', '2026-06-01 06:14:13', 1000),
(151, 100, 'Gast', '2026-06-01 09:37:45', 0);

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
(80, 20, 2, 9),
(81, 21, 10, 0),
(82, 21, 9, 1),
(83, 21, 7, 2),
(84, 21, 4, 3),
(85, 21, 6, 4),
(86, 21, 1, 5),
(87, 21, 2, 6),
(88, 21, 8, 7),
(89, 21, 5, 8),
(90, 21, 3, 9),
(91, 22, 7, 0),
(92, 22, 4, 1),
(93, 22, 8, 2),
(94, 22, 6, 3),
(95, 22, 9, 4),
(96, 22, 10, 5),
(97, 22, 5, 6),
(98, 22, 3, 7),
(99, 22, 1, 8),
(100, 22, 2, 9),
(101, 23, 9, 0),
(102, 23, 6, 1),
(103, 23, 8, 2),
(104, 23, 5, 3),
(105, 23, 4, 4),
(106, 23, 3, 5),
(107, 23, 10, 6),
(108, 23, 2, 7),
(109, 23, 1, 8),
(110, 23, 7, 9),
(111, 24, 2, 0),
(112, 24, 7, 1),
(113, 24, 5, 2),
(114, 24, 8, 3),
(115, 24, 3, 4),
(116, 24, 9, 5),
(117, 24, 1, 6),
(118, 24, 4, 7),
(119, 24, 10, 8),
(120, 24, 6, 9),
(121, 25, 1, 0),
(122, 25, 5, 1),
(123, 25, 9, 2),
(124, 25, 4, 3),
(125, 25, 7, 4),
(126, 25, 8, 5),
(127, 25, 2, 6),
(128, 25, 10, 7),
(129, 25, 6, 8),
(130, 25, 3, 9),
(131, 26, 2, 0),
(132, 26, 3, 1),
(133, 26, 9, 2),
(134, 26, 10, 3),
(135, 26, 8, 4),
(136, 26, 5, 5),
(137, 26, 7, 6),
(138, 26, 1, 7),
(139, 26, 4, 8),
(140, 26, 6, 9),
(141, 27, 1, 0),
(142, 27, 6, 1),
(143, 27, 7, 2),
(144, 27, 4, 3),
(145, 27, 8, 4),
(146, 27, 2, 5),
(147, 27, 5, 6),
(148, 27, 9, 7),
(149, 27, 3, 8),
(150, 27, 10, 9),
(151, 28, 5, 0),
(152, 28, 8, 1),
(153, 28, 1, 2),
(154, 28, 6, 3),
(155, 28, 3, 4),
(156, 28, 4, 5),
(157, 28, 2, 6),
(158, 28, 7, 7),
(159, 28, 10, 8),
(160, 28, 9, 9),
(161, 29, 3, 0),
(162, 29, 7, 1),
(163, 29, 4, 2),
(164, 29, 9, 3),
(165, 29, 6, 4),
(166, 29, 2, 5),
(167, 29, 8, 6),
(168, 29, 5, 7),
(169, 29, 1, 8),
(170, 29, 10, 9),
(171, 31, 6, 0),
(172, 31, 8, 1),
(173, 31, 9, 2),
(174, 31, 5, 3),
(175, 31, 3, 4),
(176, 31, 10, 5),
(177, 31, 2, 6),
(178, 31, 1, 7),
(179, 31, 7, 8),
(180, 31, 4, 9),
(181, 32, 2, 0),
(182, 32, 9, 1),
(183, 32, 7, 2),
(184, 32, 10, 3),
(185, 32, 1, 4),
(186, 32, 3, 5),
(187, 32, 6, 6),
(188, 32, 8, 7),
(189, 32, 5, 8),
(190, 32, 4, 9),
(191, 33, 8, 0),
(192, 33, 2, 1),
(193, 33, 4, 2),
(194, 33, 10, 3),
(195, 33, 1, 4),
(196, 33, 9, 5),
(197, 33, 3, 6),
(198, 33, 7, 7),
(199, 33, 5, 8),
(200, 33, 6, 9),
(201, 34, 7, 0),
(202, 34, 1, 1),
(203, 34, 4, 2),
(204, 34, 5, 3),
(205, 34, 6, 4),
(206, 34, 9, 5),
(207, 34, 3, 6),
(208, 34, 2, 7),
(209, 34, 8, 8),
(210, 34, 10, 9),
(211, 35, 4, 0),
(212, 35, 2, 1),
(213, 35, 3, 2),
(214, 35, 5, 3),
(215, 35, 9, 4),
(216, 35, 1, 5),
(217, 35, 10, 6),
(218, 35, 7, 7),
(219, 35, 6, 8),
(220, 35, 8, 9),
(221, 36, 6, 0),
(222, 36, 1, 1),
(223, 36, 2, 2),
(224, 36, 7, 3),
(225, 36, 4, 4),
(226, 36, 10, 5),
(227, 36, 8, 6),
(228, 36, 3, 7),
(229, 36, 9, 8),
(230, 36, 5, 9),
(231, 37, 8, 0),
(232, 37, 6, 1),
(233, 37, 3, 2),
(234, 37, 7, 3),
(235, 37, 2, 4),
(236, 37, 5, 5),
(237, 37, 10, 6),
(238, 37, 9, 7),
(239, 37, 1, 8),
(240, 37, 4, 9),
(241, 42, 3, 0),
(242, 42, 1, 1),
(243, 42, 10, 2),
(244, 42, 9, 3),
(245, 42, 4, 4),
(246, 42, 7, 5),
(247, 42, 6, 6),
(248, 42, 8, 7),
(249, 42, 2, 8),
(250, 42, 5, 9),
(251, 43, 1, 0),
(252, 43, 2, 1),
(253, 43, 4, 2),
(254, 43, 5, 3),
(255, 43, 8, 4),
(256, 43, 10, 5),
(257, 43, 3, 6),
(258, 43, 9, 7),
(259, 43, 7, 8),
(260, 43, 6, 9),
(261, 44, 17, 0),
(262, 44, 13, 1),
(263, 44, 15, 2),
(264, 44, 18, 3),
(265, 44, 11, 4),
(266, 44, 19, 5),
(267, 44, 16, 6),
(268, 44, 14, 7),
(269, 44, 20, 8),
(270, 44, 12, 9),
(271, 45, 17, 0),
(272, 45, 16, 1),
(273, 45, 19, 2),
(274, 45, 18, 3),
(275, 45, 14, 4),
(276, 45, 15, 5),
(277, 45, 11, 6),
(278, 45, 20, 7),
(279, 45, 12, 8),
(280, 45, 13, 9),
(281, 49, 28, 0),
(282, 49, 21, 1),
(283, 49, 29, 2),
(284, 49, 30, 3),
(285, 49, 25, 4),
(286, 49, 24, 5),
(287, 49, 26, 6),
(288, 49, 27, 7),
(289, 49, 22, 8),
(290, 49, 23, 9),
(291, 51, 26, 0),
(292, 51, 25, 1),
(293, 51, 29, 2),
(294, 51, 28, 3),
(295, 51, 21, 4),
(296, 51, 24, 5),
(297, 51, 27, 6),
(298, 51, 22, 7),
(299, 51, 30, 8),
(300, 51, 23, 9),
(301, 53, 30, 0),
(302, 53, 27, 1),
(303, 53, 24, 2),
(304, 53, 22, 3),
(305, 53, 26, 4),
(306, 53, 21, 5),
(307, 53, 28, 6),
(308, 53, 29, 7),
(309, 53, 25, 8),
(310, 53, 23, 9),
(311, 55, 4, 0),
(312, 55, 1, 1),
(313, 55, 2, 2),
(314, 55, 9, 3),
(315, 55, 6, 4),
(316, 55, 10, 5),
(317, 55, 7, 6),
(318, 55, 5, 7),
(319, 55, 8, 8),
(320, 55, 3, 9),
(321, 56, 21, 0),
(322, 56, 22, 1),
(323, 56, 23, 2),
(324, 56, 25, 3),
(325, 56, 30, 4),
(326, 56, 29, 5),
(327, 56, 26, 6),
(328, 56, 27, 7),
(329, 56, 24, 8),
(330, 56, 28, 9),
(331, 57, 2, 0),
(332, 57, 5, 1),
(333, 57, 7, 2),
(334, 57, 10, 3),
(335, 57, 4, 4),
(336, 57, 6, 5),
(337, 57, 8, 6),
(338, 57, 3, 7),
(339, 57, 9, 8),
(340, 57, 1, 9),
(341, 61, 18, 0),
(342, 61, 19, 1),
(343, 61, 13, 2),
(344, 61, 16, 3),
(345, 61, 14, 4),
(346, 61, 12, 5),
(347, 61, 17, 6),
(348, 61, 11, 7),
(349, 61, 20, 8),
(350, 61, 15, 9),
(351, 62, 21, 0),
(352, 62, 25, 1),
(353, 62, 23, 2),
(354, 62, 27, 3),
(355, 62, 22, 4),
(356, 62, 28, 5),
(357, 62, 29, 6),
(358, 62, 24, 7),
(359, 62, 30, 8),
(360, 62, 26, 9),
(361, 63, 22, 0),
(362, 63, 29, 1),
(363, 63, 30, 2),
(364, 63, 24, 3),
(365, 63, 28, 4),
(366, 63, 27, 5),
(367, 63, 21, 6),
(368, 63, 26, 7),
(369, 63, 23, 8),
(370, 63, 25, 9),
(371, 64, 26, 0),
(372, 64, 23, 1),
(373, 64, 28, 2),
(374, 64, 29, 3),
(375, 64, 24, 4),
(376, 64, 22, 5),
(377, 64, 30, 6),
(378, 64, 27, 7),
(379, 64, 25, 8),
(380, 64, 21, 9),
(381, 65, 24, 0),
(382, 65, 25, 1),
(383, 65, 26, 2),
(384, 65, 22, 3),
(385, 65, 29, 4),
(386, 65, 23, 5),
(387, 65, 28, 6),
(388, 65, 27, 7),
(389, 65, 21, 8),
(390, 65, 30, 9),
(391, 66, 23, 0),
(392, 66, 24, 1),
(393, 66, 29, 2),
(394, 66, 25, 3),
(395, 66, 22, 4),
(396, 66, 27, 5),
(397, 66, 28, 6),
(398, 66, 26, 7),
(399, 66, 30, 8),
(400, 66, 21, 9),
(401, 69, 2, 0),
(402, 69, 6, 1),
(403, 69, 10, 2),
(404, 69, 1, 3),
(405, 69, 9, 4),
(406, 69, 4, 5),
(407, 69, 3, 6),
(408, 69, 8, 7),
(409, 69, 7, 8),
(410, 69, 5, 9),
(411, 70, 17, 0),
(412, 70, 12, 1),
(413, 70, 20, 2),
(414, 70, 13, 3),
(415, 70, 15, 4),
(416, 70, 11, 5),
(417, 70, 14, 6),
(418, 70, 16, 7),
(419, 70, 19, 8),
(420, 70, 18, 9),
(421, 71, 15, 0),
(422, 71, 11, 1),
(423, 71, 13, 2),
(424, 71, 20, 3),
(425, 71, 17, 4),
(426, 71, 14, 5),
(427, 71, 18, 6),
(428, 71, 12, 7),
(429, 71, 16, 8),
(430, 71, 19, 9),
(431, 72, 30, 0),
(432, 72, 28, 1),
(433, 72, 26, 2),
(434, 72, 21, 3),
(435, 72, 27, 4),
(436, 72, 22, 5),
(437, 72, 24, 6),
(438, 72, 29, 7),
(439, 72, 23, 8),
(440, 72, 25, 9),
(441, 73, 30, 0),
(442, 73, 27, 1),
(443, 73, 26, 2),
(444, 73, 22, 3),
(445, 73, 21, 4),
(446, 73, 25, 5),
(447, 73, 24, 6),
(448, 73, 23, 7),
(449, 73, 29, 8),
(450, 73, 28, 9),
(451, 74, 23, 0),
(452, 74, 30, 1),
(453, 74, 26, 2),
(454, 74, 22, 3),
(455, 74, 24, 4),
(456, 74, 27, 5),
(457, 74, 29, 6),
(458, 74, 21, 7),
(459, 74, 28, 8),
(460, 74, 25, 9),
(461, 75, 24, 0),
(462, 75, 30, 1),
(463, 75, 26, 2),
(464, 75, 23, 3),
(465, 75, 22, 4),
(466, 75, 25, 5),
(467, 75, 28, 6),
(468, 75, 21, 7),
(469, 75, 27, 8),
(470, 75, 29, 9),
(471, 76, 23, 0),
(472, 76, 30, 1),
(473, 76, 21, 2),
(474, 76, 27, 3),
(475, 76, 24, 4),
(476, 76, 28, 5),
(477, 76, 29, 6),
(478, 76, 25, 7),
(479, 76, 26, 8),
(480, 76, 22, 9),
(481, 77, 21, 0),
(482, 77, 27, 1),
(483, 77, 25, 2),
(484, 77, 26, 3),
(485, 77, 30, 4),
(486, 77, 23, 5),
(487, 77, 24, 6),
(488, 77, 22, 7),
(489, 77, 28, 8),
(490, 77, 29, 9),
(491, 78, 26, 0),
(492, 78, 27, 1),
(493, 78, 30, 2),
(494, 78, 22, 3),
(495, 78, 29, 4),
(496, 78, 28, 5),
(497, 78, 24, 6),
(498, 78, 25, 7),
(499, 78, 21, 8),
(500, 78, 23, 9),
(501, 79, 29, 0),
(502, 79, 28, 1),
(503, 79, 25, 2),
(504, 79, 27, 3),
(505, 79, 30, 4),
(506, 79, 24, 5),
(507, 79, 26, 6),
(508, 79, 22, 7),
(509, 79, 23, 8),
(510, 79, 21, 9),
(511, 81, 21, 0),
(512, 81, 25, 1),
(513, 81, 22, 2),
(514, 81, 27, 3),
(515, 81, 30, 4),
(516, 81, 24, 5),
(517, 81, 26, 6),
(518, 81, 23, 7),
(519, 81, 28, 8),
(520, 81, 29, 9),
(521, 82, 30, 0),
(522, 82, 23, 1),
(523, 82, 28, 2),
(524, 82, 24, 3),
(525, 82, 22, 4),
(526, 82, 26, 5),
(527, 82, 27, 6),
(528, 82, 25, 7),
(529, 82, 29, 8),
(530, 82, 21, 9),
(531, 83, 21, 0),
(532, 83, 28, 1),
(533, 83, 23, 2),
(534, 83, 30, 3),
(535, 83, 29, 4),
(536, 83, 26, 5),
(537, 83, 22, 6),
(538, 83, 25, 7),
(539, 83, 24, 8),
(540, 83, 27, 9),
(541, 86, 17, 0),
(542, 86, 16, 1),
(543, 86, 13, 2),
(544, 86, 19, 3),
(545, 86, 11, 4),
(546, 86, 20, 5),
(547, 86, 18, 6),
(548, 86, 14, 7),
(549, 86, 15, 8),
(550, 86, 12, 9),
(551, 87, 18, 0),
(552, 87, 14, 1),
(553, 87, 17, 2),
(554, 87, 16, 3),
(555, 87, 20, 4),
(556, 87, 15, 5),
(557, 87, 11, 6),
(558, 87, 12, 7),
(559, 87, 19, 8),
(560, 87, 13, 9),
(561, 88, 21, 0),
(562, 88, 23, 1),
(563, 88, 27, 2),
(564, 89, 22, 0),
(565, 89, 21, 1),
(566, 89, 30, 2),
(567, 89, 25, 3),
(568, 89, 29, 4),
(569, 89, 23, 5),
(570, 89, 26, 6),
(571, 89, 27, 7),
(572, 89, 28, 8),
(573, 89, 24, 9),
(574, 90, 30, 0),
(575, 90, 23, 1),
(576, 90, 24, 2),
(577, 90, 25, 3),
(578, 90, 28, 4),
(579, 90, 22, 5),
(580, 90, 27, 6),
(581, 90, 26, 7),
(582, 90, 29, 8),
(583, 90, 21, 9),
(584, 91, 24, 0),
(585, 91, 27, 1),
(586, 91, 23, 2),
(587, 91, 22, 3),
(588, 91, 21, 4),
(589, 93, 15, 0),
(590, 93, 18, 1),
(591, 93, 11, 2),
(592, 93, 19, 3),
(593, 93, 16, 4),
(594, 93, 14, 5),
(595, 93, 12, 6),
(596, 93, 17, 7),
(597, 93, 13, 8),
(598, 93, 20, 9),
(599, 94, 26, 0),
(600, 94, 21, 1),
(601, 94, 30, 2),
(602, 94, 24, 3),
(603, 94, 29, 4),
(604, 94, 23, 5),
(605, 94, 27, 6),
(606, 94, 22, 7),
(607, 94, 25, 8),
(608, 94, 28, 9),
(609, 95, 22, 0),
(610, 96, 30, 0),
(611, 96, 29, 1),
(612, 96, 23, 2),
(613, 96, 21, 3),
(614, 96, 26, 4),
(615, 96, 24, 5),
(616, 96, 27, 6),
(617, 96, 28, 7),
(618, 96, 25, 8),
(619, 96, 22, 9),
(620, 97, 23, 0),
(621, 97, 24, 1),
(622, 97, 25, 2),
(623, 97, 22, 3),
(624, 97, 30, 4),
(625, 97, 27, 5),
(626, 97, 29, 6),
(627, 97, 21, 7),
(628, 97, 26, 8),
(629, 97, 28, 9),
(630, 98, 30, 0),
(631, 98, 23, 1),
(632, 98, 26, 2),
(633, 98, 22, 3),
(634, 98, 25, 4),
(635, 98, 21, 5),
(636, 98, 24, 6),
(637, 98, 29, 7),
(638, 98, 28, 8),
(639, 98, 27, 9),
(640, 99, 21, 0),
(641, 99, 25, 1),
(642, 100, 10, 0);

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
-- Tabellenstruktur für Tabelle `player_answers`
--

CREATE TABLE `player_answers` (
  `id` int(11) NOT NULL,
  `lobby_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `player_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `player_answers`
--

INSERT INTO `player_answers` (`id`, `lobby_id`, `question_id`, `player_name`) VALUES
(4, 22, 4, 'Gast'),
(3, 22, 4, 'test'),
(2, 22, 7, 'Gast'),
(1, 22, 7, 'test'),
(8, 23, 6, 'Gast'),
(7, 23, 6, 'test'),
(5, 23, 9, 'Gast'),
(6, 23, 9, 'test'),
(10, 25, 1, 'Gast'),
(9, 25, 1, 'test'),
(11, 25, 5, 'Gast'),
(12, 25, 5, 'test'),
(27, 26, 1, 'Gast'),
(28, 26, 1, 'test'),
(13, 26, 2, 'Gast'),
(14, 26, 2, 'test'),
(16, 26, 3, 'Gast'),
(15, 26, 3, 'test'),
(30, 26, 4, 'Gast'),
(29, 26, 4, 'test'),
(24, 26, 5, 'Gast'),
(23, 26, 5, 'test'),
(31, 26, 6, 'Gast'),
(32, 26, 6, 'test'),
(25, 26, 7, 'Gast'),
(26, 26, 7, 'test'),
(21, 26, 8, 'Gast'),
(22, 26, 8, 'test'),
(18, 26, 9, 'Gast'),
(17, 26, 9, 'test'),
(20, 26, 10, 'Gast'),
(19, 26, 10, 'test'),
(33, 27, 1, 'Gast'),
(34, 27, 1, 'Gast-1887'),
(35, 27, 6, 'Gast'),
(36, 27, 6, 'Gast-1887'),
(37, 28, 5, 'Gast'),
(38, 28, 5, 'Gast-9719'),
(39, 29, 3, 'Gast'),
(40, 29, 3, 'test'),
(41, 31, 6, 'Gast'),
(42, 31, 6, 'Gast-7443'),
(43, 31, 8, 'Gast'),
(44, 31, 8, 'Gast-7443'),
(45, 32, 2, 'Gast'),
(47, 32, 7, 'Gast'),
(46, 32, 9, 'Gast'),
(49, 33, 2, 'Gast'),
(50, 33, 4, 'Gast'),
(48, 33, 8, 'Gast'),
(51, 33, 10, 'Gast'),
(53, 34, 1, 'Gast'),
(52, 34, 7, 'Gast'),
(55, 35, 2, 'Gast'),
(56, 35, 3, 'Gast'),
(54, 35, 4, 'Gast'),
(57, 35, 5, 'Gast'),
(58, 35, 9, 'Gast'),
(60, 36, 1, 'Gast'),
(61, 36, 2, 'Gast'),
(66, 36, 3, 'Gast'),
(63, 36, 4, 'Gast'),
(59, 36, 6, 'Gast'),
(62, 36, 7, 'Gast'),
(65, 36, 8, 'Gast'),
(67, 36, 9, 'Gast'),
(64, 36, 10, 'Gast'),
(84, 37, 1, 'Gast'),
(85, 37, 1, 'Gast-5151'),
(76, 37, 2, 'Gast'),
(77, 37, 2, 'Gast-5151'),
(72, 37, 3, 'Gast'),
(73, 37, 3, 'Gast-5151'),
(87, 37, 4, 'Gast'),
(86, 37, 4, 'Gast-5151'),
(79, 37, 5, 'Gast'),
(78, 37, 5, 'Gast-5151'),
(71, 37, 6, 'Gast'),
(70, 37, 6, 'Gast-5151'),
(75, 37, 7, 'Gast'),
(74, 37, 7, 'Gast-5151'),
(69, 37, 8, 'Gast'),
(68, 37, 8, 'Gast-5151'),
(83, 37, 9, 'Gast'),
(82, 37, 9, 'Gast-5151'),
(80, 37, 10, 'Gast'),
(81, 37, 10, 'Gast-5151'),
(89, 42, 3, 'Gast'),
(88, 42, 3, 'Gast-2432'),
(90, 43, 1, 'Gast'),
(91, 43, 1, 'Gast-4701'),
(92, 43, 2, 'Gast'),
(93, 43, 2, 'Gast-4701'),
(104, 43, 3, 'Gast'),
(103, 43, 3, 'Gast-4701'),
(94, 43, 4, 'Gast'),
(95, 43, 4, 'Gast-4701'),
(97, 43, 5, 'Gast'),
(96, 43, 5, 'Gast-4701'),
(109, 43, 6, 'Gast'),
(110, 43, 6, 'Gast-4701'),
(107, 43, 7, 'Gast'),
(108, 43, 7, 'Gast-4701'),
(98, 43, 8, 'Gast'),
(99, 43, 8, 'Gast-4701'),
(105, 43, 9, 'Gast'),
(106, 43, 9, 'Gast-4701'),
(102, 43, 10, 'Gast'),
(101, 43, 10, 'Gast-4701'),
(115, 44, 11, 'Gast'),
(112, 44, 13, 'Gast'),
(113, 44, 15, 'Gast'),
(111, 44, 17, 'Gast'),
(114, 44, 18, 'Gast'),
(117, 45, 16, 'Gast'),
(116, 45, 17, 'Gast'),
(124, 46, 11, 'Gast'),
(126, 46, 12, 'Gast'),
(127, 46, 13, 'Gast'),
(122, 46, 14, 'Gast'),
(123, 46, 15, 'Gast'),
(119, 46, 16, 'Gast'),
(118, 46, 17, 'Gast'),
(121, 46, 18, 'Gast'),
(120, 46, 19, 'Gast'),
(125, 46, 20, 'Gast'),
(129, 49, 21, 'Gast'),
(133, 49, 24, 'Gast'),
(132, 49, 25, 'Gast'),
(128, 49, 28, 'Gast'),
(130, 49, 29, 'Gast'),
(131, 49, 30, 'Gast'),
(134, 51, 26, 'Gast'),
(136, 53, 27, 'Gast'),
(135, 53, 30, 'Gast'),
(137, 59, 2, 'Gast'),
(143, 61, 13, 'Gast'),
(142, 61, 13, 'Gast-2111 (Gast)'),
(138, 61, 18, 'Gast'),
(139, 61, 18, 'Gast-2111 (Gast)'),
(141, 61, 19, 'Gast'),
(140, 61, 19, 'Gast-2111 (Gast)'),
(144, 62, 21, 'Gast'),
(145, 62, 21, 'Gast-4675 (Gast)'),
(146, 62, 25, 'Gast'),
(147, 63, 22, 'Gast'),
(148, 63, 22, 'Gast-9767'),
(151, 64, 23, 'Gast'),
(152, 64, 23, 'test'),
(150, 64, 26, 'Gast'),
(149, 64, 26, 'test'),
(153, 64, 28, 'Gast'),
(154, 64, 28, 'test'),
(156, 64, 29, 'Gast'),
(155, 64, 29, 'test'),
(157, 65, 24, 'Gast'),
(158, 65, 24, 'Gast-9754'),
(159, 66, 23, 'Gast'),
(160, 66, 23, 'test'),
(161, 66, 24, 'Gast'),
(162, 66, 24, 'test'),
(163, 66, 29, 'Gast'),
(164, 66, 29, 'test'),
(165, 67, 23, 'Gast'),
(166, 68, 23, 'Gast'),
(167, 69, 2, 'Gast'),
(171, 71, 11, 'Gast'),
(170, 71, 11, 'Gast-7915'),
(173, 71, 13, 'Gast'),
(172, 71, 13, 'Gast-7915'),
(168, 71, 15, 'Gast'),
(169, 71, 15, 'Gast-7915'),
(177, 71, 17, 'Gast'),
(176, 71, 17, 'Gast-7915'),
(174, 71, 20, 'Gast'),
(175, 71, 20, 'Gast-7915'),
(178, 74, 23, 'Gast'),
(179, 74, 23, 'Gast-3089'),
(182, 74, 26, 'Gast'),
(183, 74, 26, 'Gast-3089'),
(180, 74, 30, 'Gast'),
(181, 74, 30, 'Gast-3089'),
(184, 75, 24, 'Gast'),
(185, 75, 24, 'test'),
(188, 75, 26, 'Gast'),
(189, 75, 26, 'test'),
(187, 75, 30, 'Gast'),
(186, 75, 30, 'test'),
(194, 76, 21, 'Gast'),
(195, 76, 21, 'test'),
(191, 76, 23, 'Gast'),
(190, 76, 23, 'test'),
(198, 76, 24, 'Gast'),
(199, 76, 24, 'test'),
(196, 76, 27, 'Gast'),
(197, 76, 27, 'test'),
(192, 76, 30, 'Gast'),
(193, 76, 30, 'test'),
(200, 77, 21, 'Gast'),
(201, 78, 26, 'Gast'),
(202, 78, 27, 'Gast'),
(204, 80, 28, 'Gast'),
(203, 80, 29, 'Gast'),
(205, 82, 30, 'Gast'),
(206, 86, 17, 'Gast'),
(207, 88, 21, 'Ralf_Reichts'),
(208, 88, 23, 'Ralf_Reichts'),
(209, 89, 22, 'Gast'),
(215, 90, 22, 'Gast'),
(211, 90, 23, 'Gast'),
(212, 90, 24, 'Gast'),
(213, 90, 25, 'Gast'),
(217, 90, 26, 'Gast'),
(216, 90, 27, 'Gast'),
(214, 90, 28, 'Gast'),
(210, 90, 30, 'Gast'),
(218, 91, 24, 'Gast'),
(219, 91, 24, 'Volker'),
(220, 91, 27, 'Gast'),
(221, 91, 27, 'Volker'),
(222, 92, 24, 'Gast'),
(223, 92, 27, 'Gast'),
(226, 93, 11, 'Gast'),
(224, 93, 15, 'Gast'),
(225, 93, 18, 'Gast'),
(227, 93, 19, 'Gast'),
(229, 94, 21, 'admin'),
(228, 94, 26, 'admin'),
(230, 95, 22, 'Gast'),
(231, 96, 30, 'Gast'),
(235, 97, 22, 'Gast'),
(232, 97, 23, 'Gast'),
(233, 97, 24, 'Gast'),
(234, 97, 25, 'Gast'),
(236, 97, 30, 'Gast'),
(237, 98, 30, 'Gast'),
(238, 99, 21, 'Gast'),
(239, 100, 10, 'Gast');

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
(1, 1, 'Which import statement loads the entire math module so that all its functions must be accessed using the module name as a prefix?', NULL, 'Korrekt ist die Variante, bei der das komplette math-Modul unter dem Namen math eingebunden wird.', 1, 0, '2026-05-18 23:16:07', '2026-06-01 11:36:50', 1),
(2, 1, 'Consider the following code. Which statement about isinstance() and inheritance is incorrect?\r\n\r\nclass Animal:\r\n    pass\r\n\r\nclass Dog(Animal):\r\n    pass\r\n\r\nd = Dog()', NULL, 'Gesucht ist die fachlich falsche Aussage über isinstance() und Vererbung.', 1, 0, '2026-05-18 23:16:07', '2026-06-01 11:36:51', 1),
(3, 1, 'Which two statements about names starting with underscores in classes are correct?', NULL, 'Diese Frage prüft Python-Konventionen zu nicht öffentlichen Namen und Name Mangling.', 1, 0, '2026-05-18 23:16:07', '2026-06-01 11:36:51', 1),
(4, 1, 'Which of the following statements about combining map() and filter() are correct? (Choose three)', NULL, 'Diese Frage prüft die Kombination von map() und filter() sowie deren Rückgabewerte in Python 3.', 1, 0, '2026-05-18 23:16:07', '2026-06-01 11:36:52', 1),
(5, 1, 'Which two statements about instance attributes initialized in __init__ are correct?', NULL, 'Diese Frage prüft, wie Instanzattribute in Python über self erzeugt und gespeichert werden.', 1, 0, '2026-05-18 23:16:07', '2026-06-01 11:36:52', 1),
(6, 1, 'What value does the variable __name__ hold when a Python module is executed directly as the main program, for example python script.py?', NULL, 'Diese Frage prüft das Python-Standardmuster if __name__ == \"__main__\".', 1, 0, '2026-05-18 23:16:07', '2026-06-01 11:36:53', 1),
(7, 1, 'Consider the following code. Which of the following statements about this code are correct?\r\n\r\nclass Config:\r\n    debug = False\r\n\r\nclass AppConfig(Config):\r\n    debug = True\r\n\r\nac = AppConfig()\r\nprint(ac.debug)', NULL, 'Diese Frage prüft die Reihenfolge der Attributauflösung bei Instanzen, Klassen und Basisklassen.', 1, 0, '2026-05-18 23:16:07', '2026-06-01 11:36:53', 1),
(8, 1, 'Which two statements about instance methods are correct?', NULL, 'Diese Frage prüft den self-Parameter und den automatischen Methodenaufruf über eine Instanz.', 1, 0, '2026-05-18 23:16:08', '2026-06-01 11:36:54', 1),
(9, 1, 'What output does the following code produce?\r\n\r\nimport math\r\nprint(math.floor(-3.7))', NULL, 'Diese Frage prüft das Verhalten von math.floor() bei negativen Zahlen.', 1, 0, '2026-05-18 23:16:08', '2026-06-01 11:36:48', 1),
(10, 1, 'Consider the following code. What is printed?\r\n```python\r\ntry:\r\n    print(\"A\", end=\" \")\r\n    raise ValueError(\"bad\")\r\nexcept ValueError:\r\n    print(\"B\", end=\" \")\r\nfinally:\r\n    print(\"C\")```', NULL, 'Diese Frage prüft die Ausführungsreihenfolge von try, except und finally.', 1, 1, '2026-05-18 23:16:08', '2026-06-01 11:37:26', 1),
(11, 2, 'Was ist 3 × 4?', NULL, '3 × 4 bedeutet: dreimal die 4 addieren → 4 + 4 + 4 = 12.', 1, 1, '2026-05-26 10:00:00', '2026-06-01 11:11:27', 1),
(12, 2, 'Was ist 6 × 7?', NULL, '6 × 7 = 42. Eine gute Merkhilfe: „Sechs mal sieben ist zwei und vierzig\" – eine der häufig verwechselten Aufgaben im Einmaleins.', 1, 1, '2026-05-26 10:00:00', NULL, NULL),
(13, 2, 'Was ist 8 × 9?', NULL, '8 × 9 = 72. Merktrick: 7, 8, 9 → 7 × 8 = 56, 8 × 9 = 72 (die Ziffern 7 und 2 folgen dem Muster der aufsteigenden Reihe).', 1, 1, '2026-05-26 10:00:00', NULL, NULL),
(14, 2, 'Was ist 4 × 5?', NULL, '4 × 5 = 20. Die Fünferreihe liefert immer Vielfache von 5: 5, 10, 15, 20 – das vierte Glied ist 20.', 1, 1, '2026-05-26 10:00:00', NULL, NULL),
(15, 2, 'Was ist 7 × 8?', NULL, '7 × 8 = 56. Merkhilfe: „Sieben mal acht – fünf und sechs macht\" (56).', 1, 1, '2026-05-26 10:00:00', NULL, NULL),
(16, 2, 'Was ist 9 × 6?', NULL, '9 × 6 = 54. In der Neunerreihe sinkt die Zehnerstelle um 1 und steigt die Einerstelle um 1: 9, 18, 27, 36, 45, 54.', 1, 1, '2026-05-26 10:00:00', NULL, NULL),
(17, 2, 'Was ist 5 × 5?', NULL, '5 × 5 = 25. Jede Quadratzahl der 5 endet auf 25: 5² = 25, 15² = 225, 25² = 625.', 1, 1, '2026-05-26 10:00:00', NULL, NULL),
(18, 2, 'Was ist 3 × 9?', NULL, '3 × 9 = 27. In der Dreierreihe: 3, 6, 9, 12, 15, 18, 21, 24, 27 – das neunte Glied.', 1, 1, '2026-05-26 10:00:00', NULL, NULL),
(19, 2, 'Was ist 6 × 6?', NULL, '6 × 6 = 36. Quadratzahlen sind nützlich: 1, 4, 9, 16, 25, 36 – 36 ist das Quadrat von 6.', 1, 1, '2026-05-26 10:00:00', NULL, NULL),
(20, 2, 'Was ist 7 × 4?', NULL, '7 × 4 = 28. Die Vierreihe: 4, 8, 12, 16, 20, 24, 28 – das siebte Glied ist 28.', 1, 1, '2026-05-26 10:00:00', NULL, NULL),
(21, 3, 'Welche Farben hat eine Ampel?', NULL, 'Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.', 1, 1, '2026-05-26 12:00:00', '2026-05-28 13:40:16', 11),
(22, 3, 'Was kann ein Vogel?', NULL, 'Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.', 1, 1, '2026-05-26 12:00:00', NULL, NULL),
(23, 3, 'Welche Tiere haben Flügel?', NULL, 'Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.', 1, 1, '2026-05-26 12:00:00', NULL, NULL),
(24, 3, 'Was braucht eine Pflanze zum Wachsen?', NULL, 'Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.', 1, 1, '2026-05-26 12:00:00', NULL, NULL),
(25, 3, 'Welche Zahlen sind kleiner als 5?', NULL, '1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.', 1, 1, '2026-05-26 12:00:00', NULL, NULL),
(26, 3, 'Was kann man im Schwimmbad machen?', NULL, 'Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.', 1, 1, '2026-05-26 12:00:00', NULL, NULL),
(27, 3, 'Welche Dinge leuchten?', NULL, 'Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.', 1, 1, '2026-05-26 12:00:00', NULL, NULL),
(28, 3, 'Was ist kalt?', NULL, 'Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.', 1, 1, '2026-05-26 12:00:00', NULL, NULL),
(29, 3, 'Welche Tiere leben im Wasser?', NULL, 'Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.', 1, 1, '2026-05-26 12:00:00', NULL, NULL),
(30, 3, 'Was macht man mit einem Buch?', NULL, 'Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.', 1, 1, '2026-05-26 12:00:00', NULL, NULL);

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
(1, 'PCAP Python Grundlagen Testpool', 'Test-Fragenpool mit zehn PCAP-Fragen aus dem Fragenkatalog. Enthält Multiple-Select-Fragen mit exakt vier Antwortmöglichkeiten und deutschen Erklärungen.', 1, 1, '2026-05-18 23:16:07', NULL, NULL),
(2, 'Das kleine 1x1', 'Testpool mit 10 Multiplikationsaufgaben aus dem kleinen Einmaleins (1–10). Jede Frage hat genau eine richtige Antwort.', 1, 1, '2026-05-26 10:00:00', '2026-06-01 08:05:28', 1),
(3, 'Für kleine Entdecker', 'Testpool mit 10 kinderleichten Fragen für ca. 5-Jährige. Jede Frage hat mehrere richtige Antworten.', 1, 1, '2026-05-26 12:00:00', NULL, NULL);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `current_question_index` int(11) DEFAULT 0,
  `show_explanation` tinyint(1) DEFAULT 0,
  `quiz_data` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `quiz_lobbies`
--

INSERT INTO `quiz_lobbies` (`id`, `join_code`, `host_name`, `question_pool`, `question_count`, `time_limit`, `point_mode`, `host_plays`, `is_started`, `created_at`, `current_question_index`, `show_explanation`, `quiz_data`) VALUES
(1, 'JSQK2', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-22 09:03:06', 0, 0, NULL),
(2, 'G5G34', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-22 09:11:10', 0, 0, NULL),
(3, 'TYHBH', 'testspieler', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-22 09:22:10', 0, 0, NULL),
(4, 'AZ5EY', 'testspieler', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-22 09:23:21', 0, 0, NULL),
(5, '9RAAC', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-22 09:24:29', 0, 0, NULL),
(6, 'T4SLK', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-22 09:44:52', 0, 0, NULL),
(7, 'YSKQN', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'no', 1, '2026-05-22 09:51:12', 0, 0, NULL),
(8, 'A4PWY', 'test', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 10:55:25', 0, 0, NULL),
(9, '8M2KW', 'test', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 10:58:51', 0, 0, NULL),
(10, 'NJBZ7', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 11:01:32', 0, 0, NULL),
(11, 'AMNPW', 'test', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 11:18:10', 0, 0, NULL),
(12, 'KCJTH', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 11:37:52', 0, 0, NULL),
(13, 'FLU4M', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 11:53:05', 0, 0, NULL),
(14, 'Q3MHQ', 'test', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 11:56:28', 0, 0, NULL),
(15, 'HWAF2', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 11:57:50', 0, 0, NULL),
(16, 'H8WTR', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 12:50:28', 0, 0, NULL),
(17, 'JFE6P', 'test', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 12:58:24', 0, 0, NULL),
(18, 'YESYC', 'test', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-22 13:09:42', 0, 0, NULL),
(19, 'D7MZZ', 'test', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 13:10:21', 0, 0, NULL),
(20, 'P4CD5', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 13:10:48', 0, 0, NULL),
(21, '5H2QR', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 13:30:46', 8, 0, NULL),
(22, '9NXSH', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 13:45:01', 1, 1, NULL),
(23, 'KU5K9', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 13:50:14', 1, 1, NULL),
(24, 'JTAXC', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 13:54:07', 0, 0, NULL),
(25, '6LNH7', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 13:56:31', 1, 1, NULL),
(26, '4AAT2', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-22 13:58:06', 10, 0, NULL),
(27, 'JWZVL', 'test', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-23 08:31:07', 1, 1, NULL),
(28, '6PCJX', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-23 08:37:18', 1, 0, NULL),
(29, 'H2STG', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-26 06:00:13', 0, 1, NULL),
(30, 'B9AVA', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-26 06:29:18', 0, 0, NULL),
(31, 'KLQR5', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-26 06:30:50', 1, 1, NULL),
(32, '7S3BJ', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-26 07:58:22', 2, 1, NULL),
(33, 'RBAQW', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-26 08:07:07', 3, 1, NULL),
(34, '6A4JU', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-26 08:08:42', 1, 1, NULL),
(35, 'MEAC5', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-26 08:10:23', 4, 1, NULL),
(36, 'VGXUD', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-26 08:19:11', 8, 1, NULL),
(37, 'UTBZS', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-26 08:50:31', 10, 0, NULL),
(38, 'TWGG6', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-26 08:54:41', 0, 0, NULL),
(39, 'VF4HZ', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-26 09:05:49', 0, 0, NULL),
(40, 'TWJEE', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-26 09:05:59', 0, 0, NULL),
(41, 'SHTDB', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-26 09:06:46', 0, 0, NULL),
(42, 'TVVUB', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-26 09:09:33', 0, 0, NULL),
(43, 'QJ5RR', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-26 09:12:40', 9, 1, NULL),
(44, 'ZEYZM', 'Gast', 'Das kleine 1x1', 10, 30, 'partial', 'yes', 1, '2026-05-26 09:28:51', 4, 1, NULL),
(45, '9JAQ6', 'Gast', 'Das kleine 1x1', 10, 30, 'partial', 'yes', 1, '2026-05-26 09:59:01', 2, 0, NULL),
(46, 'MS556', 'Gast', 'Das kleine 1x1', 10, 30, 'partial', 'yes', 1, '2026-05-26 10:00:10', 10, 0, NULL),
(47, 'H5G2X', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 10:05:21', 0, 0, NULL),
(48, '6G55L', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 10:05:49', 0, 0, NULL),
(49, 'SAC7S', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 10:06:08', 5, 1, NULL),
(50, '9YBKZ', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 10:10:18', 0, 0, NULL),
(51, '526KW', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 10:11:41', 0, 0, NULL),
(52, 'M3ZV2', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 10:12:38', 0, 0, NULL),
(53, '52FFB', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 10:13:00', 2, 0, NULL),
(54, '42Z5P', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 0, '2026-05-26 10:51:40', 0, 0, NULL),
(55, 'E3VDD', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-26 10:57:56', 0, 0, NULL),
(56, 'XWQPE', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 0, '2026-05-26 11:03:46', 0, 0, NULL),
(57, 'BB7HY', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 0, '2026-05-26 11:06:06', 0, 0, NULL),
(58, 'T9QLM', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 11:09:05', 0, 0, NULL),
(59, '3H94G', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 11:09:59', 0, 1, NULL),
(60, 'VNTYA', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 0, '2026-05-26 11:13:58', 0, 0, NULL),
(61, 'V5XK8', 'Gast', 'Das kleine 1x1', 10, 30, 'partial', 'yes', 1, '2026-05-26 11:15:12', 3, 0, NULL),
(62, 'K2KP2', 'test', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 11:17:24', 1, 1, NULL),
(63, 'K44GL', 'test', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 11:21:39', 0, 1, NULL),
(64, 'CTGD9', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 11:37:06', 4, 0, NULL),
(65, 'B4SQ8', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 11:55:22', 0, 1, '[{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1},{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4},{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1},{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3},{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2}]},{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4},{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1},{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3},{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3},{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3},{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1},{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4}]},{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4},{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3},{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3},{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1},{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2},{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3},{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4}]},{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2},{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1},{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3}]},{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2},{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1},{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4},{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3}]}]'),
(66, 'D4U5V', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 11:56:44', 2, 1, '[{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3},{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2},{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4}]},{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1},{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4},{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3},{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3},{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1},{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4},{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2},{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3},{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4},{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3},{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2},{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1}]},{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1},{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3},{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4},{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2}]},{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4},{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2},{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3},{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1}]},{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2},{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1},{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3}]}]'),
(67, 'TBKA4', 'Gast', 'Das kleine 1x1', 10, 30, 'partial', 'yes', 1, '2026-05-26 12:00:53', 0, 1, NULL),
(68, '47TAZ', 'Gast', 'Das kleine 1x1', 10, 30, 'partial', 'yes', 1, '2026-05-26 12:12:11', 0, 1, NULL),
(69, 'EEP3B', 'Gast', 'PCAP Python Grundlagen Testpool', 10, 30, 'partial', 'yes', 1, '2026-05-26 12:13:07', 1, 0, NULL),
(70, '8GFGQ', 'Gast', 'Das kleine 1x1', 10, 30, 'partial', 'yes', 0, '2026-05-26 12:15:05', 0, 0, NULL),
(71, '9H5DN', 'test', 'Das kleine 1x1', 10, 30, 'partial', 'yes', 1, '2026-05-26 12:16:23', 5, 0, NULL),
(72, '6ZVN5', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 12:48:03', 0, 0, NULL),
(73, 'AR9B8', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 12:48:57', 0, 0, NULL),
(74, 'KXXYN', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 12:50:20', 2, 1, '[{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3},{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4},{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2},{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1}]},{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1},{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4},{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2},{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3}]},{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1},{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3},{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4},{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4},{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3}]},{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1},{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3},{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4},{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1},{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4},{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3}]},{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1},{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4},{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1},{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3},{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3},{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1},{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4},{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2}]}]'),
(75, 'R4ZEA', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 12:53:06', 2, 1, '[{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4},{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1}]},{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4},{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1},{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2},{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3}]},{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3},{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4},{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2},{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1}]},{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4},{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1},{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4},{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2},{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4},{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1},{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2},{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4},{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3},{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1}]},{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3},{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4},{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1},{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4},{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3}]}]'),
(76, '4SA75', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 13:08:05', 4, 1, '[{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4},{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3},{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1}]},{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2},{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1},{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4},{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3}]},{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3},{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2},{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4},{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1}]},{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1},{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3},{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1},{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4},{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3},{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1},{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3},{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1},{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3},{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2},{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4}]},{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3},{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4},{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1},{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3},{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4}]}]');
INSERT INTO `quiz_lobbies` (`id`, `join_code`, `host_name`, `question_pool`, `question_count`, `time_limit`, `point_mode`, `host_plays`, `is_started`, `created_at`, `current_question_index`, `show_explanation`, `quiz_data`) VALUES
(77, 'LT9ZR', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 13:16:22', 0, 1, '[{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3},{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1},{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2},{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4},{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3}]},{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2},{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4},{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1},{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3}]},{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4},{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3},{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1},{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2}]},{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4},{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2},{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3}]},{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3},{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2},{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4},{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1},{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2},{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3},{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4},{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1}]}]'),
(78, '7GQW6', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-26 13:18:08', 1, 1, '[{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4},{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3},{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2},{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3}]},{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1},{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4},{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3},{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3},{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2},{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3},{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4},{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4},{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1},{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2},{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3}]},{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1},{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3},{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3},{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2},{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4},{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1}]},{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2},{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3},{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1}]},{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3},{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4},{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2}]}]'),
(79, '9XLAL', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 0, '2026-05-27 07:30:47', 0, 0, '[{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3},{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1},{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4},{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1},{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2},{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2},{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4},{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1},{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3},{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4}]},{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4},{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3},{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2},{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1}]},{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3},{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4},{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1}]},{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4},{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2},{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3},{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2},{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3},{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1}]},{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3},{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1},{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2},{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4}]},{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3},{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1},{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2}]}]'),
(80, '6AHU6', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-27 07:31:53', 1, 1, NULL),
(81, 'BB7VT', 'admin', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 0, '2026-05-27 09:09:00', 0, 0, '[{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3},{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1},{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1},{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2},{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3},{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4},{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3},{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1}]},{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3},{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4},{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1},{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2}]},{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3},{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1},{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4}]},{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4},{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2},{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3},{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1}]},{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3},{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4},{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2},{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3},{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1},{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4},{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1},{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3}]}]'),
(82, 'C3V8H', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-27 09:11:32', 1, 0, '[{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4},{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3},{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1},{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2}]},{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3},{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4},{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1},{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4},{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2},{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3}]},{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3},{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4},{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2},{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4}]},{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2},{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4},{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1},{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3},{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1},{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4},{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4},{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1},{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3}]},{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3},{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2},{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1}]}]'),
(83, 'JBMJR', 'admin', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 0, '2026-05-27 10:00:14', 0, 0, '[{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3},{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2},{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1},{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3},{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4}]},{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2},{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4},{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3}]},{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3},{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2},{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1},{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3},{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1},{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4}]},{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2},{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1},{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3},{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3},{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2},{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3},{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1},{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4}]},{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4},{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2},{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3}]}]'),
(84, 'KMGY7', 'admin', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 0, '2026-05-27 10:00:27', 0, 0, NULL),
(85, '27AJ4', 'admin', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 0, '2026-05-27 10:06:18', 0, 0, NULL);
INSERT INTO `quiz_lobbies` (`id`, `join_code`, `host_name`, `question_pool`, `question_count`, `time_limit`, `point_mode`, `host_plays`, `is_started`, `created_at`, `current_question_index`, `show_explanation`, `quiz_data`) VALUES
(86, 'V2ZYR', 'Gast', 'Das kleine 1x1', 10, 30, 'partial', 'yes', 1, '2026-05-27 11:19:25', 1, 0, '[{\"id\":17,\"question_text\":\"Was ist 5 × 5?\",\"explanation\":\"5 × 5 = 25. Jede Quadratzahl der 5 endet auf 25: 5² = 25, 15² = 225, 25² = 625.\",\"answers\":[{\"id\":67,\"text\":\"30\",\"is_correct\":0,\"explanation\":\"Falsch. 30 ist das Ergebnis von 5 × 6.\",\"sort_order\":3},{\"id\":68,\"text\":\"10\",\"is_correct\":0,\"explanation\":\"Falsch. 10 ist das Ergebnis von 5 × 2.\",\"sort_order\":4},{\"id\":66,\"text\":\"20\",\"is_correct\":0,\"explanation\":\"Falsch. 20 ist das Ergebnis von 4 × 5.\",\"sort_order\":2},{\"id\":65,\"text\":\"25\",\"is_correct\":1,\"explanation\":\"Richtig. 5 × 5 = 25. Quadratzahl der 5.\",\"sort_order\":1}]},{\"id\":16,\"question_text\":\"Was ist 9 × 6?\",\"explanation\":\"9 × 6 = 54. In der Neunerreihe sinkt die Zehnerstelle um 1 und steigt die Einerstelle um 1: 9, 18, 27, 36, 45, 54.\",\"answers\":[{\"id\":63,\"text\":\"48\",\"is_correct\":0,\"explanation\":\"Falsch. 48 ist das Ergebnis von 6 × 8.\",\"sort_order\":3},{\"id\":62,\"text\":\"63\",\"is_correct\":0,\"explanation\":\"Falsch. 63 ist das Ergebnis von 9 × 7.\",\"sort_order\":2},{\"id\":64,\"text\":\"45\",\"is_correct\":0,\"explanation\":\"Falsch. 45 ist das Ergebnis von 9 × 5.\",\"sort_order\":4},{\"id\":61,\"text\":\"54\",\"is_correct\":1,\"explanation\":\"Richtig. 9 × 6 = 54.\",\"sort_order\":1}]},{\"id\":13,\"question_text\":\"Was ist 8 × 9?\",\"explanation\":\"8 × 9 = 72. Merktrick: 7, 8, 9 → 7 × 8 = 56, 8 × 9 = 72 (die Ziffern 7 und 2 folgen dem Muster der aufsteigenden Reihe).\",\"answers\":[{\"id\":52,\"text\":\"64\",\"is_correct\":0,\"explanation\":\"Falsch. 64 ist das Ergebnis von 8 × 8.\",\"sort_order\":4},{\"id\":50,\"text\":\"63\",\"is_correct\":0,\"explanation\":\"Falsch. 63 ist das Ergebnis von 7 × 9.\",\"sort_order\":2},{\"id\":51,\"text\":\"81\",\"is_correct\":0,\"explanation\":\"Falsch. 81 ist das Ergebnis von 9 × 9.\",\"sort_order\":3},{\"id\":49,\"text\":\"72\",\"is_correct\":1,\"explanation\":\"Richtig. 8 × 9 = 72.\",\"sort_order\":1}]},{\"id\":19,\"question_text\":\"Was ist 6 × 6?\",\"explanation\":\"6 × 6 = 36. Quadratzahlen sind nützlich: 1, 4, 9, 16, 25, 36 – 36 ist das Quadrat von 6.\",\"answers\":[{\"id\":74,\"text\":\"30\",\"is_correct\":0,\"explanation\":\"Falsch. 30 ist das Ergebnis von 5 × 6.\",\"sort_order\":2},{\"id\":75,\"text\":\"42\",\"is_correct\":0,\"explanation\":\"Falsch. 42 ist das Ergebnis von 6 × 7.\",\"sort_order\":3},{\"id\":73,\"text\":\"36\",\"is_correct\":1,\"explanation\":\"Richtig. 6 × 6 = 36. Quadratzahl der 6.\",\"sort_order\":1},{\"id\":76,\"text\":\"32\",\"is_correct\":0,\"explanation\":\"Falsch. 32 ist das Ergebnis von 4 × 8.\",\"sort_order\":4}]},{\"id\":11,\"question_text\":\"Was ist 3 × 4?\",\"explanation\":\"3 × 4 bedeutet: dreimal die 4 addieren → 4 + 4 + 4 = 12.\",\"answers\":[{\"id\":43,\"text\":\"16\",\"is_correct\":0,\"explanation\":\"Falsch. 16 ist das Ergebnis von 4 × 4.\",\"sort_order\":3},{\"id\":42,\"text\":\"9\",\"is_correct\":0,\"explanation\":\"Falsch. 9 wäre das Ergebnis von 3 × 3, nicht 3 × 4.\",\"sort_order\":2},{\"id\":41,\"text\":\"12\",\"is_correct\":1,\"explanation\":\"Richtig. 3 × 4 = 12.\",\"sort_order\":1},{\"id\":44,\"text\":\"11\",\"is_correct\":0,\"explanation\":\"Falsch. 11 ist keine Zahl aus der Dreier- oder Vierreihe im kleinen Einmaleins.\",\"sort_order\":4}]},{\"id\":20,\"question_text\":\"Was ist 7 × 4?\",\"explanation\":\"7 × 4 = 28. Die Vierreihe: 4, 8, 12, 16, 20, 24, 28 – das siebte Glied ist 28.\",\"answers\":[{\"id\":78,\"text\":\"21\",\"is_correct\":0,\"explanation\":\"Falsch. 21 ist das Ergebnis von 7 × 3.\",\"sort_order\":2},{\"id\":80,\"text\":\"24\",\"is_correct\":0,\"explanation\":\"Falsch. 24 ist das Ergebnis von 4 × 6.\",\"sort_order\":4},{\"id\":79,\"text\":\"32\",\"is_correct\":0,\"explanation\":\"Falsch. 32 ist das Ergebnis von 4 × 8.\",\"sort_order\":3},{\"id\":77,\"text\":\"28\",\"is_correct\":1,\"explanation\":\"Richtig. 7 × 4 = 28.\",\"sort_order\":1}]},{\"id\":18,\"question_text\":\"Was ist 3 × 9?\",\"explanation\":\"3 × 9 = 27. In der Dreierreihe: 3, 6, 9, 12, 15, 18, 21, 24, 27 – das neunte Glied.\",\"answers\":[{\"id\":69,\"text\":\"27\",\"is_correct\":1,\"explanation\":\"Richtig. 3 × 9 = 27.\",\"sort_order\":1},{\"id\":72,\"text\":\"30\",\"is_correct\":0,\"explanation\":\"Falsch. 30 ist das Ergebnis von 3 × 10.\",\"sort_order\":4},{\"id\":71,\"text\":\"24\",\"is_correct\":0,\"explanation\":\"Falsch. 24 ist das Ergebnis von 3 × 8.\",\"sort_order\":3},{\"id\":70,\"text\":\"21\",\"is_correct\":0,\"explanation\":\"Falsch. 21 ist das Ergebnis von 3 × 7.\",\"sort_order\":2}]},{\"id\":14,\"question_text\":\"Was ist 4 × 5?\",\"explanation\":\"4 × 5 = 20. Die Fünferreihe liefert immer Vielfache von 5: 5, 10, 15, 20 – das vierte Glied ist 20.\",\"answers\":[{\"id\":53,\"text\":\"20\",\"is_correct\":1,\"explanation\":\"Richtig. 4 × 5 = 20.\",\"sort_order\":1},{\"id\":54,\"text\":\"16\",\"is_correct\":0,\"explanation\":\"Falsch. 16 ist das Ergebnis von 4 × 4.\",\"sort_order\":2},{\"id\":55,\"text\":\"25\",\"is_correct\":0,\"explanation\":\"Falsch. 25 ist das Ergebnis von 5 × 5.\",\"sort_order\":3},{\"id\":56,\"text\":\"18\",\"is_correct\":0,\"explanation\":\"Falsch. 18 ist das Ergebnis von 3 × 6 oder 2 × 9.\",\"sort_order\":4}]},{\"id\":15,\"question_text\":\"Was ist 7 × 8?\",\"explanation\":\"7 × 8 = 56. Merkhilfe: „Sieben mal acht – fünf und sechs macht\\\" (56).\",\"answers\":[{\"id\":58,\"text\":\"49\",\"is_correct\":0,\"explanation\":\"Falsch. 49 ist das Ergebnis von 7 × 7.\",\"sort_order\":2},{\"id\":57,\"text\":\"56\",\"is_correct\":1,\"explanation\":\"Richtig. 7 × 8 = 56.\",\"sort_order\":1},{\"id\":60,\"text\":\"54\",\"is_correct\":0,\"explanation\":\"Falsch. 54 ist das Ergebnis von 6 × 9.\",\"sort_order\":4},{\"id\":59,\"text\":\"64\",\"is_correct\":0,\"explanation\":\"Falsch. 64 ist das Ergebnis von 8 × 8.\",\"sort_order\":3}]},{\"id\":12,\"question_text\":\"Was ist 6 × 7?\",\"explanation\":\"6 × 7 = 42. Eine gute Merkhilfe: „Sechs mal sieben ist zwei und vierzig\\\" – eine der häufig verwechselten Aufgaben im Einmaleins.\",\"answers\":[{\"id\":45,\"text\":\"42\",\"is_correct\":1,\"explanation\":\"Richtig. 6 × 7 = 42.\",\"sort_order\":1},{\"id\":48,\"text\":\"35\",\"is_correct\":0,\"explanation\":\"Falsch. 35 ist das Ergebnis von 5 × 7.\",\"sort_order\":4},{\"id\":47,\"text\":\"48\",\"is_correct\":0,\"explanation\":\"Falsch. 48 ist das Ergebnis von 6 × 8.\",\"sort_order\":3},{\"id\":46,\"text\":\"36\",\"is_correct\":0,\"explanation\":\"Falsch. 36 ist das Ergebnis von 6 × 6.\",\"sort_order\":2}]}]'),
(87, '97ZVU', 'Gast', 'Das kleine 1x1', 10, 30, 'partial', 'yes', 1, '2026-05-27 11:21:30', 0, 0, '[{\"id\":18,\"question_text\":\"Was ist 3 × 9?\",\"explanation\":\"3 × 9 = 27. In der Dreierreihe: 3, 6, 9, 12, 15, 18, 21, 24, 27 – das neunte Glied.\",\"answers\":[{\"id\":71,\"text\":\"24\",\"is_correct\":0,\"explanation\":\"Falsch. 24 ist das Ergebnis von 3 × 8.\",\"sort_order\":3},{\"id\":70,\"text\":\"21\",\"is_correct\":0,\"explanation\":\"Falsch. 21 ist das Ergebnis von 3 × 7.\",\"sort_order\":2},{\"id\":72,\"text\":\"30\",\"is_correct\":0,\"explanation\":\"Falsch. 30 ist das Ergebnis von 3 × 10.\",\"sort_order\":4},{\"id\":69,\"text\":\"27\",\"is_correct\":1,\"explanation\":\"Richtig. 3 × 9 = 27.\",\"sort_order\":1}]},{\"id\":14,\"question_text\":\"Was ist 4 × 5?\",\"explanation\":\"4 × 5 = 20. Die Fünferreihe liefert immer Vielfache von 5: 5, 10, 15, 20 – das vierte Glied ist 20.\",\"answers\":[{\"id\":55,\"text\":\"25\",\"is_correct\":0,\"explanation\":\"Falsch. 25 ist das Ergebnis von 5 × 5.\",\"sort_order\":3},{\"id\":56,\"text\":\"18\",\"is_correct\":0,\"explanation\":\"Falsch. 18 ist das Ergebnis von 3 × 6 oder 2 × 9.\",\"sort_order\":4},{\"id\":53,\"text\":\"20\",\"is_correct\":1,\"explanation\":\"Richtig. 4 × 5 = 20.\",\"sort_order\":1},{\"id\":54,\"text\":\"16\",\"is_correct\":0,\"explanation\":\"Falsch. 16 ist das Ergebnis von 4 × 4.\",\"sort_order\":2}]},{\"id\":17,\"question_text\":\"Was ist 5 × 5?\",\"explanation\":\"5 × 5 = 25. Jede Quadratzahl der 5 endet auf 25: 5² = 25, 15² = 225, 25² = 625.\",\"answers\":[{\"id\":68,\"text\":\"10\",\"is_correct\":0,\"explanation\":\"Falsch. 10 ist das Ergebnis von 5 × 2.\",\"sort_order\":4},{\"id\":66,\"text\":\"20\",\"is_correct\":0,\"explanation\":\"Falsch. 20 ist das Ergebnis von 4 × 5.\",\"sort_order\":2},{\"id\":67,\"text\":\"30\",\"is_correct\":0,\"explanation\":\"Falsch. 30 ist das Ergebnis von 5 × 6.\",\"sort_order\":3},{\"id\":65,\"text\":\"25\",\"is_correct\":1,\"explanation\":\"Richtig. 5 × 5 = 25. Quadratzahl der 5.\",\"sort_order\":1}]},{\"id\":16,\"question_text\":\"Was ist 9 × 6?\",\"explanation\":\"9 × 6 = 54. In der Neunerreihe sinkt die Zehnerstelle um 1 und steigt die Einerstelle um 1: 9, 18, 27, 36, 45, 54.\",\"answers\":[{\"id\":62,\"text\":\"63\",\"is_correct\":0,\"explanation\":\"Falsch. 63 ist das Ergebnis von 9 × 7.\",\"sort_order\":2},{\"id\":63,\"text\":\"48\",\"is_correct\":0,\"explanation\":\"Falsch. 48 ist das Ergebnis von 6 × 8.\",\"sort_order\":3},{\"id\":61,\"text\":\"54\",\"is_correct\":1,\"explanation\":\"Richtig. 9 × 6 = 54.\",\"sort_order\":1},{\"id\":64,\"text\":\"45\",\"is_correct\":0,\"explanation\":\"Falsch. 45 ist das Ergebnis von 9 × 5.\",\"sort_order\":4}]},{\"id\":20,\"question_text\":\"Was ist 7 × 4?\",\"explanation\":\"7 × 4 = 28. Die Vierreihe: 4, 8, 12, 16, 20, 24, 28 – das siebte Glied ist 28.\",\"answers\":[{\"id\":77,\"text\":\"28\",\"is_correct\":1,\"explanation\":\"Richtig. 7 × 4 = 28.\",\"sort_order\":1},{\"id\":80,\"text\":\"24\",\"is_correct\":0,\"explanation\":\"Falsch. 24 ist das Ergebnis von 4 × 6.\",\"sort_order\":4},{\"id\":78,\"text\":\"21\",\"is_correct\":0,\"explanation\":\"Falsch. 21 ist das Ergebnis von 7 × 3.\",\"sort_order\":2},{\"id\":79,\"text\":\"32\",\"is_correct\":0,\"explanation\":\"Falsch. 32 ist das Ergebnis von 4 × 8.\",\"sort_order\":3}]},{\"id\":15,\"question_text\":\"Was ist 7 × 8?\",\"explanation\":\"7 × 8 = 56. Merkhilfe: „Sieben mal acht – fünf und sechs macht\\\" (56).\",\"answers\":[{\"id\":58,\"text\":\"49\",\"is_correct\":0,\"explanation\":\"Falsch. 49 ist das Ergebnis von 7 × 7.\",\"sort_order\":2},{\"id\":57,\"text\":\"56\",\"is_correct\":1,\"explanation\":\"Richtig. 7 × 8 = 56.\",\"sort_order\":1},{\"id\":59,\"text\":\"64\",\"is_correct\":0,\"explanation\":\"Falsch. 64 ist das Ergebnis von 8 × 8.\",\"sort_order\":3},{\"id\":60,\"text\":\"54\",\"is_correct\":0,\"explanation\":\"Falsch. 54 ist das Ergebnis von 6 × 9.\",\"sort_order\":4}]},{\"id\":11,\"question_text\":\"Was ist 3 × 4?\",\"explanation\":\"3 × 4 bedeutet: dreimal die 4 addieren → 4 + 4 + 4 = 12.\",\"answers\":[{\"id\":42,\"text\":\"9\",\"is_correct\":0,\"explanation\":\"Falsch. 9 wäre das Ergebnis von 3 × 3, nicht 3 × 4.\",\"sort_order\":2},{\"id\":43,\"text\":\"16\",\"is_correct\":0,\"explanation\":\"Falsch. 16 ist das Ergebnis von 4 × 4.\",\"sort_order\":3},{\"id\":41,\"text\":\"12\",\"is_correct\":1,\"explanation\":\"Richtig. 3 × 4 = 12.\",\"sort_order\":1},{\"id\":44,\"text\":\"11\",\"is_correct\":0,\"explanation\":\"Falsch. 11 ist keine Zahl aus der Dreier- oder Vierreihe im kleinen Einmaleins.\",\"sort_order\":4}]},{\"id\":12,\"question_text\":\"Was ist 6 × 7?\",\"explanation\":\"6 × 7 = 42. Eine gute Merkhilfe: „Sechs mal sieben ist zwei und vierzig\\\" – eine der häufig verwechselten Aufgaben im Einmaleins.\",\"answers\":[{\"id\":46,\"text\":\"36\",\"is_correct\":0,\"explanation\":\"Falsch. 36 ist das Ergebnis von 6 × 6.\",\"sort_order\":2},{\"id\":48,\"text\":\"35\",\"is_correct\":0,\"explanation\":\"Falsch. 35 ist das Ergebnis von 5 × 7.\",\"sort_order\":4},{\"id\":47,\"text\":\"48\",\"is_correct\":0,\"explanation\":\"Falsch. 48 ist das Ergebnis von 6 × 8.\",\"sort_order\":3},{\"id\":45,\"text\":\"42\",\"is_correct\":1,\"explanation\":\"Richtig. 6 × 7 = 42.\",\"sort_order\":1}]},{\"id\":19,\"question_text\":\"Was ist 6 × 6?\",\"explanation\":\"6 × 6 = 36. Quadratzahlen sind nützlich: 1, 4, 9, 16, 25, 36 – 36 ist das Quadrat von 6.\",\"answers\":[{\"id\":75,\"text\":\"42\",\"is_correct\":0,\"explanation\":\"Falsch. 42 ist das Ergebnis von 6 × 7.\",\"sort_order\":3},{\"id\":73,\"text\":\"36\",\"is_correct\":1,\"explanation\":\"Richtig. 6 × 6 = 36. Quadratzahl der 6.\",\"sort_order\":1},{\"id\":74,\"text\":\"30\",\"is_correct\":0,\"explanation\":\"Falsch. 30 ist das Ergebnis von 5 × 6.\",\"sort_order\":2},{\"id\":76,\"text\":\"32\",\"is_correct\":0,\"explanation\":\"Falsch. 32 ist das Ergebnis von 4 × 8.\",\"sort_order\":4}]},{\"id\":13,\"question_text\":\"Was ist 8 × 9?\",\"explanation\":\"8 × 9 = 72. Merktrick: 7, 8, 9 → 7 × 8 = 56, 8 × 9 = 72 (die Ziffern 7 und 2 folgen dem Muster der aufsteigenden Reihe).\",\"answers\":[{\"id\":52,\"text\":\"64\",\"is_correct\":0,\"explanation\":\"Falsch. 64 ist das Ergebnis von 8 × 8.\",\"sort_order\":4},{\"id\":50,\"text\":\"63\",\"is_correct\":0,\"explanation\":\"Falsch. 63 ist das Ergebnis von 7 × 9.\",\"sort_order\":2},{\"id\":49,\"text\":\"72\",\"is_correct\":1,\"explanation\":\"Richtig. 8 × 9 = 72.\",\"sort_order\":1},{\"id\":51,\"text\":\"81\",\"is_correct\":0,\"explanation\":\"Falsch. 81 ist das Ergebnis von 9 × 9.\",\"sort_order\":3}]}]'),
(88, 'MKUN8', 'Ralf_Reichts', 'Für kleine Entdecker', 3, 60, 'partial', 'yes', 1, '2026-05-28 07:53:10', 2, 0, '[{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3},{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2},{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4}]},{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2},{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1},{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4},{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3}]}]'),
(89, '68TEX', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-28 10:01:37', 1, 0, '[{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4},{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3}]},{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3},{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1},{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2}]},{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1},{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3},{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2},{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2},{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3},{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4},{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1},{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4},{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3}]},{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2},{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3},{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4}]},{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3},{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2},{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4},{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4},{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3},{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1},{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4},{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2}]},{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3},{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1}]}]'),
(90, 'FTKJU', 'Ralf_Reichts', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-28 10:03:31', 8, 0, '[{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4},{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1},{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3},{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2}]},{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1},{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4},{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3}]},{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1},{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2},{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4},{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3},{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3},{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1},{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2},{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4},{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2},{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3}]},{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2},{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3},{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1},{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3},{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4},{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1}]},{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2},{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3},{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1}]}]'),
(91, 'K4BR2', 'admin', 'Für kleine Entdecker', 5, 60, 'all_or_nothing', 'yes', 1, '2026-05-28 13:05:01', 1, 1, '[{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1},{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3},{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3},{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1}]},{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4},{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1},{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3},{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4}]},{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1},{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2},{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3}]}]'),
(92, '69BD6', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-28 13:09:22', 1, 1, NULL),
(93, 'TBU4H', 'Gast', 'Das kleine 1x1', 10, 30, 'partial', 'yes', 1, '2026-05-28 14:04:42', 3, 1, '[{\"id\":15,\"question_text\":\"Was ist 7 × 8?\",\"explanation\":\"7 × 8 = 56. Merkhilfe: „Sieben mal acht – fünf und sechs macht\\\" (56).\",\"answers\":[{\"id\":58,\"text\":\"49\",\"is_correct\":0,\"explanation\":\"Falsch. 49 ist das Ergebnis von 7 × 7.\",\"sort_order\":2},{\"id\":60,\"text\":\"54\",\"is_correct\":0,\"explanation\":\"Falsch. 54 ist das Ergebnis von 6 × 9.\",\"sort_order\":4},{\"id\":57,\"text\":\"56\",\"is_correct\":1,\"explanation\":\"Richtig. 7 × 8 = 56.\",\"sort_order\":1},{\"id\":59,\"text\":\"64\",\"is_correct\":0,\"explanation\":\"Falsch. 64 ist das Ergebnis von 8 × 8.\",\"sort_order\":3}]},{\"id\":18,\"question_text\":\"Was ist 3 × 9?\",\"explanation\":\"3 × 9 = 27. In der Dreierreihe: 3, 6, 9, 12, 15, 18, 21, 24, 27 – das neunte Glied.\",\"answers\":[{\"id\":72,\"text\":\"30\",\"is_correct\":0,\"explanation\":\"Falsch. 30 ist das Ergebnis von 3 × 10.\",\"sort_order\":4},{\"id\":71,\"text\":\"24\",\"is_correct\":0,\"explanation\":\"Falsch. 24 ist das Ergebnis von 3 × 8.\",\"sort_order\":3},{\"id\":70,\"text\":\"21\",\"is_correct\":0,\"explanation\":\"Falsch. 21 ist das Ergebnis von 3 × 7.\",\"sort_order\":2},{\"id\":69,\"text\":\"27\",\"is_correct\":1,\"explanation\":\"Richtig. 3 × 9 = 27.\",\"sort_order\":1}]},{\"id\":11,\"question_text\":\"Was ist 3 × 4?\",\"explanation\":\"3 × 4 bedeutet: dreimal die 4 addieren → 4 + 4 + 4 = 12.\",\"answers\":[{\"id\":41,\"text\":\"12\",\"is_correct\":1,\"explanation\":\"Richtig. 3 × 4 = 12.\",\"sort_order\":1},{\"id\":42,\"text\":\"9\",\"is_correct\":0,\"explanation\":\"Falsch. 9 wäre das Ergebnis von 3 × 3, nicht 3 × 4.\",\"sort_order\":2},{\"id\":43,\"text\":\"16\",\"is_correct\":0,\"explanation\":\"Falsch. 16 ist das Ergebnis von 4 × 4.\",\"sort_order\":3},{\"id\":44,\"text\":\"11\",\"is_correct\":0,\"explanation\":\"Falsch. 11 ist keine Zahl aus der Dreier- oder Vierreihe im kleinen Einmaleins.\",\"sort_order\":4}]},{\"id\":19,\"question_text\":\"Was ist 6 × 6?\",\"explanation\":\"6 × 6 = 36. Quadratzahlen sind nützlich: 1, 4, 9, 16, 25, 36 – 36 ist das Quadrat von 6.\",\"answers\":[{\"id\":74,\"text\":\"30\",\"is_correct\":0,\"explanation\":\"Falsch. 30 ist das Ergebnis von 5 × 6.\",\"sort_order\":2},{\"id\":75,\"text\":\"42\",\"is_correct\":0,\"explanation\":\"Falsch. 42 ist das Ergebnis von 6 × 7.\",\"sort_order\":3},{\"id\":76,\"text\":\"32\",\"is_correct\":0,\"explanation\":\"Falsch. 32 ist das Ergebnis von 4 × 8.\",\"sort_order\":4},{\"id\":73,\"text\":\"36\",\"is_correct\":1,\"explanation\":\"Richtig. 6 × 6 = 36. Quadratzahl der 6.\",\"sort_order\":1}]},{\"id\":16,\"question_text\":\"Was ist 9 × 6?\",\"explanation\":\"9 × 6 = 54. In der Neunerreihe sinkt die Zehnerstelle um 1 und steigt die Einerstelle um 1: 9, 18, 27, 36, 45, 54.\",\"answers\":[{\"id\":62,\"text\":\"63\",\"is_correct\":0,\"explanation\":\"Falsch. 63 ist das Ergebnis von 9 × 7.\",\"sort_order\":2},{\"id\":63,\"text\":\"48\",\"is_correct\":0,\"explanation\":\"Falsch. 48 ist das Ergebnis von 6 × 8.\",\"sort_order\":3},{\"id\":64,\"text\":\"45\",\"is_correct\":0,\"explanation\":\"Falsch. 45 ist das Ergebnis von 9 × 5.\",\"sort_order\":4},{\"id\":61,\"text\":\"54\",\"is_correct\":1,\"explanation\":\"Richtig. 9 × 6 = 54.\",\"sort_order\":1}]},{\"id\":14,\"question_text\":\"Was ist 4 × 5?\",\"explanation\":\"4 × 5 = 20. Die Fünferreihe liefert immer Vielfache von 5: 5, 10, 15, 20 – das vierte Glied ist 20.\",\"answers\":[{\"id\":54,\"text\":\"16\",\"is_correct\":0,\"explanation\":\"Falsch. 16 ist das Ergebnis von 4 × 4.\",\"sort_order\":2},{\"id\":53,\"text\":\"20\",\"is_correct\":1,\"explanation\":\"Richtig. 4 × 5 = 20.\",\"sort_order\":1},{\"id\":55,\"text\":\"25\",\"is_correct\":0,\"explanation\":\"Falsch. 25 ist das Ergebnis von 5 × 5.\",\"sort_order\":3},{\"id\":56,\"text\":\"18\",\"is_correct\":0,\"explanation\":\"Falsch. 18 ist das Ergebnis von 3 × 6 oder 2 × 9.\",\"sort_order\":4}]},{\"id\":12,\"question_text\":\"Was ist 6 × 7?\",\"explanation\":\"6 × 7 = 42. Eine gute Merkhilfe: „Sechs mal sieben ist zwei und vierzig\\\" – eine der häufig verwechselten Aufgaben im Einmaleins.\",\"answers\":[{\"id\":47,\"text\":\"48\",\"is_correct\":0,\"explanation\":\"Falsch. 48 ist das Ergebnis von 6 × 8.\",\"sort_order\":3},{\"id\":48,\"text\":\"35\",\"is_correct\":0,\"explanation\":\"Falsch. 35 ist das Ergebnis von 5 × 7.\",\"sort_order\":4},{\"id\":45,\"text\":\"42\",\"is_correct\":1,\"explanation\":\"Richtig. 6 × 7 = 42.\",\"sort_order\":1},{\"id\":46,\"text\":\"36\",\"is_correct\":0,\"explanation\":\"Falsch. 36 ist das Ergebnis von 6 × 6.\",\"sort_order\":2}]},{\"id\":17,\"question_text\":\"Was ist 5 × 5?\",\"explanation\":\"5 × 5 = 25. Jede Quadratzahl der 5 endet auf 25: 5² = 25, 15² = 225, 25² = 625.\",\"answers\":[{\"id\":68,\"text\":\"10\",\"is_correct\":0,\"explanation\":\"Falsch. 10 ist das Ergebnis von 5 × 2.\",\"sort_order\":4},{\"id\":66,\"text\":\"20\",\"is_correct\":0,\"explanation\":\"Falsch. 20 ist das Ergebnis von 4 × 5.\",\"sort_order\":2},{\"id\":67,\"text\":\"30\",\"is_correct\":0,\"explanation\":\"Falsch. 30 ist das Ergebnis von 5 × 6.\",\"sort_order\":3},{\"id\":65,\"text\":\"25\",\"is_correct\":1,\"explanation\":\"Richtig. 5 × 5 = 25. Quadratzahl der 5.\",\"sort_order\":1}]},{\"id\":13,\"question_text\":\"Was ist 8 × 9?\",\"explanation\":\"8 × 9 = 72. Merktrick: 7, 8, 9 → 7 × 8 = 56, 8 × 9 = 72 (die Ziffern 7 und 2 folgen dem Muster der aufsteigenden Reihe).\",\"answers\":[{\"id\":49,\"text\":\"72\",\"is_correct\":1,\"explanation\":\"Richtig. 8 × 9 = 72.\",\"sort_order\":1},{\"id\":52,\"text\":\"64\",\"is_correct\":0,\"explanation\":\"Falsch. 64 ist das Ergebnis von 8 × 8.\",\"sort_order\":4},{\"id\":51,\"text\":\"81\",\"is_correct\":0,\"explanation\":\"Falsch. 81 ist das Ergebnis von 9 × 9.\",\"sort_order\":3},{\"id\":50,\"text\":\"63\",\"is_correct\":0,\"explanation\":\"Falsch. 63 ist das Ergebnis von 7 × 9.\",\"sort_order\":2}]},{\"id\":20,\"question_text\":\"Was ist 7 × 4?\",\"explanation\":\"7 × 4 = 28. Die Vierreihe: 4, 8, 12, 16, 20, 24, 28 – das siebte Glied ist 28.\",\"answers\":[{\"id\":79,\"text\":\"32\",\"is_correct\":0,\"explanation\":\"Falsch. 32 ist das Ergebnis von 4 × 8.\",\"sort_order\":3},{\"id\":77,\"text\":\"28\",\"is_correct\":1,\"explanation\":\"Richtig. 7 × 4 = 28.\",\"sort_order\":1},{\"id\":78,\"text\":\"21\",\"is_correct\":0,\"explanation\":\"Falsch. 21 ist das Ergebnis von 7 × 3.\",\"sort_order\":2},{\"id\":80,\"text\":\"24\",\"is_correct\":0,\"explanation\":\"Falsch. 24 ist das Ergebnis von 4 × 6.\",\"sort_order\":4}]}]'),
(94, 'V55QL', 'admin', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-28 14:12:15', 1, 1, '[{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1},{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2},{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4},{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3}]},{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1},{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3}]},{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4},{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2},{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3},{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1}]},{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3},{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1},{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3},{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4},{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1}]},{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4},{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3},{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4},{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3},{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4},{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1},{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2},{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1},{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3},{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4}]}]'),
(95, 'VWRE3', 'Gast', 'Für kleine Entdecker', 1, 30, 'partial', 'yes', 1, '2026-05-28 14:13:30', 1, 0, '[{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4},{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2}]}]');
INSERT INTO `quiz_lobbies` (`id`, `join_code`, `host_name`, `question_pool`, `question_count`, `time_limit`, `point_mode`, `host_plays`, `is_started`, `created_at`, `current_question_index`, `show_explanation`, `quiz_data`) VALUES
(96, 'BLF4P', 'Gast', 'Für kleine Entdecker', 10, 45, 'partial', 'yes', 1, '2026-05-29 11:05:02', 1, 0, '[{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4},{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2},{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3},{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3},{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4},{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1}]},{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3},{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4},{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1}]},{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3},{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2},{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1}]},{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2},{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3},{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1},{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4}]},{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4},{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1},{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4},{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2},{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1},{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4},{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1},{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2},{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3},{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2}]}]'),
(97, 'K5EBD', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-29 11:45:09', 4, 1, '[{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3},{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2},{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1}]},{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3},{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1},{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4},{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3},{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1},{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2},{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3}]},{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4},{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2},{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3},{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4},{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1},{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4},{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3}]},{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1},{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3}]},{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4},{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3},{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1},{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1},{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2},{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4},{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3}]}]'),
(98, 'Z9E4H', 'Gast', 'Für kleine Entdecker', 10, 30, 'partial', 'yes', 1, '2026-05-29 14:05:21', 0, 1, '[{\"id\":30,\"question_text\":\"Was macht man mit einem Buch?\",\"explanation\":\"Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.\",\"answers\":[{\"id\":120,\"text\":\"Trinken\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch kann man nicht trinken.\",\"sort_order\":4},{\"id\":117,\"text\":\"Lesen\",\"is_correct\":1,\"explanation\":\"Richtig! Bücher sind zum Lesen da.\",\"sort_order\":1},{\"id\":119,\"text\":\"Essen\",\"is_correct\":0,\"explanation\":\"Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!\",\"sort_order\":3},{\"id\":118,\"text\":\"Anschauen\",\"is_correct\":1,\"explanation\":\"Richtig! Besonders Bilderbücher schaut man gerne an.\",\"sort_order\":2}]},{\"id\":23,\"question_text\":\"Welche Tiere haben Flügel?\",\"explanation\":\"Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.\",\"answers\":[{\"id\":90,\"text\":\"Vogel\",\"is_correct\":1,\"explanation\":\"Richtig! Vögel haben Flügel zum Fliegen.\",\"sort_order\":2},{\"id\":91,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde haben keine Flügel.\",\"sort_order\":3},{\"id\":89,\"text\":\"Schmetterling\",\"is_correct\":1,\"explanation\":\"Richtig! Schmetterlinge haben bunte Flügel.\",\"sort_order\":1},{\"id\":92,\"text\":\"Fisch\",\"is_correct\":0,\"explanation\":\"Falsch. Fische haben Flossen, aber keine Flügel.\",\"sort_order\":4}]},{\"id\":26,\"question_text\":\"Was kann man im Schwimmbad machen?\",\"explanation\":\"Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.\",\"answers\":[{\"id\":103,\"text\":\"Kochen\",\"is_correct\":0,\"explanation\":\"Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.\",\"sort_order\":3},{\"id\":104,\"text\":\"Schlafen\",\"is_correct\":0,\"explanation\":\"Falsch. Im Schwimmbad schläft man nicht – man plantscht!\",\"sort_order\":4},{\"id\":102,\"text\":\"Tauchen\",\"is_correct\":1,\"explanation\":\"Richtig! Unter Wasser tauchen macht viel Spaß.\",\"sort_order\":2},{\"id\":101,\"text\":\"Schwimmen\",\"is_correct\":1,\"explanation\":\"Richtig! Schwimmen ist die Hauptsache im Schwimmbad.\",\"sort_order\":1}]},{\"id\":22,\"question_text\":\"Was kann ein Vogel?\",\"explanation\":\"Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.\",\"answers\":[{\"id\":87,\"text\":\"Bellen\",\"is_correct\":0,\"explanation\":\"Falsch. Bellen machen Hunde, keine Vögel.\",\"sort_order\":3},{\"id\":85,\"text\":\"Fliegen\",\"is_correct\":1,\"explanation\":\"Richtig! Die meisten Vögel können fliegen.\",\"sort_order\":1},{\"id\":88,\"text\":\"Miauen\",\"is_correct\":0,\"explanation\":\"Falsch. Miauen machen Katzen, keine Vögel.\",\"sort_order\":4},{\"id\":86,\"text\":\"Singen\",\"is_correct\":1,\"explanation\":\"Richtig! Viele Vögel machen schöne Gesänge.\",\"sort_order\":2}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1},{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2},{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3},{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4}]},{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1},{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3},{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2}]},{\"id\":24,\"question_text\":\"Was braucht eine Pflanze zum Wachsen?\",\"explanation\":\"Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.\",\"answers\":[{\"id\":93,\"text\":\"Wasser\",\"is_correct\":1,\"explanation\":\"Richtig! Ohne Wasser geht eine Pflanze ein.\",\"sort_order\":1},{\"id\":96,\"text\":\"Fernseher\",\"is_correct\":0,\"explanation\":\"Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.\",\"sort_order\":4},{\"id\":94,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Pflanzen brauchen Licht zum Wachsen.\",\"sort_order\":2},{\"id\":95,\"text\":\"Erde\",\"is_correct\":1,\"explanation\":\"Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.\",\"sort_order\":3}]},{\"id\":29,\"question_text\":\"Welche Tiere leben im Wasser?\",\"explanation\":\"Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.\",\"answers\":[{\"id\":116,\"text\":\"Katze\",\"is_correct\":0,\"explanation\":\"Falsch. Katzen mögen kein Wasser und leben an Land.\",\"sort_order\":4},{\"id\":114,\"text\":\"Delfin\",\"is_correct\":1,\"explanation\":\"Richtig! Delfine leben im Meer und sind sehr klug.\",\"sort_order\":2},{\"id\":115,\"text\":\"Hund\",\"is_correct\":0,\"explanation\":\"Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.\",\"sort_order\":3},{\"id\":113,\"text\":\"Fisch\",\"is_correct\":1,\"explanation\":\"Richtig! Fische leben im Wasser und können nicht an Land.\",\"sort_order\":1}]},{\"id\":28,\"question_text\":\"Was ist kalt?\",\"explanation\":\"Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.\",\"answers\":[{\"id\":112,\"text\":\"Sonne\",\"is_correct\":0,\"explanation\":\"Falsch. Die Sonne wärmt uns, sie ist nicht kalt.\",\"sort_order\":4},{\"id\":109,\"text\":\"Eis\",\"is_correct\":1,\"explanation\":\"Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.\",\"sort_order\":1},{\"id\":111,\"text\":\"Feuer\",\"is_correct\":0,\"explanation\":\"Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!\",\"sort_order\":3},{\"id\":110,\"text\":\"Schnee\",\"is_correct\":1,\"explanation\":\"Richtig! Schnee ist kalt – man braucht eine warme Jacke.\",\"sort_order\":2}]},{\"id\":27,\"question_text\":\"Welche Dinge leuchten?\",\"explanation\":\"Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.\",\"answers\":[{\"id\":105,\"text\":\"Sonne\",\"is_correct\":1,\"explanation\":\"Richtig! Die Sonne ist das hellste Licht am Himmel.\",\"sort_order\":1},{\"id\":106,\"text\":\"Lampe\",\"is_correct\":1,\"explanation\":\"Richtig! Eine Lampe leuchtet wenn man sie einschaltet.\",\"sort_order\":2},{\"id\":107,\"text\":\"Taschenlampe\",\"is_correct\":1,\"explanation\":\"Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.\",\"sort_order\":3},{\"id\":108,\"text\":\"Stein\",\"is_correct\":0,\"explanation\":\"Falsch. Ein normaler Stein leuchtet nicht.\",\"sort_order\":4}]}]'),
(99, 'UHQQS', 'Gast', 'Für kleine Entdecker', 2, 30, 'partial', 'yes', 1, '2026-06-01 06:14:13', 0, 1, '[{\"id\":21,\"question_text\":\"Welche Farben hat eine Ampel?\",\"explanation\":\"Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.\",\"answers\":[{\"id\":82,\"text\":\"Gelb\",\"is_correct\":1,\"explanation\":\"Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.\",\"sort_order\":2},{\"id\":83,\"text\":\"Grün\",\"is_correct\":1,\"explanation\":\"Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.\",\"sort_order\":3},{\"id\":84,\"text\":\"Lila\",\"is_correct\":0,\"explanation\":\"Falsch. Lila gibt es an einer Ampel nicht.\",\"sort_order\":4},{\"id\":81,\"text\":\"Rot\",\"is_correct\":1,\"explanation\":\"Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.\",\"sort_order\":1}]},{\"id\":25,\"question_text\":\"Welche Zahlen sind kleiner als 5?\",\"explanation\":\"1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.\",\"answers\":[{\"id\":100,\"text\":\"8\",\"is_correct\":0,\"explanation\":\"Falsch. 8 ist größer als 5.\",\"sort_order\":4},{\"id\":98,\"text\":\"3\",\"is_correct\":1,\"explanation\":\"Richtig! 3 ist kleiner als 5.\",\"sort_order\":2},{\"id\":99,\"text\":\"6\",\"is_correct\":0,\"explanation\":\"Falsch. 6 ist größer als 5.\",\"sort_order\":3},{\"id\":97,\"text\":\"1\",\"is_correct\":1,\"explanation\":\"Richtig! 1 ist kleiner als 5.\",\"sort_order\":1}]}]'),
(100, 'AVY6A', 'Gast', 'PCAP Python Grundlagen Testpool', 1, 60, 'partial', 'yes', 1, '2026-06-01 09:37:45', 0, 1, '[{\"id\":10,\"question_text\":\"Consider the following code. What is printed?\\r\\n```python\\r\\ntry:\\r\\n    print(\\\"A\\\", end=\\\" \\\")\\r\\n    raise ValueError(\\\"bad\\\")\\r\\nexcept ValueError:\\r\\n    print(\\\"B\\\", end=\\\" \\\")\\r\\nfinally:\\r\\n    print(\\\"C\\\")```\",\"explanation\":\"Diese Frage prüft die Ausführungsreihenfolge von try, except und finally.\",\"answers\":[{\"id\":140,\"text\":\"A ValueError C\",\"is_correct\":0,\"explanation\":\"Falsch. Die ValueError wird abgefangen und erscheint deshalb nicht als ungefangener Fehler in der Ausgabe.\",\"sort_order\":4},{\"id\":137,\"text\":\"A B C\",\"is_correct\":1,\"explanation\":\"Richtig. Der try-Block gibt zuerst A aus, die ValueError wird im except-Block behandelt und finally läuft immer am Ende.\",\"sort_order\":1},{\"id\":139,\"text\":\"B C\",\"is_correct\":0,\"explanation\":\"Falsch. A wird bereits vor dem Auslösen der Exception ausgegeben.\",\"sort_order\":3},{\"id\":138,\"text\":\"A C\",\"is_correct\":0,\"explanation\":\"Falsch. Der except-Block wird ausgeführt, daher fehlt B nicht in der Ausgabe.\",\"sort_order\":2}]}]');

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
(7, 3, NULL, NULL, 'Pascal-A.', 'test3@test.de', '$2y$10$ZtewH.f61IE75yj/aF0omez5rUhgsnOklu7g/Hu/q7NktOioY4mjq', 1, '2026-05-22 09:58:37', '2026-05-29 11:05:02', NULL, 1),
(9, 1, NULL, NULL, 'testspieler', 'test1@test.de', '$2y$10$taKvc2PU2Gu7E9DlMkTWrubQIB8v.b8cXpY4M8mOi9vVWzp331gEy', 1, '2026-05-22 11:13:55', '2026-05-27 13:55:11', NULL, 7),
(10, 2, NULL, NULL, 'Modrzejewski', 'modrzejewski@damago.de', '$2y$10$RDLBr1pha/a3RoVRk5BJEeaTh/yNli1ORAszV3tbUfZdXWDyvr5Ji', 1, '2026-05-27 13:57:34', '2026-05-29 13:04:46', 7, NULL),
(11, 2, NULL, NULL, 'Ralf_Reichts', 'ralf@reichts.de', '$2y$10$s0sWw9DJ51SrrkT7OuiZzu06akSZxkYsrZbZiNz8MFSvo5w/Fah32', 1, '2026-05-28 08:14:04', '2026-05-28 15:04:16', 1, 1);

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
-- Indizes für die Tabelle `player_answers`
--
ALTER TABLE `player_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_player_question` (`lobby_id`,`question_id`,`player_name`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT für Tabelle `lobby_questions`
--
ALTER TABLE `lobby_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=643;

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
-- AUTO_INCREMENT für Tabelle `player_answers`
--
ALTER TABLE `player_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT für Tabelle `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT für Tabelle `question_pools`
--
ALTER TABLE `question_pools`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `quiz_lobbies`
--
ALTER TABLE `quiz_lobbies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
