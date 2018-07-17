<?php
require "../inc/include.php";
if(!isset($_GET['type']) || ($_GET['type'] != "P" && $_GET['type'] != "C"))
{
    $type = "P";
}
else {
    $type = $_GET['type'];
}
$titre = "Facturation";
require "../inc/header.php";
?>
<div class="down-btn-container">
    <a href="#" onclick="scrollToBottom(event)" class="down-btn">&#9662;&#9662;</a>
</div>
<div class="container">
    <div class="row margin-height">
        <div class="col-md-6 text-center">
            <a href="index.php" class="btn btn-default pull-left">Précédent</a>
            <a class="btn btn-default exporterFactures" href="exporterFactures.php?">Extraire au format CSV</a>
        </div>
        <div class="col-md-6 text-center">
            <button class="btn btn-primary validerFacturation">Valider la facturation</button>
        </div>
    </div>
    <ul class="nav nav-tabs">
        <li role="presentation"<?=$type == "P" ? ' class="active"' : ''?>><a href="facturation.php?type=P">Para</a></li>
        <li role="presentation"<?=$type == "C" ? ' class="active"' : ''?>><a href="facturation.php?type=C">Cryo</a></li>
    </ul>
    <table class="table with-checkboxes">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectionnerLignes"></th>
                <th>Commande N°</th>
                <th>Utilisateur</th>
                <th>Equipe</th>
                <th>Nb blocs</th>
                <th>Nb lames blanches</th>
                <th>Nb lames colorées</th>
                <th>Nb colorations</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $commandes = $db->getCommandesAFacturer($type);
            foreach($commandes as $commande)
            {
            ?>
            <tr data-id="<?=$commande['idCommande']?>">
                <td><input type="checkbox" id="cb-<?=$commande['numCommande']?>"></td>
                <td><label for="cb-<?=$commande['numCommande']?>" class="all-line"><?=$commande['numCommande']?></label></td>
                <td><label for="cb-<?=$commande['numCommande']?>" class="all-line"><?=$commande['prenomUtilisateur'] . " " . $commande['nomUtilisateur']?></label></td>
                <td><label for="cb-<?=$commande['numCommande']?>" class="all-line"><?=$commande['nomCentre'] . "-" . $commande['nomEquipe']?></label></td>
                <td><label for="cb-<?=$commande['numCommande']?>" class="all-line"><?=$commande['nbIncl']?></label></td>
                <td><label for="cb-<?=$commande['numCommande']?>" class="all-line"><?=$commande['lamesBlanches']?></label></td>
                <td><label for="cb-<?=$commande['numCommande']?>" class="all-line"><?=$commande['lamesColores']?></label></td>
                <td><label for="cb-<?=$commande['numCommande']?>" class="all-line"><?=$commande['nbColo']?></label></td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <div class="row margin-height">
        <div class="col-md-6 text-center">
            <a href="index.php" class="btn btn-default pull-left">Précédent</a>
            <a class="btn btn-default exporterFactures" href="exporterFactures.php?">Extraire au format CSV</a>
        </div>
        <div class="col-md-6 text-center">
            <button class="btn btn-primary validerFacturation">Valider la facturation</button>
        </div>
    </div>
</div>
<?php
$scripts = ["facturation.js"];
require "../inc/footer.php";
?>
