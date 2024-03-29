<?php
session_start();
$path = '';
/**
* Lis un fichier de configuration
*
* @param string $file Chemin du fichier de configuration
*
* @return array Tableau associatif
*/
function loadConfiguration($file, $isEnv = false)
{
    $config = [];
    $content = file_get_contents($file);
    // Découpage du document dans un tableau de lignes
    $lignes = preg_split("/\\r\\n|\\r|\\n/", $content);
    foreach($lignes as $ligne)
    {
        $ligne = trim($ligne);
        //Si la ligne fait plus de 2 caractères de long et ne commence pas par "#" (caractère de commentaire)
        if(strlen($ligne) > 2 && $ligne[0] != "#")
        {
            $posEspace = strpos($ligne, $isEnv ? '=' : ' ');
            $nom = trim(substr($ligne, 0, $posEspace));
            $valeur = trim(substr($ligne, $posEspace+1));
            //Si le paramètre existe déjà, on le stocke sous forme de tableau
            //(Pour gérer de multiples serveurs LDAP)
            if(isset($config[$nom]) && !is_array($config[$nom]))
            {
                //Cas ou le paramètre existe déjà mais pas sous forme de tableau
                $lastValeur = $config[$nom];
                $config[$nom] = array();
                $config[$nom][] = $lastValeur;
                $config[$nom][] = $valeur;
            }
            elseif(isset($config[$nom]))
            {
                //Cas ou le paramètre existe déjà sous forme de tableau
                $config[$nom][] = $valeur;
            }
            else {
                //Le paramètre n'existe pas, on le charge dans le tableau $config
                $config[$nom] = $valeur;
            }
        }
    }
    return $config;
}

$config = loadConfiguration($_SERVER['DOCUMENT_ROOT'] . $path . "/config/config.cfg");
$mysqlConfig = loadConfiguration($_SERVER['DOCUMENT_ROOT'] . $path . "/.env", true);


/**
* Edite un paramètre de la configuration
*
* @param string $file
* @param string $name Nom du paramètre
* @param string $value Nouvelle valeur
*/
function editConfiguration($file, $name, $value)
{
    $lignes = file($file);
    $nouvellesLignes = array_map(function($ligne) use($name, $value) {
        if(strpos($ligne, $name) === 0)
        {
            return $name . " " . $value . "\n";
        }
        return $ligne;
    },$lignes);
    file_put_contents($file, implode('', $nouvellesLignes));
}

/**
 * Envoie un mail d'une personne à une ou plusieurs personnes
 *
 * Le contenu du mail est défini à partir d'un modele, les occurences de $search trouvées dans ce modele seront remplacées par leur équivalent dans $replace
 *
 * @param string|array $from Expéditeur du mail. Si le paramètre est un tableau associatif, il doit être sous la forme :
 * ['name'] = "Nom du destinataire", ['mail'] = "Adresse mail du destinataire"
 * @param string|array $to Destinataire(s) du mail. Si le paramètre est un tableau associatif, il doit être sous la même forme qu'au dessus
 * Ce paramètre peut aussi être un tableau de chaines de caractères, ou un tableau de tableaux associatifs formulé comme au dessus
 * dans le cas ou il y aurait plusieurs destinataires.
 * @param string $subject Objet du mail
 * @param string $modeleMail Modèle du mail, associé à un fichier .txt (et .html) dans le fichier cfg/config.cfg
 * @param array $search Paramètre du mail devant être recherchés. Voir le fichier cfg/config.cfg pour plus d'informations
 * @param array $replace Remplacement des chaines contenues dans $serach
 *
 * @return boolean
 * @throws phpmailerException
 */
function sendMail($from, $to, $subject, $modeleMail, $search, $replace)
{
    global $config;
    global $logger;
    global $pdfPath;

    $logger->log("Mail", "DEBUG", 'Trying to send mail ' . $modeleMail . ' from ' . (is_array($from) ? $from['mail'] : $from) . ' to ' . print_r($to, true) . '.');
    //Récupération du dossier dans lequel trouver les modèles depuis la configuration
    $directory = $_SERVER['DOCUMENT_ROOT'] . '/plateau/mails/';
    if(file_exists($directory . $config[$modeleMail] . ".txt"))
    {
        $bodyText = file_get_contents($directory . $config[$modeleMail] . ".txt");
        //Le mail au format HTML est facultatif, si il n'existe pas on utilise celui au format texte
        if(file_exists($directory . $config[$modeleMail] . ".html"))
        {
            $bodyHTML = file_get_contents($directory . $config[$modeleMail] . ".html");
        }
        else {
            $bodyHTML = $bodyText;
        }
        //Remplacement des paramètres modifiables contenus dans les mails
        $bodyHTML = str_replace($search, $replace, $bodyHTML);
        $bodyText = str_replace($search, $replace, $bodyText);
        $subject = str_replace($search, $replace, $subject);
    }
    else {
        $logger->log("Mail", "ERROR", "Fichier de mail \"" . $directory . $config[$modeleMail] . ".txt\" introuvable!");
    }


    require_once $_SERVER['DOCUMENT_ROOT'] . $pdfPath . '/inc/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->Host = $config['smtp_host'];
    // Authentification SMTP activée uniquement si on dispose d'un nom d'utilisateur et d'un mot de passe
    $mail->SMTPAuth = isset($config['smtp_password']) && isset($config['smtp_username']);
    if(isset($config['smtp_username']))
    {
        $mail->Username = $config['smtp_username'];
    }
    if(isset($config['smtp_password']))
    {
        $mail->Password = $config['smtp_password'];
    }
    if(isset($config['smtp_secure']))
    {
        $mail->SMTPSecure = $config['smtp_secure'];
    }
    $mail->Port = $config['smtp_port'];
    //Ajout de l'expéditeur
    if(is_array($from))
        $mail->setFrom($from['mail'], $from['name']);
    else {
        $mail->setFrom($from);
    }
    //Ajout des destinataires
    if(is_array($to))
    {
        if(isset($to[0]))
        {
            foreach($to as $toAdress)
            {
                if(is_array($toAdress))
                {
                    $mail->addAddress($toAdress['mail'], $toAdress['name']);
                }
                else {
                    $mail->addAddress($toAdress);
                }
            }
        }
        else {
            $mail->addAddress($to['mail'], $to['name']);
        }
    }
    else
    {
        $mail->addAddress($to);
    }

    $mail->isHTML(true);

    $mail->Subject = $subject;
    $mail->Body    = $bodyHTML;
    $mail->AltBody = $bodyText;

    if(!$mail->send()) {
        $logger->log("Mail", "ERROR", 'Le mail n\'a pas pu être envoyé. Erreur PHPMailer : ' . $mail->ErrorInfo);
        return false;
    } else {
        $logger->log("Mail", "DEBUG", 'Mail envoyé.');
        return true;
    }
}

/**
* Tente de se connecter au dictionnaire LDAP à l'aide d'un nom d'utilisateur et d'un mot de passe
*
* @param string $username
* @param string $password
*
* @return array|boolean Retourne un tableau associatif en cas de succès comportant les valeurs mail, nom et prenom. Retourne false en cas d'échec de la connexion
*/
function connectLDAP($username, $password)
{
    global $config;
    //La connexion LDAP se fait à partir de l'identifiant et non de l'adresse mail complète
    if(strpos($username, "@") !== false)
    {
        $username = explode("@", $username)[0];
    }
    $i = 0;
    $erreurServeur = false;
    do {
        if(!is_array($config['ldap_url']) && $erreurServeur == true)
        {
            throw new ErrorException("Le serveur LDAP est inaccessible");
        }
        if(!is_array($config['ldap_url']))
        {
            $ldap = ldap_connect($config['ldap_url'], $config['ldap_port']);
        }
        elseif(isset($config['ldap_url'][$i])) {
            $ldap = ldap_connect($config['ldap_url'][$i], $config['ldap_port']);
        }
        else {
            throw new ErrorException("Le serveur LDAP est inaccessible");
        }
        //Création d'un gestionnaire d'errer afin de pouvoir effectuer un traitement différent en fonction de l'erreur
        set_error_handler(function($errno, $errstr) {
            //Le serveur LDAP est inaccessible
            if(strpos($errstr, "Invalid credentials") === false)
            {
                throw new ErrorException("Le serveur LDAP est inaccessible");
            }
            //Ne pas exécuter le getsionnaire d'erreurs interne
            return true;
        });
        $erreurServeur = false;
        try {
            $bind = ldap_bind($ldap, "uid=" . $username . ",".$config['ldap_dn_users'], $password);
        }
        catch(ErrorException $err)
        {
            $erreurServeur = true;
        }
        $i++;
    } while ($erreurServeur);

    restore_error_handler();
    //Si l'authentification a échoué
    if(!$bind)
        return false;
    //Recherche des champs mail, sn (nom) et givenName (prénom) de l'utilisateur dans le dictionnaire
    $recherche = ldap_search($ldap, $config['ldap_dn_users'], "(uid=" . $username . ")", ["mail", "sn", "givenName", "ou"]);
    //Chargement du premier résultat (il est censé n'y en avoir qu'un seul)
    $entry = ldap_first_entry($ldap, $recherche);
    $mail = ldap_get_values($ldap, $entry, "mail");
    $infos['mail'] = $mail[0];
    $nom = ldap_get_values($ldap, $entry, "sn");
    $infos['nom'] = $nom[0];
    $prenom = ldap_get_values($ldap, $entry, "givenName");
    $infos['prenom'] = $prenom[0];
    $uniteEquipe = ldap_get_values($ldap, $entry, "ou");
    $infos['unite_equipe'] = $uniteEquipe[0];
    //Fermeture de la connexion
    ldap_unbind($ldap);
    return $infos;
}

function compterOperations($operation)
{
    $nbOperations = 0;
    $echantillons = $_SESSION['commande']['echantillons'];
    if (!is_array($echantillons) || $echantillons == null) {
        return 0;
    }
    foreach($echantillons as $echantillon)
    {
        if(isset($echantillon[$operation]) && $echantillon[$operation] == 1)
            $nbOperations++;
    }
    return $nbOperations;
}

/**
* Retourne la valuer d'une variable si elle existe, ou une chaine vide sinon.
*
* Permet de gagner de la place lorsqu'on remplit des champs avec des valeurs qui ne sont pas forcément définies
*/
function getVarSafe(&$var)
{
    return isset($var) ? $var : "";
}

/**
 * Retourne la valeur à afficher dans la colonne Coupe des fiches commandes
 */
function getRecapCoupe($echantillon) {
    $nbLames = count($echantillon['lames']);
    if ($echantillon['epaisseurCoupes'] == null && $echantillon['nbCoupes'] == null && $nbLames == 0) {
        return '/';
    }
    $ret = ($echantillon['epaisseurCoupes'] == null ? '' : $echantillon['epaisseurCoupes'] . 'µm/')
        . ($echantillon['nbCoupes'] == null ? '' : $echantillon['nbCoupes'] . 'C/')
        . ($nbLames == 0 ? '' : $nbLames . 'L/');
    return substr($ret, 0, strlen($ret) - 1);
}

/**
 * Retourne le numéro de commande formaté en HTML pour affichage.
 */
function getNumCommandeHtml($numCommande) {
    if (preg_match('/^([CP]\d+)-(\d+)-(\d+)-(\d+)((?>-\d+)*)$/i', $numCommande,$matches)) {
        return $matches[1] . '-' . $matches[2] . '-' . $matches[3] . '-<b>' . $matches[4] . '</b>'
            . (isset($matches[5]) ? $matches[5] : '');
    }
    return $numCommande;
}

/**
 * Retourne le recap des coloration format HTML
 * @param $lames
 * @return string
 */
function getRecapColoration($lames) {
    if (!is_array($lames) || count($lames) <= 0) {
        return '/';
    }
    $ret = array_reduce($lames, function ($str, $lame) {
        return $str . ' - '. $lame['nomColoration'] . '<br>';
    }, '');
    return substr($ret, 0, strlen($ret) - 4);
}


/* =================================
 *
 * ==>  REQUIREMENTS & INCLUDES  <==
 *
  ================================*/

require $_SERVER['DOCUMENT_ROOT'] . $path . "/vendor/autoload.php";
require $_SERVER['DOCUMENT_ROOT'] . $path . "/inc/logger.class.php";
$logger = new Logger($config['log_level']);
require $_SERVER['DOCUMENT_ROOT'] . $path . "/inc/database.class.php";
try {
    $db = new Database(
        $mysqlConfig['MYSQL_HOSTNAME'],
        $mysqlConfig['MYSQL_USER'],
        $mysqlConfig['MYSQL_PASSWORD'],
        $mysqlConfig['MYSQL_DATABASE'],
        $logger
    );
} catch (PDOException $e) {
    die('Connexion à la base de données impossible<br>'.$e->getMessage());
}

//Description des étapes de suivi des commandes
$descEtapes = [
    1 => [
        "nom" => "Réception",
        "mail" => false,
        "methodes" => [
            "get" => [$db, "getCommandesAReceptionner"],
            "set" => [$db, "setCommandeRecue"]
        ]
    ],
    2 => [
        "nom" => "Inclusion",
        "mail" => false,
        "methodes" => [
            "get" =>[$db, "getEchantillonsAInclure"],
            "set" => [$db, "setEchantillonInclu"]
        ]
    ],
    3 => [
        "nom" => "Coupe",
        "mail" => false,
        "methodes" => [
            "get" => [$db, "getEchantillonsACouper"],
            "set" => [$db, "setEchantillonCoupe"]
        ]
    ],
    4 => [
        "nom" => "Coloration",
        "mail" => false,
        "methodes" => [
            "get" => [$db, "getLamesAColorer"],
            "set" => [$db, "setLameColoree"]
        ]
    ],
    5 => [
        "nom" => "Retour",
        "mail" => true,
        "methodes" => [
            "get" => [$db, "getCommandesARenvoyer"],
            "set" => [$db, "setCommandeRenvoyee"]
        ]
    ]
];

if(isset($_SESSION['idAdministrateur'])) {
    $administrateur = $db->getAdministrateur($_SESSION['idAdministrateur']);
    unset($_SESSION['idUtilisateur'], $utilisateur);
} else if(isset($_SESSION['idUtilisateur'])) {
    $utilisateur = $db->getUtilisateur($_SESSION['idUtilisateur']);
    unset($_SESSION['idAdministrateur'], $administrateur);
}

function estConnecte() {
    global $utilisateur;
    global $administrateur;
    return (strpos($_SERVER['REQUEST_URI'], '/plateau/') === false && isset($_SESSION['idUtilisateur'], $utilisateur))
        || (strpos($_SERVER['REQUEST_URI'], '/plateau/') ===  0 && isset($_SESSION['idAdministrateur'], $administrateur));
}

//   -Si un utilisateur n'est pas connecté
//   -Et qu'on est pas dans la partie plateau (pour éviter de déconnecter un administrateur)
// OU
//   -Si un administrateur n'est pas connecté
//   -Et qu'on est dans la partie plateau
// -Si on est pas sur la page de connexion (pour éviter une redirection infinie)
// - Si on est pas dans la partie commande
if(!estConnecte()
    && strpos($_SERVER['SCRIPT_FILENAME'], "connexion.php") === false
    && strpos($_SERVER['SCRIPT_NAME'], '/commande/telecharger.php') === false
    && strpos($_SERVER['SCRIPT_NAME'], '/plateau/ajax/echantillons.php') === false
) {
//    var_dump($_POST);
//    die;
    header("Location: connexion.php");
    exit;
}
