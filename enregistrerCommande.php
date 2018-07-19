<?php
require "inc/include.php";
if(!isset($_SESSION['commande']))
{
    header("Location: index.php");
    exit;
}
if(!isset($_POST['commentaireUtilisateur']))
{
    header("Location: recapitulatif.php");
    exit;
}
$commentaireUtilisateur = htmlspecialchars($_POST['commentaireUtilisateur']);
$idCommande = $db->insererCommande($_SESSION['idUtilisateur'], $_SESSION['commande']['numProvisoire'], $_SESSION['commande']['echantillons'], $commentaireUtilisateur);
if($idCommande === false)
{
    header("Location: recapitulatif.php");
    exit;
}
$logger->log("Application", "INFO", "Commande #" . $idCommande . " créée par l'utilisateur " . $_SESSION['idUtilisateur']);
require "inc/header.php";
?>
<script language="JavaScript">
    function imprimerCommande(event) {
        if (!(navigator.appName == 'Microsoft Internet Explorer' ||  !!(navigator.userAgent.match(/Trident/)
            || navigator.userAgent.match(/rv:11/)) || (typeof $.browser !== "undefined" && $.browser.msie == 1))){
            event.preventDefault();
            var contenu = window.frames['impressionCommande'];
            contenu.focus();
            contenu.print();
        }
    }
</script>
<div class="container">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><?=$_SESSION['commande']['type'] == "P" ? "Paraffine" : "Cryo"?></div>
            <div class="panel-body text-center">
                <h3>Commande enregistrée!</h3>
                Votre commande N°<?=$db->getNumCommande($idCommande)?> a été crée avec succès!<br>
                <a target="_blank" href="ficheCommande.php?idCommande=<?=$idCommande?>&pourImpression=1" class="btn btn-primary" onclick="imprimerCommande(event)">Imprimer</a><br>
                Merci d'apporter la fiche en même temps que les échantillons identifiés au plateau.<br><br>
                <a href="index.php" class="btn btn-default">Retour à l'accueil</a>
            </div>
        </div>
    </div>
</div>
<iframe src="/ficheCommande.php?idCommande=<?=$idCommande?>&pourImpression=1" style="display:none;" name="impressionCommande"></iframe>
<?php
unset($_SESSION['commande']);
require "inc/footer.php";
?>
