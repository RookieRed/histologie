-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
--
-- Généré le :  Mar 25 Septembre 2018 à 12:33
-- Version du serveur :  10.1.26-MariaDB-0+deb9u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données :  `histologie`
--

-- --------------------------------------------------------

--
-- Structure de la table `Administrateur`
--

DROP TABLE IF EXISTS `Administrateur`;
CREATE TABLE `Administrateur` (
  `idAdmin` int(11) NOT NULL,
  `nomAdmin` varchar(30) DEFAULT NULL,
  `passAdmin` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Animal`
--

DROP TABLE IF EXISTS `Animal`;
CREATE TABLE `Animal` (
  `idAnimal` int(11) NOT NULL,
  `typeAnimal` varchar(30) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Centre`
--

DROP TABLE IF EXISTS `Centre`;
CREATE TABLE `Centre` (
  `idCentre` int(11) NOT NULL,
  `nomCentre` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Coloration`
--

DROP TABLE IF EXISTS `Coloration`;
CREATE TABLE `Coloration` (
  `idColoration` int(11) NOT NULL,
  `nomColoration` varchar(15) NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Commande`
--

DROP TABLE IF EXISTS `Commande`;
CREATE TABLE `Commande` (
  `idCommande` int(11) NOT NULL,
  `numCommande` varchar(20) NOT NULL,
  `dateCommande` date NOT NULL,
  `dateReceptionCommande` date DEFAULT NULL,
  `dateRetourCommande` date DEFAULT NULL,
  `dateFacturationCommande` date DEFAULT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `commentaireUtilisateur` varchar(2000) DEFAULT NULL,
  `commentairePlateau` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Echantillon`
--

DROP TABLE IF EXISTS `Echantillon`;
CREATE TABLE `Echantillon` (
  `idEchantillon` int(11) NOT NULL,
  `numEchantillon` varchar(30) NOT NULL,
  `identAnimalEchantillon` varchar(50) NOT NULL,
  `dateInclusion` date DEFAULT NULL,
  `dateCoupe` date DEFAULT NULL,
  `idCommande` int(11) NOT NULL,
  `idOrgane` int(11) NOT NULL,
  `idAnimal` int(11) NOT NULL,
  `idInclusion` int(11) DEFAULT NULL,
  `epaisseurCoupes` tinyint(4) DEFAULT NULL,
  `nbCoupes` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Equipe`
--

DROP TABLE IF EXISTS `Equipe`;
CREATE TABLE `Equipe` (
  `idEquipe` int(11) NOT NULL,
  `nomEquipe` varchar(30) NOT NULL,
  `idCentre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Inclusion`
--

DROP TABLE IF EXISTS `Inclusion`;
CREATE TABLE `Inclusion` (
  `idInclusion` int(11) NOT NULL,
  `nomInclusion` varchar(30) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Lame`
--

DROP TABLE IF EXISTS `Lame`;
CREATE TABLE `Lame` (
  `idLame` int(11) NOT NULL,
  `numLame` varchar(30) NOT NULL,
  `dateColoration` date DEFAULT NULL,
  `idEchantillon` int(11) NOT NULL,
  `idColoration` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Organe`
--

DROP TABLE IF EXISTS `Organe`;
CREATE TABLE `Organe` (
  `idOrgane` int(11) NOT NULL,
  `nomOrgane` varchar(30) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Utilisateur`
--

DROP TABLE IF EXISTS `Utilisateur`;
CREATE TABLE `Utilisateur` (
  `idUtilisateur` int(11) NOT NULL,
  `nomUtilisateur` varchar(30) NOT NULL,
  `prenomUtilisateur` varchar(30) NOT NULL,
  `mailUtilisateur` varchar(50) NOT NULL,
  `idEquipe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `Administrateur`
--
ALTER TABLE `Administrateur`
  ADD PRIMARY KEY (`idAdmin`);

--
-- Index pour la table `Animal`
--
ALTER TABLE `Animal`
  ADD PRIMARY KEY (`idAnimal`);

--
-- Index pour la table `Centre`
--
ALTER TABLE `Centre`
  ADD PRIMARY KEY (`idCentre`);

--
-- Index pour la table `Coloration`
--
ALTER TABLE `Coloration`
  ADD PRIMARY KEY (`idColoration`);

--
-- Index pour la table `Commande`
--
ALTER TABLE `Commande`
  ADD PRIMARY KEY (`idCommande`),
  ADD KEY `FK_Commande_idUtilisateur` (`idUtilisateur`);

--
-- Index pour la table `Echantillon`
--
ALTER TABLE `Echantillon`
  ADD PRIMARY KEY (`idEchantillon`),
  ADD KEY `FK_Echantillon_idCommande` (`idCommande`),
  ADD KEY `FK_Echantillon_idOrgane` (`idOrgane`),
  ADD KEY `FK_Echantillon_idAnimal` (`idAnimal`),
  ADD KEY `FK_Echantillon_idInclusion` (`idInclusion`);

--
-- Index pour la table `Equipe`
--
ALTER TABLE `Equipe`
  ADD PRIMARY KEY (`idEquipe`),
  ADD KEY `FK_Equipe_idCentre` (`idCentre`);

--
-- Index pour la table `Inclusion`
--
ALTER TABLE `Inclusion`
  ADD PRIMARY KEY (`idInclusion`);

--
-- Index pour la table `Lame`
--
ALTER TABLE `Lame`
  ADD PRIMARY KEY (`idLame`);

--
-- Index pour la table `Organe`
--
ALTER TABLE `Organe`
  ADD PRIMARY KEY (`idOrgane`);

--
-- Index pour la table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  ADD PRIMARY KEY (`idUtilisateur`),
  ADD KEY `FK_Utilisateur_idEquipe` (`idEquipe`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `Administrateur`
--
ALTER TABLE `Administrateur`
  MODIFY `idAdmin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `Animal`
--
ALTER TABLE `Animal`
  MODIFY `idAnimal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT pour la table `Centre`
--
ALTER TABLE `Centre`
  MODIFY `idCentre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `Coloration`
--
ALTER TABLE `Coloration`
  MODIFY `idColoration` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT pour la table `Commande`
--
ALTER TABLE `Commande`
  MODIFY `idCommande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;
--
-- AUTO_INCREMENT pour la table `Echantillon`
--
ALTER TABLE `Echantillon`
  MODIFY `idEchantillon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3631;
--
-- AUTO_INCREMENT pour la table `Equipe`
--
ALTER TABLE `Equipe`
  MODIFY `idEquipe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT pour la table `Inclusion`
--
ALTER TABLE `Inclusion`
  MODIFY `idInclusion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT pour la table `Lame`
--
ALTER TABLE `Lame`
  MODIFY `idLame` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5069;
--
-- AUTO_INCREMENT pour la table `Organe`
--
ALTER TABLE `Organe`
  MODIFY `idOrgane` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
--
-- AUTO_INCREMENT pour la table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  MODIFY `idUtilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `Commande`
--
ALTER TABLE `Commande`
  ADD CONSTRAINT `FK_Commande_idUtilisateur` FOREIGN KEY (`idUtilisateur`) REFERENCES `Utilisateur` (`idUtilisateur`);

--
-- Contraintes pour la table `Echantillon`
--
ALTER TABLE `Echantillon`
  ADD CONSTRAINT `FK_Echantillon_idCommande` FOREIGN KEY (`idCommande`) REFERENCES `Commande` (`idCommande`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_Echantillon_idAnimal` FOREIGN KEY (`idAnimal`) REFERENCES `Animal` (`idAnimal`),
  ADD CONSTRAINT `FK_Echantillon_idInclusion` FOREIGN KEY (`idInclusion`) REFERENCES `Inclusion` (`idInclusion`),
  ADD CONSTRAINT `FK_Echantillon_idOrgane` FOREIGN KEY (`idOrgane`) REFERENCES `Organe` (`idOrgane`);

--
-- Contraintes pour la table `Equipe`
--
ALTER TABLE `Equipe`
  ADD CONSTRAINT `FK_Equipe_idCentre` FOREIGN KEY (`idCentre`) REFERENCES `Centre` (`idCentre`);

--
-- Contraintes pour la table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  ADD CONSTRAINT `FK_Utilisateur_idEquipe` FOREIGN KEY (`idEquipe`) REFERENCES `Equipe` (`idEquipe`);

INSERT INTO `Administrateur` (`nomAdmin`, `passAdmin`)
  VALUES ('admin', '$2y$10$Y6Du4GoI1g/Cfm5u99OdXu9634kHR/iO0ceJapY1dTjnlAMXuvqN2');
