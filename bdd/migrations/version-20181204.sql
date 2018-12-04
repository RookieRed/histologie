-- ------------------
-- Table Echantillon
-- ------------------

ALTER TABLE `Echantillon`
  DROP FOREIGN KEY `FK_Echantillon_idCommande`;

ALTER TABLE `Echantillon`
  ADD CONSTRAINT `FK_Echantillon_idCommande`
  FOREIGN KEY (`idCommande`) REFERENCES `Commande`(`idCommande`)
  ON DELETE CASCADE ON UPDATE RESTRICT;

-- -------------
-- Table Lame
-- -------------

ALTER TABLE `Lame`
  ADD CONSTRAINT `FK_Lame_idEchantillon`
  FOREIGN KEY (`idEchantillon`) REFERENCES `Echantillon`(`idEchantillon`)
  ON DELETE CASCADE ON UPDATE RESTRICT;

ALTER TABLE `Lame`
  ADD CONSTRAINT `FK_Lame_idColoration`
  FOREIGN KEY (`idColoration`) REFERENCES `Coloration`(`idColoration`)
  ON DELETE RESTRICT ON UPDATE RESTRICT;

