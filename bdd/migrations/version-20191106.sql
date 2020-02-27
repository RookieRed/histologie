-- -----------------
-- TABLE UTILISATEUR
-- ------------------
ALTER TABLE `Utilisateur`
  DROP FOREIGN KEY FK_Utilisateur_idEquipe,
  DROP INDEX `FK_Utilisateur_idEquipe`,
  DROP COLUMN `idEquipe`,
  ADD COLUMN `motDePasse` VARCHAR(64) NULL DEFAULT NULL,
  ADD COLUMN `idCentre` int(11) DEFAULT NULL,
  ADD KEY `FK_Equipe_idCentre` (`idCentre`);

ALTER TABLE `Utilisateur`
  ADD CONSTRAINT `FK_Equipe_idCentre` FOREIGN KEY (`idCentre`) REFERENCES `Centre`(`idCentre`)
    ON DELETE RESTRICT ON UPDATE RESTRICT;

-----------------------
-- TABLE EQUIPE
-----------------------
DROP TABLE `Equipe`;


