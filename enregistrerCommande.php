<?php
require "inc/include.php";
if(!isset($_SESSION['commande'])) {
    header("Location: index.php");
    exit;
}
$numProvisoire = $_SESSION['commande']['numProvisoire'];
if(!isset($_POST['commentaireUtilisateur'])) {
    header("Location: recapitulatif.php");
    exit;
}
$commentaireUtilisateur = htmlspecialchars($_POST['commentaireUtilisateur']);
$idCommande = $db->insererCommande($_SESSION['idUtilisateur'], $numProvisoire, $_SESSION['commande']['echantillons'], $commentaireUtilisateur);
if($idCommande === false) {
    header("Location: recapitulatif.php");
    exit;
}

// Envoie du mail de confirmation
$lienPDF = "/commande/telecharger.php?idCommande=$idCommande";
if (!isset($utilisateur)) {
    $utilisateur = $db->getUtilisateur($_SESSION['idUtilisateur']);
}
$destinataire = [
    "mail" => $utilisateur['mailUtilisateur'],
    "name" => $utilisateur['prenomUtilisateur'] . " " . $utilisateur['nomUtilisateur']
];
sendMail($config['smtp_username'], $destinataire, $config['objet_commande_creee'],
    "mail_commande_creee",
    ["{utilisateur}", "{numCommande}", "{lienPDF}"],
    [$utilisateur['prenomUtilisateur'], $numProvisoire, 'http://' . $_SERVER['HTTP_HOST'] . $lienPDF]);

// Log
$logger->log("Application", "INFO", "Commande #" . $idCommande . " créée par l'utilisateur " . $_SESSION['idUtilisateur']);

// Affichage de la page
require "inc/header.php";
?>
<div class="container">
    <?php include 'inc/breadcrumb.php'; ?>
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><?=$_SESSION['commande']['type'] == "P" ? "Paraffine" : "Cryo"?></div>
            <div class="panel-body text-center">
                <h3>Commande enregistrée!</h3>
                Votre commande N°<?=$db->getNumCommande($idCommande)?> a été crée avec succès !<br>
                <a target="_blank" href="<?=$lienPDF?>" class="btn btn-primary">Télécharger PDF</a><br>
                Merci d'apporter la fiche en même temps que les échantillons identifiés au plateau.<br><br>
                <a href="index.php" class="btn btn-default">Retour à l'accueil</a>
            </div>
        </div>
    </div>
</div>
<?php
unset($_SESSION['commande']);
require "inc/footer.php";
?>
