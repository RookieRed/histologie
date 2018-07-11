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
    <ul class="nav nav-tabs">
        <li role="presentation"<?=$type == "P" ? ' class="active"' : ''?>><a href="facturation.php?type=P">Para</a></li>
        <li role="presentation"<?=$type == "C" ? ' class="active"' : ''?>><a href="facturation.php?type=C">Cryo</a></li>
    </ul>
    <table class="table">
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
                <td><input type="checkbox"></td>
                <td><?=$commande['numCommande']?></td>
                <td><?=$commande['prenomUtilisateur'] . " " . $commande['nomUtilisateur']?></td>
                <td><?=$commande['nomCentre'] . "-" . $commande['nomEquipe']?></td>
                <td><?=$commande['nbIncl']?></td>
                <td><?=$commande['lamesBlanches']?></td>
                <td><?=$commande['lamesColores']?></td>
                <td><?=$commande['nbColo']?></td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <div class="col-md-6 text-center">
        <a class="btn btn-default" href="exporterFactures.php?" id="exporterFactures">Extraire au format CSV</a>
    </div>
    <div class="col-md-6 text-center">
        <button class="btn btn-primary" id="validerFacturation">Valider la facturation</button>
    </div>
    <a href="index.php" class="btn btn-default">Précédent</a>
</div>
<?php
$scripts = ["facturation.js"];
require "../inc/footer.php";
?>
