-- ============================================================
-- Testdaten: Fragenpool "Das kleine 1x1"
-- ============================================================
-- Pool-ID:      2
-- Fragen-IDs:   11 – 20
-- Antwort-IDs:  41 – 80  (4 Antworten pro Frage)
-- created_by:   1  (Admin)
--
-- Einspielen mit:
--   mysql -u <user> -p <datenbank> < testdaten_kleines_einmaleins.sql
-- ============================================================

-- ------------------------------------------------------------
-- 1. Fragenpool
-- ------------------------------------------------------------
INSERT INTO `question_pools`
    (`id`, `name`, `description`, `created_by`, `is_active`, `created_at`, `updated_at`, `updated_by`)
VALUES
    (2, 'Das kleine 1x1',
     'Testpool mit 10 Multiplikationsaufgaben aus dem kleinen Einmaleins (1–10). Jede Frage hat genau eine richtige Antwort.',
     1, 1, '2026-05-26 10:00:00', NULL, NULL);

-- ------------------------------------------------------------
-- 2. Fragen
-- ------------------------------------------------------------
INSERT INTO `questions`
    (`id`, `question_pool_id`, `question_text`, `image_id`, `explanation`, `created_by`, `is_active`, `created_at`, `updated_at`, `updated_by`)
VALUES
(11, 2, 'Was ist 3 × 4?',
 NULL, '3 × 4 bedeutet: dreimal die 4 addieren → 4 + 4 + 4 = 12.',
 1, 1, '2026-05-26 10:00:00', NULL, NULL),

(12, 2, 'Was ist 6 × 7?',
 NULL, '6 × 7 = 42. Eine gute Merkhilfe: „Sechs mal sieben ist zwei und vierzig" – eine der häufig verwechselten Aufgaben im Einmaleins.',
 1, 1, '2026-05-26 10:00:00', NULL, NULL),

(13, 2, 'Was ist 8 × 9?',
 NULL, '8 × 9 = 72. Merktrick: 7, 8, 9 → 7 × 8 = 56, 8 × 9 = 72 (die Ziffern 7 und 2 folgen dem Muster der aufsteigenden Reihe).',
 1, 1, '2026-05-26 10:00:00', NULL, NULL),

(14, 2, 'Was ist 4 × 5?',
 NULL, '4 × 5 = 20. Die Fünferreihe liefert immer Vielfache von 5: 5, 10, 15, 20 – das vierte Glied ist 20.',
 1, 1, '2026-05-26 10:00:00', NULL, NULL),

(15, 2, 'Was ist 7 × 8?',
 NULL, '7 × 8 = 56. Merkhilfe: „Sieben mal acht – fünf und sechs macht" (56).',
 1, 1, '2026-05-26 10:00:00', NULL, NULL),

(16, 2, 'Was ist 9 × 6?',
 NULL, '9 × 6 = 54. In der Neunerreihe sinkt die Zehnerstelle um 1 und steigt die Einerstelle um 1: 9, 18, 27, 36, 45, 54.',
 1, 1, '2026-05-26 10:00:00', NULL, NULL),

(17, 2, 'Was ist 5 × 5?',
 NULL, '5 × 5 = 25. Jede Quadratzahl der 5 endet auf 25: 5² = 25, 15² = 225, 25² = 625.',
 1, 1, '2026-05-26 10:00:00', NULL, NULL),

(18, 2, 'Was ist 3 × 9?',
 NULL, '3 × 9 = 27. In der Dreierreihe: 3, 6, 9, 12, 15, 18, 21, 24, 27 – das neunte Glied.',
 1, 1, '2026-05-26 10:00:00', NULL, NULL),

(19, 2, 'Was ist 6 × 6?',
 NULL, '6 × 6 = 36. Quadratzahlen sind nützlich: 1, 4, 9, 16, 25, 36 – 36 ist das Quadrat von 6.',
 1, 1, '2026-05-26 10:00:00', NULL, NULL),

(20, 2, 'Was ist 7 × 4?',
 NULL, '7 × 4 = 28. Die Vierreihe: 4, 8, 12, 16, 20, 24, 28 – das siebte Glied ist 28.',
 1, 1, '2026-05-26 10:00:00', NULL, NULL);

-- ------------------------------------------------------------
-- 3. Antwortoptionen  (4 pro Frage, sort_order 1–4)
--    Spaltenreihenfolge: id, question_id, sort_order,
--                        answer_text, is_correct, explanation,
--                        created_by, created_at, updated_at, updated_by
-- ------------------------------------------------------------
INSERT INTO `answer_options`
    (`id`, `question_id`, `sort_order`, `answer_text`, `is_correct`, `explanation`, `created_by`, `created_at`, `updated_at`, `updated_by`)
VALUES

-- Frage 11: 3 × 4 = 12
(41, 11, 1, '12', 1, 'Richtig. 3 × 4 = 12.', 1, '2026-05-26 10:00:00', NULL, NULL),
(42, 11, 2, '9',  0, 'Falsch. 9 wäre das Ergebnis von 3 × 3, nicht 3 × 4.', 1, '2026-05-26 10:00:00', NULL, NULL),
(43, 11, 3, '16', 0, 'Falsch. 16 ist das Ergebnis von 4 × 4.', 1, '2026-05-26 10:00:00', NULL, NULL),
(44, 11, 4, '11', 0, 'Falsch. 11 ist keine Zahl aus der Dreier- oder Vierreihe im kleinen Einmaleins.', 1, '2026-05-26 10:00:00', NULL, NULL),

-- Frage 12: 6 × 7 = 42
(45, 12, 1, '42', 1, 'Richtig. 6 × 7 = 42.', 1, '2026-05-26 10:00:00', NULL, NULL),
(46, 12, 2, '36', 0, 'Falsch. 36 ist das Ergebnis von 6 × 6.', 1, '2026-05-26 10:00:00', NULL, NULL),
(47, 12, 3, '48', 0, 'Falsch. 48 ist das Ergebnis von 6 × 8.', 1, '2026-05-26 10:00:00', NULL, NULL),
(48, 12, 4, '35', 0, 'Falsch. 35 ist das Ergebnis von 5 × 7.', 1, '2026-05-26 10:00:00', NULL, NULL),

-- Frage 13: 8 × 9 = 72
(49, 13, 1, '72', 1, 'Richtig. 8 × 9 = 72.', 1, '2026-05-26 10:00:00', NULL, NULL),
(50, 13, 2, '63', 0, 'Falsch. 63 ist das Ergebnis von 7 × 9.', 1, '2026-05-26 10:00:00', NULL, NULL),
(51, 13, 3, '81', 0, 'Falsch. 81 ist das Ergebnis von 9 × 9.', 1, '2026-05-26 10:00:00', NULL, NULL),
(52, 13, 4, '64', 0, 'Falsch. 64 ist das Ergebnis von 8 × 8.', 1, '2026-05-26 10:00:00', NULL, NULL),

-- Frage 14: 4 × 5 = 20
(53, 14, 1, '20', 1, 'Richtig. 4 × 5 = 20.', 1, '2026-05-26 10:00:00', NULL, NULL),
(54, 14, 2, '16', 0, 'Falsch. 16 ist das Ergebnis von 4 × 4.', 1, '2026-05-26 10:00:00', NULL, NULL),
(55, 14, 3, '25', 0, 'Falsch. 25 ist das Ergebnis von 5 × 5.', 1, '2026-05-26 10:00:00', NULL, NULL),
(56, 14, 4, '18', 0, 'Falsch. 18 ist das Ergebnis von 3 × 6 oder 2 × 9.', 1, '2026-05-26 10:00:00', NULL, NULL),

-- Frage 15: 7 × 8 = 56
(57, 15, 1, '56', 1, 'Richtig. 7 × 8 = 56.', 1, '2026-05-26 10:00:00', NULL, NULL),
(58, 15, 2, '49', 0, 'Falsch. 49 ist das Ergebnis von 7 × 7.', 1, '2026-05-26 10:00:00', NULL, NULL),
(59, 15, 3, '64', 0, 'Falsch. 64 ist das Ergebnis von 8 × 8.', 1, '2026-05-26 10:00:00', NULL, NULL),
(60, 15, 4, '54', 0, 'Falsch. 54 ist das Ergebnis von 6 × 9.', 1, '2026-05-26 10:00:00', NULL, NULL),

-- Frage 16: 9 × 6 = 54
(61, 16, 1, '54', 1, 'Richtig. 9 × 6 = 54.', 1, '2026-05-26 10:00:00', NULL, NULL),
(62, 16, 2, '63', 0, 'Falsch. 63 ist das Ergebnis von 9 × 7.', 1, '2026-05-26 10:00:00', NULL, NULL),
(63, 16, 3, '48', 0, 'Falsch. 48 ist das Ergebnis von 6 × 8.', 1, '2026-05-26 10:00:00', NULL, NULL),
(64, 16, 4, '45', 0, 'Falsch. 45 ist das Ergebnis von 9 × 5.', 1, '2026-05-26 10:00:00', NULL, NULL),

-- Frage 17: 5 × 5 = 25
(65, 17, 1, '25', 1, 'Richtig. 5 × 5 = 25. Quadratzahl der 5.', 1, '2026-05-26 10:00:00', NULL, NULL),
(66, 17, 2, '20', 0, 'Falsch. 20 ist das Ergebnis von 4 × 5.', 1, '2026-05-26 10:00:00', NULL, NULL),
(67, 17, 3, '30', 0, 'Falsch. 30 ist das Ergebnis von 5 × 6.', 1, '2026-05-26 10:00:00', NULL, NULL),
(68, 17, 4, '10', 0, 'Falsch. 10 ist das Ergebnis von 5 × 2.', 1, '2026-05-26 10:00:00', NULL, NULL),

-- Frage 18: 3 × 9 = 27
(69, 18, 1, '27', 1, 'Richtig. 3 × 9 = 27.', 1, '2026-05-26 10:00:00', NULL, NULL),
(70, 18, 2, '21', 0, 'Falsch. 21 ist das Ergebnis von 3 × 7.', 1, '2026-05-26 10:00:00', NULL, NULL),
(71, 18, 3, '24', 0, 'Falsch. 24 ist das Ergebnis von 3 × 8.', 1, '2026-05-26 10:00:00', NULL, NULL),
(72, 18, 4, '30', 0, 'Falsch. 30 ist das Ergebnis von 3 × 10.', 1, '2026-05-26 10:00:00', NULL, NULL),

-- Frage 19: 6 × 6 = 36
(73, 19, 1, '36', 1, 'Richtig. 6 × 6 = 36. Quadratzahl der 6.', 1, '2026-05-26 10:00:00', NULL, NULL),
(74, 19, 2, '30', 0, 'Falsch. 30 ist das Ergebnis von 5 × 6.', 1, '2026-05-26 10:00:00', NULL, NULL),
(75, 19, 3, '42', 0, 'Falsch. 42 ist das Ergebnis von 6 × 7.', 1, '2026-05-26 10:00:00', NULL, NULL),
(76, 19, 4, '32', 0, 'Falsch. 32 ist das Ergebnis von 4 × 8.', 1, '2026-05-26 10:00:00', NULL, NULL),

-- Frage 20: 7 × 4 = 28
(77, 20, 1, '28', 1, 'Richtig. 7 × 4 = 28.', 1, '2026-05-26 10:00:00', NULL, NULL),
(78, 20, 2, '21', 0, 'Falsch. 21 ist das Ergebnis von 7 × 3.', 1, '2026-05-26 10:00:00', NULL, NULL),
(79, 20, 3, '32', 0, 'Falsch. 32 ist das Ergebnis von 4 × 8.', 1, '2026-05-26 10:00:00', NULL, NULL),
(80, 20, 4, '24', 0, 'Falsch. 24 ist das Ergebnis von 4 × 6.', 1, '2026-05-26 10:00:00', NULL, NULL);
