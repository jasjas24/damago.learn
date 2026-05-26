-- ============================================================
-- Testdaten: Fragenpool "Für kleine Entdecker"
-- Mehrere richtige Antworten pro Frage
-- ============================================================
-- Pool-ID:      3
-- Fragen-IDs:   21 – 30
-- Antwort-IDs:  81 – 120  (4 Antworten pro Frage)
-- created_by:   1  (Admin)
--
-- Einspielen mit:
--   mysql -u <user> -p <datenbank> < testdaten_mehrere_antworten.sql
-- ============================================================

-- ------------------------------------------------------------
-- 1. Fragenpool
-- ------------------------------------------------------------
INSERT INTO `question_pools`
    (`id`, `name`, `description`, `created_by`, `is_active`, `created_at`, `updated_at`, `updated_by`)
VALUES
    (3, 'Für kleine Entdecker',
     'Testpool mit 10 kinderleichten Fragen für ca. 5-Jährige. Jede Frage hat mehrere richtige Antworten.',
     1, 1, '2026-05-26 12:00:00', NULL, NULL);

-- ------------------------------------------------------------
-- 2. Fragen
-- ------------------------------------------------------------
INSERT INTO `questions`
    (`id`, `question_pool_id`, `question_text`, `image_id`, `explanation`, `created_by`, `is_active`, `created_at`, `updated_at`, `updated_by`)
VALUES
(21, 3, 'Welche Farben hat eine Ampel?',
 NULL, 'Eine Ampel hat drei Farben: Rot bedeutet Stopp, Gelb bedeutet Achtung, Grün bedeutet Gehen.',
 1, 1, '2026-05-26 12:00:00', NULL, NULL),

(22, 3, 'Was kann ein Vogel?',
 NULL, 'Vögel können fliegen und singen. Bellen machen Hunde, Miauen machen Katzen.',
 1, 1, '2026-05-26 12:00:00', NULL, NULL),

(23, 3, 'Welche Tiere haben Flügel?',
 NULL, 'Schmetterlinge und Vögel haben Flügel und können damit fliegen. Hunde und Fische haben keine Flügel.',
 1, 1, '2026-05-26 12:00:00', NULL, NULL),

(24, 3, 'Was braucht eine Pflanze zum Wachsen?',
 NULL, 'Pflanzen brauchen Wasser, Sonne und Erde. Einen Fernseher brauchen sie nicht.',
 1, 1, '2026-05-26 12:00:00', NULL, NULL),

(25, 3, 'Welche Zahlen sind kleiner als 5?',
 NULL, '1 und 3 sind kleiner als 5. Die Zahlen 6 und 8 sind größer als 5.',
 1, 1, '2026-05-26 12:00:00', NULL, NULL),

(26, 3, 'Was kann man im Schwimmbad machen?',
 NULL, 'Im Schwimmbad kann man schwimmen und tauchen. Kochen und schlafen macht man zuhause.',
 1, 1, '2026-05-26 12:00:00', NULL, NULL),

(27, 3, 'Welche Dinge leuchten?',
 NULL, 'Die Sonne, eine Lampe und eine Taschenlampe leuchten und geben Licht. Ein Stein leuchtet nicht.',
 1, 1, '2026-05-26 12:00:00', NULL, NULL),

(28, 3, 'Was ist kalt?',
 NULL, 'Eis und Schnee sind kalt. Feuer und die Sonne sind heiß.',
 1, 1, '2026-05-26 12:00:00', NULL, NULL),

(29, 3, 'Welche Tiere leben im Wasser?',
 NULL, 'Fische und Delfine leben im Wasser. Hunde und Katzen leben an Land.',
 1, 1, '2026-05-26 12:00:00', NULL, NULL),

(30, 3, 'Was macht man mit einem Buch?',
 NULL, 'Ein Buch kann man lesen und die Bilder anschauen. Essen und trinken kann man ein Buch nicht.',
 1, 1, '2026-05-26 12:00:00', NULL, NULL);

-- ------------------------------------------------------------
-- 3. Antwortoptionen
--    Spaltenreihenfolge: id, question_id, sort_order,
--                        answer_text, is_correct, explanation,
--                        created_by, created_at, updated_at, updated_by
-- ------------------------------------------------------------
INSERT INTO `answer_options`
    (`id`, `question_id`, `sort_order`, `answer_text`, `is_correct`, `explanation`, `created_by`, `created_at`, `updated_at`, `updated_by`)
VALUES

-- Frage 21: Welche Farben hat eine Ampel? → Rot, Gelb, Grün
(81,  21, 1, 'Rot',   1, 'Richtig! Rot ist eine Ampelfarbe – sie bedeutet Stopp.', 1, '2026-05-26 12:00:00', NULL, NULL),
(82,  21, 2, 'Gelb',  1, 'Richtig! Gelb ist eine Ampelfarbe – sie bedeutet Achtung.', 1, '2026-05-26 12:00:00', NULL, NULL),
(83,  21, 3, 'Grün',  1, 'Richtig! Grün ist eine Ampelfarbe – sie bedeutet Gehen.', 1, '2026-05-26 12:00:00', NULL, NULL),
(84,  21, 4, 'Lila',  0, 'Falsch. Lila gibt es an einer Ampel nicht.', 1, '2026-05-26 12:00:00', NULL, NULL),

-- Frage 22: Was kann ein Vogel? → Fliegen, Singen
(85,  22, 1, 'Fliegen', 1, 'Richtig! Die meisten Vögel können fliegen.', 1, '2026-05-26 12:00:00', NULL, NULL),
(86,  22, 2, 'Singen',  1, 'Richtig! Viele Vögel machen schöne Gesänge.', 1, '2026-05-26 12:00:00', NULL, NULL),
(87,  22, 3, 'Bellen',  0, 'Falsch. Bellen machen Hunde, keine Vögel.', 1, '2026-05-26 12:00:00', NULL, NULL),
(88,  22, 4, 'Miauen',  0, 'Falsch. Miauen machen Katzen, keine Vögel.', 1, '2026-05-26 12:00:00', NULL, NULL),

-- Frage 23: Welche Tiere haben Flügel? → Schmetterling, Vogel
(89,  23, 1, 'Schmetterling', 1, 'Richtig! Schmetterlinge haben bunte Flügel.', 1, '2026-05-26 12:00:00', NULL, NULL),
(90,  23, 2, 'Vogel',         1, 'Richtig! Vögel haben Flügel zum Fliegen.', 1, '2026-05-26 12:00:00', NULL, NULL),
(91,  23, 3, 'Hund',          0, 'Falsch. Hunde haben keine Flügel.', 1, '2026-05-26 12:00:00', NULL, NULL),
(92,  23, 4, 'Fisch',         0, 'Falsch. Fische haben Flossen, aber keine Flügel.', 1, '2026-05-26 12:00:00', NULL, NULL),

-- Frage 24: Was braucht eine Pflanze? → Wasser, Sonne, Erde
(93,  24, 1, 'Wasser',      1, 'Richtig! Ohne Wasser geht eine Pflanze ein.', 1, '2026-05-26 12:00:00', NULL, NULL),
(94,  24, 2, 'Sonne',       1, 'Richtig! Pflanzen brauchen Licht zum Wachsen.', 1, '2026-05-26 12:00:00', NULL, NULL),
(95,  24, 3, 'Erde',        1, 'Richtig! In der Erde halten die Wurzeln Halt und finden Nährstoffe.', 1, '2026-05-26 12:00:00', NULL, NULL),
(96,  24, 4, 'Fernseher',   0, 'Falsch. Einen Fernseher braucht nur der Mensch, nicht die Pflanze.', 1, '2026-05-26 12:00:00', NULL, NULL),

-- Frage 25: Welche Zahlen sind kleiner als 5? → 1, 3
(97,  25, 1, '1', 1, 'Richtig! 1 ist kleiner als 5.', 1, '2026-05-26 12:00:00', NULL, NULL),
(98,  25, 2, '3', 1, 'Richtig! 3 ist kleiner als 5.', 1, '2026-05-26 12:00:00', NULL, NULL),
(99,  25, 3, '6', 0, 'Falsch. 6 ist größer als 5.', 1, '2026-05-26 12:00:00', NULL, NULL),
(100, 25, 4, '8', 0, 'Falsch. 8 ist größer als 5.', 1, '2026-05-26 12:00:00', NULL, NULL),

-- Frage 26: Was kann man im Schwimmbad machen? → Schwimmen, Tauchen
(101, 26, 1, 'Schwimmen', 1, 'Richtig! Schwimmen ist die Hauptsache im Schwimmbad.', 1, '2026-05-26 12:00:00', NULL, NULL),
(102, 26, 2, 'Tauchen',   1, 'Richtig! Unter Wasser tauchen macht viel Spaß.', 1, '2026-05-26 12:00:00', NULL, NULL),
(103, 26, 3, 'Kochen',    0, 'Falsch. Kochen macht man in der Küche, nicht im Schwimmbad.', 1, '2026-05-26 12:00:00', NULL, NULL),
(104, 26, 4, 'Schlafen',  0, 'Falsch. Im Schwimmbad schläft man nicht – man plantscht!', 1, '2026-05-26 12:00:00', NULL, NULL),

-- Frage 27: Welche Dinge leuchten? → Sonne, Lampe, Taschenlampe
(105, 27, 1, 'Sonne',          1, 'Richtig! Die Sonne ist das hellste Licht am Himmel.', 1, '2026-05-26 12:00:00', NULL, NULL),
(106, 27, 2, 'Lampe',          1, 'Richtig! Eine Lampe leuchtet wenn man sie einschaltet.', 1, '2026-05-26 12:00:00', NULL, NULL),
(107, 27, 3, 'Taschenlampe',   1, 'Richtig! Mit einer Taschenlampe kann man im Dunkeln leuchten.', 1, '2026-05-26 12:00:00', NULL, NULL),
(108, 27, 4, 'Stein',          0, 'Falsch. Ein normaler Stein leuchtet nicht.', 1, '2026-05-26 12:00:00', NULL, NULL),

-- Frage 28: Was ist kalt? → Eis, Schnee
(109, 28, 1, 'Eis',    1, 'Richtig! Eis ist sehr kalt – es ist gefrorenes Wasser.', 1, '2026-05-26 12:00:00', NULL, NULL),
(110, 28, 2, 'Schnee', 1, 'Richtig! Schnee ist kalt – man braucht eine warme Jacke.', 1, '2026-05-26 12:00:00', NULL, NULL),
(111, 28, 3, 'Feuer',  0, 'Falsch. Feuer ist heiß, nicht kalt. Nicht anfassen!', 1, '2026-05-26 12:00:00', NULL, NULL),
(112, 28, 4, 'Sonne',  0, 'Falsch. Die Sonne wärmt uns, sie ist nicht kalt.', 1, '2026-05-26 12:00:00', NULL, NULL),

-- Frage 29: Welche Tiere leben im Wasser? → Fisch, Delfin
(113, 29, 1, 'Fisch',   1, 'Richtig! Fische leben im Wasser und können nicht an Land.', 1, '2026-05-26 12:00:00', NULL, NULL),
(114, 29, 2, 'Delfin',  1, 'Richtig! Delfine leben im Meer und sind sehr klug.', 1, '2026-05-26 12:00:00', NULL, NULL),
(115, 29, 3, 'Hund',    0, 'Falsch. Hunde leben an Land, auch wenn manche gern schwimmen.', 1, '2026-05-26 12:00:00', NULL, NULL),
(116, 29, 4, 'Katze',   0, 'Falsch. Katzen mögen kein Wasser und leben an Land.', 1, '2026-05-26 12:00:00', NULL, NULL),

-- Frage 30: Was macht man mit einem Buch? → Lesen, Anschauen
(117, 30, 1, 'Lesen',      1, 'Richtig! Bücher sind zum Lesen da.', 1, '2026-05-26 12:00:00', NULL, NULL),
(118, 30, 2, 'Anschauen',  1, 'Richtig! Besonders Bilderbücher schaut man gerne an.', 1, '2026-05-26 12:00:00', NULL, NULL),
(119, 30, 3, 'Essen',      0, 'Falsch. Ein Buch isst man nicht – das schmeckt nicht gut!', 1, '2026-05-26 12:00:00', NULL, NULL),
(120, 30, 4, 'Trinken',    0, 'Falsch. Ein Buch kann man nicht trinken.', 1, '2026-05-26 12:00:00', NULL, NULL);
