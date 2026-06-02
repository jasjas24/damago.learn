-- Migration: Avatar-Spalte für Lobby-Spieler hinzufügen
-- Auf einer bereits bestehenden Datenbank einmalig ausführen.

USE `damago_quiz`;

ALTER TABLE `lobby_players`
  ADD COLUMN `avatar` varchar(100) DEFAULT NULL AFTER `player_name`;
