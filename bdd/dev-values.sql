--
-- Contenu de la table `Animal`
--

INSERT INTO `Animal` (`idAnimal`, `typeAnimal`, `visible`) VALUES
(1, 'Souris', 1),
(2, 'Poisson', 1),
(25, 'test', 0),
(26, 'Rat', 1),
(27, 'Humain', 1),
(28, '', 0),
(29, 'okay', 0),
(30, 'ey', 0);

--
-- Contenu de la table `Centre`
--

INSERT INTO `Centre` (`idCentre`, `nomCentre`) VALUES
(3, '1048');

--
-- Contenu de la table `Coloration`
--

INSERT INTO `Coloration` (`idColoration`, `nomColoration`, `visible`) VALUES
(4, 'HE', 1),
(5, 'TM', 1),
(6, 'RS', 1),
(7, 'ORO', 1);

--
-- Contenu de la table `Equipe`
--

INSERT INTO `Equipe` (`idEquipe`, `nomEquipe`, `idCentre`) VALUES
(3, '00', 3),
(4, '01', 3),
(5, '02', 3),
(6, '03', 3),
(7, '04', 3),
(8, '10', 3),
(9, '14', 3),
(10, '05', 3),
(11, '08', 3),
(12, '07', 3),
(13, '09', 3),
(14, '11', 3);

--
-- Contenu de la table `Inclusion`
--

INSERT INTO `Inclusion` (`idInclusion`, `nomInclusion`, `visible`) VALUES
(1, 'Longitudinal', 1),
(2, 'Transversal', 1),
(5, 'A plat', 1),
(12, 'vertical', 0);

--
-- Contenu de la table `Organe`
--

INSERT INTO `Organe` (`idOrgane`, `nomOrgane`, `visible`) VALUES
(1, 'Poumon', 1),
(2, 'Foie', 1),
(3, 'Rein', 1),
(4, 'Intestin', 1),
(22, 'Coeur', 1),
(23, 'Artère', 1),
(24, 'Tissu adipeux blanc', 1),
(25, 'Tissu adipeux brun', 1),
(26, 'Tissu adipeux SC', 1),
(27, 'Tissu adipeux PG', 1),
(28, 'Utérus', 1),
(29, 'Vagin', 1),
(30, 'Glande mammaire', 1),
(31, 'Muscle', 1),
(32, 'placenta', 0),
(33, 'Rate', 0),
(34, 'Mesentere', 0),
(35, 'Pancreas', 0),
(36, 'Foie+PG', 0),
(37, 'Tissu adipeux mésentérique', 1),
(38, 'i WAT', 1),
(39, 'e WAT', 1),
(40, 'prostate', 0);

--
-- Contenu d'Utilisateur
--

INSERT INTO Utilisateur (`idUtilisateur`, `nomUtilisateur`, `prenomUtilisateur`, `mailUtilisateur`, `idEquipe`) VALUES
(1, 'Paul', 'MARTIN', 'test@yopmail.com', 3);
