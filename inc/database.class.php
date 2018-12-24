<?php
class Database {
    private $_handler;
    private $_logger;
    private $_lastNumRows;

    /**
     * Effectue la connexion à la base de données
     *
     * @param string $host Adresse du serveur mysql
     * @param string $login Nom de l'utilisateur à connecter
     * @param string $password Mot de passe
     * @param $dbName
     * @param Logger $logger
     * @throws Exception
     */
    public function __construct($host, $login, $password, $dbName, Logger $logger)
    {
        $this->_logger = $logger;
        try {
            //Options de connexion
            $options = [
                PDO::MYSQL_ATTR_FOUND_ROWS => true
            ];
            //Connexion
            $this->_handler = new PDO('mysql:host=' . $host . ';dbname=' . $dbName . ';charset=UTF8', $login, $password, $options);
            //Paramètres PDO
            $this->_handler->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->_handler->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        catch (Exception $e) {
            $this->_logger->log("Database", "ERROR", "Echec de la connexion à la base de données");
            throw new Exception('Erreur de connexion à la base de données :' . $e->getMessage());
        }
        $this->_logger->log("Database", "DEBUG", "Connecté à la base de données");
    }

   /**
    * Effectue une requête SQL renvoyant un résultat
    *
    * Cette fonction est adaptée aux requêtes SELECT
    *
    * @param string $sql Requête SQL à exécuter
    * @param Mixed $args Les arguments suivants seront utilisés en tant que paramètre de la requête sql.
    *
    * @return Array retourne un tableau contenant les résultats de la requête, ou null si la requête a échoué
    */
    private function query($sql)
    {
        //Récupération des arguments de la requête
        $args = func_get_args();
        array_shift($args);
        $request = $this->_handler->prepare($sql);
        if($request ==! false) {
            if($request->execute($args))
            {
                $resultArray = $request->fetchAll();
                $request->closeCursor();
                return $resultArray;
            }
            else {
                $this->_logger->log("Database", "WARNING",
                    "Requête échouée : \"" . $sql . "\" paramètres : " . print_r($args, true));
                return null;
            }
        } else {
            $this->_logger->log("Database", "ERROR", "Requête incorrecte : " . $sql);
            return null;
        }
    }

   /**
    * Effectue une requête SQL ne renvoyant pas de résultat
    *
    * Cette fonction est adaptée aux requêtes UPDATE, INSERT, DELETE, ... ne renvoyant pas de données.
    *
    * @param string $sql Requête SQL à exécuter
    * @param Mixed $args Les paramètres suivants seront utilisés en tant que paramètre de la requête sql.
    *
    * @return boolean Retourne Vrai si la requête est effectuée avec succès, Faux sinon
    */
    private function execute($sql)
    {
        //Récupération des arguments de la requête
        $args = func_get_args();
        array_shift($args);
        $request = $this->_handler->prepare($sql);
        if($request ==! false) {
            if($request->execute($args))
            {
                $this->_lastNumRows = $request->rowCount();
                return true;
            }
            else {
                $this->_logger->log("Database", "WARNING",
                    "Requête échouée : \"" . $sql . "\" paramètres : " . print_r($args, true));
                return false;
            }
        } else {
            $this->_logger->log("Database", "ERROR", "Requête incorrecte : " . $sql);
            return false;
        }
    }

    /*
    * Retourne l'objet PDO
    */
    public function getHandler()
    {
        return $this->_handler;
    }

    //Utilisateur

    public function getIdUtilisateur($mail)
    {
        $result = $this->query('SELECT idUtilisateur FROM Utilisateur WHERE mailUtilisateur = ?', $mail);
        if(isset($result[0]['idUtilisateur']))
            return $result[0]['idUtilisateur'];
        return false;
    }

    //Insère un utilisateur, ainsi que son centre et son équipe si ils n'existent pas
    public function insererUtilisateur($mail, $nom, $prenom, $nomCentre, $nomEquipe)
    {
        $idCentre = $this->getIdCentre($nomCentre);
        if($idCentre === false && $this->ajouterCentre($nomCentre))
        {
            $idCentre = $this->_handler->lastInsertId();
        }
        $idEquipe = $this->getIdEquipe($nomEquipe, $idCentre);
        if($idEquipe === false && $this->ajouterEquipe($nomEquipe, $idCentre))
        {
            $idEquipe = $this->_handler->lastInsertId();
        }
        if($this->execute('INSERT INTO Utilisateur (nomUtilisateur, prenomUtilisateur, mailUtilisateur, idEquipe)
                           VALUES (?, ?, ?, ?)', $nom, $prenom, $mail, $idEquipe))
        {
            return $this->_handler->lastInsertId();
        }
        return false;
    }

    public function getUtilisateur($idUtilisateur)
    {
        $result = $this->query('SELECT nomUtilisateur, prenomUtilisateur, mailUtilisateur, nomEquipe, nomCentre
                                FROM Utilisateur u
                                INNER JOIN Equipe e ON u.idEquipe = e.idEquipe
                                INNER JOIN Centre c ON e.idCentre = c.idCentre
                                WHERE idUtilisateur = ?', $idUtilisateur);
        if(isset($result[0]))
            return $result[0];
    }

    public function getUtilisateurFromCommande($idCommande)
    {
        $result = $this->query('SELECT mailUtilisateur, nomUtilisateur, prenomUtilisateur FROM Utilisateur u
                                INNER JOIN Commande c ON c.idUtilisateur = u.idUtilisateur
                                WHERE c.idCommande = ?', $idCommande);
        if(isset($result[0]))
        {
            return $result[0];
        }
    }

    //Commande

    //Retourne le nombre de commandes effectuées pendant l'année passée en paramètre
    public function getNbCommandePourAnnee($annee, $type)
    {
        $result = $this->query('SELECT COUNT(*) nbCommandes FROM Commande WHERE YEAR(dateCommande) = ? AND LEFT(numCommande, 1) = ?', $annee, $type);
        if(isset($result[0]['nbCommandes']))
            return $result[0]['nbCommandes'];
        return 0;
    }

    public function getNumCommande($idCommande)
    {
        $result = $this->query('SELECT numCommande FROM Commande WHERE idCommande = ?', $idCommande);
        if(isset($result[0]['numCommande']))
            return $result[0]['numCommande'];
    }

    //Retourne une liste de commandes à réceptionner
    public function getCommandesAReceptionner($type)
    {
        return $this->query('SELECT idCommande id, idCommande cid, numCommande "Commande N°", CONCAT(u.prenomUtilisateur, \' \', u.nomUtilisateur) "Utilisateur"
                            FROM Commande c, Utilisateur u
                            WHERE dateReceptionCommande IS NULL AND LEFT(numCommande, 1) = ?
                              AND c.idUtilisateur = u.idUtilisateur
                            ORDER BY dateCommande', $type);
    }

    //Retourne le nombre de commandes à réceptionner
    public function getNbCommandesAReceptionner($type)
    {
        $result = $this->query('SELECT COUNT(*) nbCommandes FROM Commande
                                WHERE dateReceptionCommande IS NULL AND LEFT(numCommande, 1) = ?
                                ORDER BY dateCommande', $type);
        if(isset($result[0]['nbCommandes']))
        {
            return $result[0]['nbCommandes'];
        }
        return 0;
    }

    //Enregistre qu'une commande a été reçue
    public function setCommandeRecue($idCommande, $dateReception)
    {
        return $this->execute('UPDATE Commande SET dateReceptionCommande = ? WHERE idCommande = ?', $dateReception, $idCommande);
    }

    //Retourne une liste de commandes prêtes à être renvoyées
    public function getCommandesARenvoyer($type)
    {
        $commandes = $this->query('SELECT DISTINCT c.idCommande id, c.idCommande cid, numCommande "Commande N°", CONCAT(u.prenomUtilisateur, \' \', u.nomUtilisateur) "Utilisateur"
                                   FROM Commande c
                                   LEFT JOIN Echantillon e ON c.idCommande = e.idCommande
                                   LEFT JOIN Lame l ON e.idEchantillon = l.idEchantillon
                                   INNER JOIN Utilisateur u ON u.idUtilisateur = c.idUtilisateur
                                   INNER JOIN Equipe eq ON u.idEquipe = eq.idEquipe
                                   INNER JOIN Centre ce ON ce.idCentre = eq.idCentre
                                   WHERE LEFT(numCommande, 1) = ? AND dateRetourCommande IS NULL
                                   GROUP BY c.idCommande, numCommande, commentairePlateau, dateRetourCommande, dateCommande
                                   HAVING MIN(
                                       CASE
                                           WHEN dateReceptionCommande IS NULL THEN "En attente de réception"
                                           WHEN dateRetourCommande IS NOT NULL THEN "Retournée"
                                           WHEN l.idColoration IS NOT NULL AND l.dateColoration IS NULL THEN "Coloration"
                                           WHEN e.epaisseurCoupes IS NOT NULL AND e.dateCoupe IS NULL THEN "Coupe"
                                           WHEN e.idInclusion IS NOT NULL AND e.dateInclusion IS NULL THEN "Inclusion"
                                           ELSE "Terminée"
                                       END
                                   ) = "Terminée"
                                   ORDER BY c.idCommande DESC', $type);
        return $commandes;
    }

    //Retourne le nombre de commandes prêtes à être renvoyées
    public function getNbCommandesARenvoyer($type)
    {
        $commandesARenvoyer = $this->getCommandesARenvoyer($type);
        if ($commandesARenvoyer == null)
            return 0;
        return count($commandesARenvoyer);
    }

    //Enregistre qu'une commande a bien été renvoyée
    public function setCommandeRenvoyee($idCommande, $dateRetour, $commentaire = "")
    {
        return $this->execute('UPDATE Commande SET dateRetourCommande = ?, commentairePlateau = ? WHERE idCommande = ?', $dateRetour, $commentaire, $idCommande);
    }

    //Insère une commande ainsi que ses échantillons
    public function insererCommande($idUtilisateur, $numProvisoire, $echantillons, $commentaireUtilisateur)
    {
        $success = true;
        $this->_handler->beginTransaction();
        $regex = '/((P|C)[\d]{4}-[\d]{2}-[\d]{2}-)[\d]+/';
        preg_match($regex, $numProvisoire, $matches);
        $numFinal = $matches[1] . ($this->getNbCommandePourAnnee(date("Y"), substr($numProvisoire, 0, 1)) + 1);
        $success = $success && $this->execute('INSERT INTO Commande (numCommande, dateCommande, idUtilisateur, commentaireUtilisateur)
                        VALUES (?, NOW(), ?, ?)', $numFinal, $idUtilisateur, $commentaireUtilisateur);
        if($success)
        {
            $idCommande = $this->_handler->lastInsertId();
            foreach($echantillons as $key => $echantillon)
            {
                $success = $success && $this->insererEchantillon($idCommande, $numFinal . "-" . ($key + 1),
                    $echantillon['identAnimal'], $echantillon['nbCoupes'], $echantillon['epaisseurCoupes'],
                    $echantillon['organe'], $echantillon['animal'],
                    $echantillon['inclusion'] == 1 ? $echantillon['sensInclusion'] : null, $echantillon['lames']);
            }
        }
        if($success)
        {
            $this->_handler->commit();
            return $idCommande;
        }
        else {
            $this->_handler->rollBack();
            return false;
        }
    }

    /*
    * Récupère les archives filtrées en fonction des données du formulaire
    */
    public function getArchives($type, $commande, $equipe, $annee, $utilisateur, $echantillon)
    {
        //On échappe les caractères spéciaux de MySQL, on ajoute des % à chaque extrémité des paramètres afin
        //de pouvoir effectuer des recherches partielles (sur le nom de famille par exemple)
        $commande = "%" . str_replace(["%", "_"], ["\\\\%", "\\\\_"], $commande) . "%";
        $utilisateur = "%" . str_replace(["%", "_"], ["\\\\%", "\\\\_"], $utilisateur) . "%";
        $echantillon = "%" . str_replace(["%", "_"], ["\\\\%", "\\\\_"], $echantillon) . "%";
        //Si l'année ou l'équipe ne sont pas renseignés, on utilise le % afin de ne pas filtrer les résultats sur ces
        //critères
        if(empty($annee)) {
            $annee = "%";
        } else {
            $annee = "$annee%";
        }
        if(empty($equipe)) {
            $equipe = "%";
        }
        //On utilise MIN car il est obligatoire d'utiliser une fonction d'aggrégation avec le group by...
        //Rajout d'espaces devant les états pour définir une priorité lorsque 2 échantillons sont à des états différents
        return $this->query('SELECT DISTINCT  c.idCommande, numCommande, commentairePlateau, u.nomUtilisateur, u.prenomUtilisateur,
                             DATE_FORMAT(dateRetourCommande, "%d-%m-%Y") dateRetourCommande,
                             DATE_FORMAT(dateCommande, "%d-%m-%Y") dateCommande,
                             MIN(CASE
                                WHEN dateReceptionCommande IS NULL THEN "En attente de réception"
                                WHEN dateRetourCommande IS NOT NULL THEN "Retournée"
                                WHEN e.idInclusion IS NOT NULL AND e.dateInclusion IS NULL THEN "Inclusion"
                                WHEN e.epaisseurCoupes IS NOT NULL AND e.dateCoupe IS NULL THEN "Coupe"
                                WHEN l.idColoration IS NOT NULL AND l.dateColoration IS NULL THEN "Coloration"
                                ELSE "Terminée"
                             END) AS etat
                             FROM Commande c
                             LEFT JOIN Echantillon e ON c.idCommande = e.idCommande
                             LEFT JOIN Lame l ON e.idEchantillon = l.idEchantillon
                             INNER JOIN Utilisateur u ON u.idUtilisateur = c.idUtilisateur
                             INNER JOIN Equipe eq ON u.idEquipe = eq.idEquipe
                             INNER JOIN Centre ce ON ce.idCentre = eq.idCentre
                             WHERE numCommande LIKE ? AND CONCAT(ce.nomCentre, "-", eq.nomEquipe) LIKE ?
                             AND (CONCAT(u.nomUtilisateur, " ", u.prenomUtilisateur) LIKE ?
                             OR CONCAT(u.prenomUtilisateur, " ", u.nomUtilisateur) LIKE ?)
                             AND c.dateCommande LIKE ? AND e.identAnimalEchantillon LIKE ?
                             AND LEFT(c.numCommande, 1) = ?
                             GROUP BY  c.idCommande, numCommande, commentairePlateau, dateRetourCommande, dateCommande, nomUtilisateur, prenomUtilisateur
                             ORDER BY c.idCommande DESC', $commande, $equipe,
                             $utilisateur, $utilisateur, $annee, $echantillon, $type);
    }

    public function getCommandesAFacturer($type)
    {
        return $this->query('SELECT c.idCommande, c.numCommande, u.nomUtilisateur, u.prenomUtilisateur, e.nomEquipe,
                             ce.nomCentre, COALESCE(lamesBlanches, 0) lamesBlanches, COALESCE(lamesColores, 0) lamesColores,
                             (
                                 SELECT COUNT(idInclusion)
                                 FROM Echantillon e
                                 WHERE e.idCommande = c.idCommande
                             ) nbIncl,
                             (
                                 SELECT COUNT(idColoration)
                                 FROM Lame l
                                 INNER JOIN Echantillon e ON l.idEchantillon = e.idEchantillon
                                 WHERE c.idCommande = e.idCommande AND e.epaisseurCoupes IS NULL
                             ) nbColo
                             FROM Commande c
                             LEFT JOIN (
                                 SELECT idCommande, COUNT(l.idLame) - COUNT(idColoration) lamesBlanches, COUNT(idColoration) lamesColores
                                 FROM Echantillon e
                                 INNER JOIN Lame l ON l.idEchantillon = e.idEchantillon
                                 WHERE epaisseurCoupes IS NOT NULL
                                 GROUP BY idCommande
                             ) T ON T.idCommande = c.idCommande
                             INNER JOIN Utilisateur u ON u.idUtilisateur = c.idUtilisateur
                             INNER JOIN Equipe e ON u.idEquipe = e.idEquipe
                             INNER JOIN Centre ce ON ce.idCentre = e.idCentre
                             WHERE c.dateFacturationCommande IS NULL AND c.dateRetourCommande IS NOT NULL
                             AND LEFT(c.numCommande, 1) = ?', $type);
    }

    public function getCommandesAFacturerSelectionnees($idsCommandes)
    {
        return call_user_func_array([$this, "query"], array_merge(['SELECT c.idCommande, c.numCommande, u.nomUtilisateur, u.prenomUtilisateur, e.nomEquipe,
                             ce.nomCentre, COALESCE(lamesBlanches, 0) lamesBlanches, COALESCE(lamesColores, 0) lamesColores,
                             (
                                 SELECT COUNT(idInclusion)
                                 FROM Echantillon e
                                 WHERE e.idCommande = c.idCommande
                             ) nbIncl,
                             (
                                 SELECT COUNT(idColoration)
                                 FROM Lame l
                                 INNER JOIN Echantillon e ON l.idEchantillon = e.idEchantillon
                                 WHERE c.idCommande = e.idCommande AND e.epaisseurCoupes IS NULL
                             ) nbColo
                             FROM Commande c
                             LEFT JOIN (
                                 SELECT idCommande, COUNT(l.idLame) - COUNT(idColoration) lamesBlanches, COUNT(idColoration) lamesColores
                                 FROM Echantillon e
                                 INNER JOIN Lame l ON l.idEchantillon = e.idEchantillon
                                 WHERE epaisseurCoupes IS NOT NULL
                                 GROUP BY idCommande
                             ) T ON T.idCommande = c.idCommande
                             INNER JOIN Utilisateur u ON u.idUtilisateur = c.idUtilisateur
                             INNER JOIN Equipe e ON u.idEquipe = e.idEquipe
                             INNER JOIN Centre ce ON ce.idCentre = e.idCentre
                             WHERE c.dateFacturationCommande IS NULL AND c.dateRetourCommande IS NOT NULL
                             AND c.idCommande IN (' . implode(',', array_fill(0, count($idsCommandes), '?')) . ')'], $idsCommandes));
    }

    public function setCommandeFacturee($idCommande)
    {
        return $this->execute('UPDATE Commande SET dateFacturationCommande = NOW() WHERE idCommande = ?', $idCommande);
    }

    public function getCommandeById($idCommande)
    {
        $commande = $this->query('SELECT c.*, CONCAT(u.prenomUtilisateur, " ", u.nomUtilisateur) as utilisateur, e.nomEquipe '
            . ' FROM Commande c, Utilisateur u, Equipe e '
            . ' WHERE e.idEquipe = u.idEquipe AND c.idUtilisateur = u.idUtilisateur AND idCommande = ? ', $idCommande);
        if(isset($commande[0]))
            $commande = $commande[0];
        else
            return;

        $commande['echantillons'] = $this->getEchantillonsPourCommande($idCommande);
        return $commande;
    }


    //Echantillon

    //Insère un échantillon ainsi que les lames associées si il y en a
    public function insererEchantillon($idCommande, $numEchantillon, $identAnimal, $nbCoupes, $epaisseurCoupes, $idOrgane, $idAnimal, $idInclusion, $lames)
    {
        $success = $this->execute('INSERT INTO Echantillon (numEchantillon, identAnimalEchantillon, nbCoupes, epaisseurCoupes, idCommande, idOrgane, idAnimal, idInclusion)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)', $numEchantillon, $identAnimal, $nbCoupes, $epaisseurCoupes, $idCommande, $idOrgane, $idAnimal, $idInclusion);
        if($success)
        {
            $idEchantillon = $this->_handler->lastInsertId();
            foreach($lames as $key => $lame)
            {
                $success = $success && $this->insererLame($idEchantillon, $numEchantillon . "-" . ($key + 1), $lame['coloration']);
            }
        }
        return $success;
    }

    /*
    * Retourne un tableau d'échantillons nécessitant une inclusion
    */
    public function getEchantillonsAInclure()
    {
        return $this->query('SELECT e.idCommande cid, e.idEchantillon id, e.numEchantillon "Echantillon N°", CONCAT(u.prenomUtilisateur, \' \', u.nomUtilisateur) "Utilisateur"
                             FROM Utilisateur u, Echantillon e
                             INNER JOIN Commande c ON c.idCommande = e.idCommande
                             WHERE c.dateReceptionCommande IS NOT NULL AND LEFT(c.numCommande, 1) = "P"
                             AND u.idUtilisateur = c.idUtilisateur
                             AND e.dateInclusion IS NULL
                             AND e.idInclusion IS NOT NULL');
    }

    //Retourne le nombre d'échantillons nécessitant une inclusion
    public function getNbEchantillonsAInclure()
    {
        $result = $this->query('SELECT COUNT(*) nbEchantillons FROM Echantillon e
                                INNER JOIN Commande c ON c.idCommande = e.idCommande
                                WHERE c.dateReceptionCommande IS NOT NULL AND LEFT(c.numCommande, 1) = "P"
                                AND e.dateInclusion IS NULL
                                AND e.idInclusion IS NOT NULL');
        if(isset($result[0]['nbEchantillons']))
            return $result[0]['nbEchantillons'];
        return 0;
    }

    //Enregistre l'inclusion d'un échantillon
    public function setEchantillonInclu($idEchantillon, $dateInclusion)
    {
        return $this->execute('UPDATE Echantillon SET dateInclusion = ? WHERE idEchantillon = ?', $dateInclusion, $idEchantillon);
    }

    //Retourne une liste d'échantillons nécessitant une coupe
    public function getEchantillonsACouper($type)
    {
        return $this->query('SELECT e.idCommande cid, e.idEchantillon id, e.numEchantillon "Echantillon N°", CONCAT(u.prenomUtilisateur, \' \', u.nomUtilisateur) "Utilisateur"
                             FROM Utilisateur u, Echantillon e
                             INNER JOIN Commande c ON c.idCommande = e.idCommande
                             WHERE c.dateReceptionCommande IS NOT NULL AND e.dateCoupe IS NULL
                             AND c.idUtilisateur = u.idUtilisateur
                             AND e.epaisseurCoupes IS NOT NULL AND e.nbCoupes IS NOT NULL AND LEFT(c.numCommande, 1) = ?
                             AND (e.dateInclusion IS NOT NULL OR e.idInclusion IS NULL)', $type);
    }

    //Retourne le nombre d'échantillons nécessitant une coupe
    public function getNbEchantillonsACouper($type)
    {
        $result = $this->query('SELECT COUNT(*) nbEchantillons FROM Echantillon e
                                INNER JOIN Commande c ON c.idCommande = e.idCommande
                                WHERE c.dateReceptionCommande IS NOT NULL AND e.dateCoupe IS NULL
                                AND e.epaisseurCoupes IS NOT NULL AND e.nbCoupes IS NOT NULL AND LEFT(c.numCommande, 1) = ?
                                AND (e.dateInclusion IS NOT NULL OR e.idInclusion IS NULL)', $type);
        if(isset($result[0]['nbEchantillons']))
            return $result[0]['nbEchantillons'];
        return 0;
    }

    //Enregistre la coupe d'un échantillon
    public function setEchantillonCoupe($idEchantillon, $dateCoupe)
    {
        return $this->execute('UPDATE Echantillon SET dateCoupe = ? WHERE idEchantillon = ?', $dateCoupe, $idEchantillon);
    }

    //Récupère les échantillons liés à une commande donnée
    public function getEchantillonsPourCommande($idCommande)
    {
        $echantillons = $this->query('SELECT e.idCommande cid, idEchantillon, numEchantillon, identAnimalEchantillon, nomOrgane,
                                      typeAnimal, nomInclusion, epaisseurCoupes, nbCoupes
                                      FROM Echantillon e
                                      INNER JOIN Animal a ON a.idAnimal = e.idAnimal
                                      INNER JOIN Organe o ON o.idOrgane = e.idOrgane
                                      LEFT JOIN Inclusion i ON i.idInclusion = e.idInclusion
                                      WHERE idCommande = ?', $idCommande);
        for($i = 0; $i < count($echantillons); $i++)
        {
            $echantillons[$i]['lames'] = $this->getLamesPourEchantillon($echantillons[$i]['idEchantillon']);
        }
        return $echantillons;
    }

    //Lame

    public function insererLame($idEchantillon, $numLame, $idColoration)
    {
        return $this->execute('INSERT INTO Lame (numLame, idEchantillon, idColoration)
                               VALUES (?, ?, ?)', $numLame, $idEchantillon, $idColoration);
    }

    //Retourne une liste de lames nécessitant une coloration
    public function getLamesAColorer($type)
    {
        /*
        Explications requête :
        Selection des lames dont la commande est réceptionné et est du même type que celui passé en paramère
        dont la coupe est effectuée ou n'est pas à formulaire
        dont l'inclusion est effectuée ou n'est pas à formulaire
        et dont la coloration est à faire mais pas effectuée
        */
        return $this->query('SELECT e.idCommande cid, idLame id, numLame "Lame N°", col.nomColoration Coloration, CONCAT(u.prenomUtilisateur,\' \',u.nomUtilisateur) "Utilisateur"
                             FROM Utilisateur u, Lame l
                             INNER JOIN Echantillon e ON e.idEchantillon = l.idEchantillon
                             INNER JOIN Commande c ON c.idCommande = e.idCommande
                             INNER JOIN Coloration col ON col.idColoration = l.idColoration
                             WHERE c.dateReceptionCommande IS NOT NULL AND LEFT(c.numCommande, 1) = ?
                             AND c.idUtilisateur = u.idUtilisateur
                             AND (e.dateCoupe IS NOT NULL || (e.epaisseurCoupes IS NULL AND e.nbCoupes IS NULL))
                             AND (e.dateInclusion IS NOT NULL OR e.idInclusion IS NULL)
                             AND l.idColoration IS NOT NULL AND l.dateColoration IS NULL', $type);
    }

    //Retourne le nombre de lames nécessitant une coloration
    public function getNbLamesAColorer($type)
    {
        /*
        Explications requête :
        Selection du nombre de lames dont la commande est réceptionné et est du même type que celui passé en paramère
        dont la coupe est effectuée ou n'est pas à formulaire
        dont l'inclusion est effectuée ou n'est pas à formulaire
        et dont la coloration est à faire mais pas effectuée
        */
        $result = $this->query('SELECT COUNT(*) nbLames FROM Lame l
                                INNER JOIN Echantillon e ON e.idEchantillon = l.idEchantillon
                                INNER JOIN Commande c ON c.idCommande = e.idCommande
                                WHERE c.dateReceptionCommande IS NOT NULL AND LEFT(c.numCommande, 1) = ?
                                AND (e.dateCoupe IS NOT NULL || (e.epaisseurCoupes IS NULL AND e.nbCoupes IS NULL))
                                AND (e.dateInclusion IS NOT NULL OR e.idInclusion IS NULL)
                                AND l.idColoration IS NOT NULL AND l.dateColoration IS NULL', $type);
        if(isset($result[0]['nbLames']))
            return $result[0]['nbLames'];
    }

    //Enregistre la coloration d'une lame
    public function setLameColoree($idLame, $dateColoration)
    {
        return $this->execute('UPDATE Lame SET dateColoration = ? WHERE idLame = ?', $dateColoration, $idLame);
    }

    //Récupère toutes les lames liées à un échantillon donné
    public function getLamesPourEchantillon($idEchantillon)
    {
        return $this->query('SELECT idLame, numLame, nomColoration
                             FROM Lame l
                             LEFT JOIN Coloration c ON c.idColoration = l.idColoration
                             WHERE idEchantillon = ?', $idEchantillon);
    }

    //Centre

    public function ajouterCentre($nomCentre)
    {
        return $this->execute('INSERT INTO Centre (nomCentre) VALUES (?)', $nomCentre);
    }

    public function supprimerCentre($idCentre)
    {
        return $this->execute('UPDATE Centre SET visible = 0 WHERE idCentre = ?', $idCentre);
    }

    public function modifierCentre($idCentre, $nomCentre)
    {
        return $this->execute('UPDATE Centre SET nomCentre = ? WHERE idCentre = ?', $nomCentre, $idCentre);
    }

    public function getCentres()
    {
        return $this->query('SELECT idCentre, nomCentre FROM Centre WHERE visible = 1');
    }

    //Récupère l'ID d'un centre à partir de son nom
    public function getIdCentre($nomCentre)
    {
        $result = $this->query('SELECT idCentre FROM Centre WHERE nomCentre = ?', $nomCentre);
        return isset($result[0]['idCentre']) ? $result[0]['idCentre'] : false;
    }


    //Equipe

    public function ajouterEquipe($nomEquipe, $idCentre)
    {
        return $this->execute('INSERT INTO Equipe (nomEquipe, idCentre) VALUES (?, ?)', $nomEquipe, $idCentre);
    }

    public function modifierEquipe($idEquipe, $nomEquipe)
    {
        return $this->execute('UPDATE Equipe SET nomEquipe = ? WHERE idEquipe = ?', $nomEquipe, $idEquipe);
    }

    //Retourne une liste d'équipes accompagnée de leur centre respectif
    public function getEquipes()
    {
        return $this->query('SELECT c.nomCentre, e.nomEquipe, e.idEquipe FROM Equipe e
                             INNER JOIN Centre c ON c.idCentre = e.idCentre');
    }

    public function getIdEquipe($nomEquipe, $idCentre)
    {
        $result = $this->query('SELECT idEquipe FROM Equipe WHERE nomEquipe = ? AND idCentre = ?', $nomEquipe, $idCentre);
        return isset($result[0]['idEquipe']) ? $result[0]['idEquipe'] : false;
    }

    //Organe

    public function ajouterOrgane($nomOrgane, $visible = true)
    {
        if($visible)
        {
            $this->execute('UPDATE Organe SET visible = 1 WHERE nomOrgane = ?', $nomOrgane);
        }
        else {
            $idOrgane = $this->getIdOrgane($nomOrgane);
            if($idOrgane > 0)
            {
                return $idOrgane;
            }
        }
        if($this->_lastNumRows == 0)
        {
            $this->execute('INSERT INTO Organe (nomOrgane, visible) VALUES (?, ?)', $nomOrgane, $visible);
        }
        return $this->getIdOrgane($nomOrgane);
    }

    public function supprimerOrgane($idOrgane)
    {
        return $this->execute('UPDATE Organe SET visible = 0 WHERE idOrgane = ?', $idOrgane);
    }

    public function modifierOrgane($idOrgane, $nomOrgane)
    {
        return $this->execute('UPDATE Organe SET nomOrgane = ? WHERE idOrgane = ?', $nomOrgane, $idOrgane);
    }

    public function getNomOrgane($idOrgane)
    {
        $result = $this->query('SELECT nomOrgane FROM Organe WHERE idOrgane = ?', $idOrgane);
        return isset($result[0]['nomOrgane']) ? $result[0]['nomOrgane'] : null;
    }

    public function getIdOrgane($nomOrgane)
    {
        $result = $this->query('SELECT idOrgane FROM Organe WHERE nomOrgane = ?', $nomOrgane);
        if(isset($result[0]['idOrgane']))
            return $result[0]['idOrgane'];
    }

    public function getOrganes()
    {
        return $this->query('SELECT nomOrgane, idOrgane FROM Organe WHERE visible = 1');
    }

    public function existeOrgane($idOrgane)
    {
        $result = $this->query('SELECT visible FROM Organe WHERE idOrgane = ?', $idOrgane);
        return isset($result[0]['visible']) && $result[0]['visible'] == 1;
    }

    //Animal

    public function ajouterAnimal($typeAnimal, $visible = true)
    {
        if($visible)
        {
            $this->execute('UPDATE Animal SET visible = 1 WHERE typeAnimal = ?', $typeAnimal);
        }
        else {
            $idAnimal = $this->getIdAnimal($typeAnimal);
            if($idAnimal > 0)
            {
                return $idAnimal;
            }
        }
        if($this->_lastNumRows < 1)
        {
            $this->execute('INSERT INTO Animal (typeAnimal, visible) VALUES (?, ?)', $typeAnimal, $visible);
        }
        return $this->getIdAnimal($typeAnimal);
    }

    public function supprimerAnimal($idAnimal)
    {
        return $this->execute('UPDATE Animal SET visible = 0 WHERE idAnimal = ?', $idAnimal);
    }

    public function modifierAnimal($idAnimal, $typeAnimal)
    {
        return $this->execute('UPDATE Animal SET typeAnimal = ? WHERE idAnimal = ?', $typeAnimal, $idAnimal);
    }

    public function getAnimaux()
    {
        return $this->query('SELECT idAnimal, typeAnimal FROM Animal WHERE visible = 1');
    }

    public function existeAnimal($idAnimal)
    {
        $result = $this->query('SELECT visible FROM Animal WHERE idAnimal = ?', $idAnimal);
        return isset($result[0]['visible']) && $result[0]['visible'] == 1;
    }

    public function getNomAnimal($idAnimal)
    {
        $result = $this->query('SELECT typeAnimal FROM Animal WHERE idAnimal = ?', $idAnimal);
        return isset($result[0]['typeAnimal']) ? $result[0]['typeAnimal'] : null;
    }

    public function getIdAnimal($typeAnimal)
    {
        $result = $this->query('SELECT idAnimal FROM Animal WHERE typeAnimal = ?', $typeAnimal);
        if(isset($result[0]['idAnimal']))
            return $result[0]['idAnimal'];
    }

    //Inclusion

    public function ajouterInclusion($nomInclusion, $visible = true)
    {
        if($visible)
        {
            $this->execute('UPDATE Inclusion SET visible = 1 WHERE nomInclusion = ?', $nomInclusion);
        }
        else {
            $idInclusion = $this->getIdInclusion($nomInclusion);
            if($idInclusion > 0)
            {
                return $idInclusion;
            }
        }
        if($this->_lastNumRows == 0)
            $this->execute('INSERT INTO Inclusion (nomInclusion, visible) VALUES (?, ?)', $nomInclusion, $visible);
        return $this->getIdInclusion($nomInclusion);
    }

    public function supprimerInclusion($idInclusion)
    {
        return $this->execute('UPDATE Inclusion SET visible = 0 WHERE idInclusion = ?', $idInclusion);
    }

    public function modifierInclusion($idInclusion, $nomInclusion)
    {
        return $this->execute('UPDATE Inclusion SET nomInclusion = ? WHERE idInclusion = ?', $nomInclusion, $idInclusion);
    }

    public function getInclusions()
    {
        return $this->query('SELECT nomInclusion, idInclusion FROM Inclusion WHERE visible = 1');
    }

    public function existeInclusion($idInclusion)
    {
        $result = $this->query('SELECT visible FROM Inclusion WHERE idInclusion = ?', $idInclusion);
        return isset($result[0]['visible']) && $result[0]['visible'] == 1;
    }

    public function getNomInclusion($idInclusion)
    {
        $result = $this->query('SELECT nomInclusion FROM Inclusion WHERE idInclusion = ?', $idInclusion);
        if(isset($result[0]['nomInclusion']))
            return $result[0]['nomInclusion'];
    }

    public function getIdinclusion($nomInclusion)
    {
        $result = $this->query('SELECT idInclusion FROM Inclusion WHERE nomInclusion = ?', $nomInclusion);
        if(isset($result[0]['idInclusion']))
            return $result[0]['idInclusion'];
    }

    //Coloration

    public function ajouterColoration($nomColoration, $visible = true)
    {
        if($visible)
        {
            $this->execute('UPDATE Coloration SET visible = 1 WHERE nomColoration = ?', $nomColoration);
        }
        else {
            $idColoration = $this->getIdColoration($nomColoration);
            if($idColoration > 0)
            {
                return $idColoration;
            }
        }
        if($this->_lastNumRows == 0)
        {
            $this->execute('INSERT INTO Coloration (nomColoration, visible) VALUES (?, ?)', $nomColoration, $visible);
        }
        return $this->getIdColoration($nomColoration);
    }

    public function supprimerColoration($idColoration)
    {
        return $this->execute('UPDATE Coloration SET visible = 0 WHERE idColoration = ?', $idColoration);
    }

    public function modifierColoration($idColoration, $nomColoration)
    {
        return $this->execute('UPDATE Coloration SET nomColoration = ? WHERE idColoration = ?', $nomColoration, $idColoration);
    }

    public function getColorations()
    {
        return $this->query('SELECT nomColoration, idColoration FROM Coloration WHERE visible = 1');
    }

    public function getNomColoration($idColoration)
    {
        $result = $this->query('SELECT nomColoration FROM Coloration WHERE idColoration = ?', $idColoration);
        if(isset($result[0]['nomColoration']))
            return $result[0]['nomColoration'];
    }

    public function getIdColoration($nomColoration)
    {
        $result = $this->query('SELECT idColoration FROM Coloration WHERE nomColoration = ?', $nomColoration);
        if(isset($result[0]['idColoration']))
            return $result[0]['idColoration'];
    }

    public function existeColoration($idColoration)
    {
        $result = $this->query('SELECT visible FROM Coloration WHERE idColoration = ?', $idColoration);
        return isset($result[0]['visible']) && $result[0]['visible'] == 1;
    }

    // administrateur

    public function insererAdministrateur($nomAdmin, $passAdmin)
    {
        return $this->execute('INSERT INTO Administrateur (nomAdmin, passAdmin) VALUES (?, ?)', $nomAdmin, $passAdmin);
    }

    public function connecterAdministrateur($nomAdmin, $passAdmin)
    {
        $result = $this->query('SELECT passAdmin, idAdmin FROM Administrateur WHERE nomAdmin = ?', $nomAdmin);
        if(!isset($result[0]))
            return false;
        $passBdd = $result[0]['passAdmin'];
        return password_verify($passAdmin, $passBdd) ? $result[0]['idAdmin'] : false;
    }

    public function getAdministrateur($idAdmin)
    {
        $result = $this->query('SELECT nomAdmin FROM Administrateur WHERE idAdmin = ?', $idAdmin);
        if(isset($result[0]))
            return $result[0];
    }

    public function getAdministrateurs()
    {
        return $this->query('SELECT idAdmin, nomAdmin FROM Administrateur');
    }

    public function supprimerAdministrateur($idAdministrateur)
    {
        return $this->execute('DELETE FROM Administrateur WHERE idAdmin = ?', $idAdministrateur);
    }

    public function modifierMdpAdministrateur($idAdministrateur, $passAdmin)
    {
        return $this->execute('UPDATE Administrateur SET passAdmin = ? WHERE idAdmin = ?', $passAdmin, $idAdministrateur);
    }

    public function supprimerCommande($idCommande)
    {
        return $this->execute('DELETE FROM Commande WHERE idCommande = ?', $idCommande);
    }

    public function ajouterLogo($fileName, $folder)
    {
        return $this->execute('INSERT INTO `Fichier`(`nomFichier`, `cheminFichier`, `typeFichier`) VALUES (?, ?, ?)',
            $fileName, $folder, 'LOGO');
    }

    public function getLastLogoPath()
    {
        $logos = $this->query('SELECT * FROM Fichier WHERE typeFichier = ? ORDER BY dateCreation DESC LIMIT 1', 'LOGO');
        if ($logos && count($logos) > 0) {
            return $logos[0]['cheminFichier'] . $logos[0]['nomFichier'];
        }
        return null;
    }

}
