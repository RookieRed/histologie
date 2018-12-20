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


-- ----------------
-- Table Fichiers
-- ----------------

CREATE TABLE `histologie`.`Fichier`
(
  `idFichier` INT NOT NULL AUTO_INCREMENT ,
  `nomFichier` VARCHAR(70) NOT NULL ,
  `cheminFichier` VARCHAR(256) NOT NULL ,
  `typeFichier` VARCHAR(20) NOT NULL ,
  `dateCreation` DATETIME DEFAULT NOW() NOT NULL ,
  PRIMARY KEY (`idFichier`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `Fichier`(`nomFichier`, `cheminFichier`, `typeFichier`)
VALUES ('histo.jpg', '/web/img/', 'LOGO');
