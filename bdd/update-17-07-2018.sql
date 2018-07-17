ALTER TABLE `Commande`
  CHANGE `commentaireUtilisateur` `commentaireUtilisateur` VARCHAR(1000) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  CHANGE `commentairePlateau` `commentairePlateau` VARCHAR(1000) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;