<?php
require "inc/include.php";
if(!isset($_GET['type']) || ($_GET['type'] != 'C' && $_GET['type'] != 'P'))
{
    header("Location: index.php");
    exit;
}
$type = $_GET['type'];
require "inc/header.php";
?>
<div class="container">
    <div class="col-md-12">
        <?php if (!empty($_POST) && empty($erreur)) { ?>
        <div class="row" style="margin-bottom: 10px;"><a href="archives.php?type=<?=$type?>" class="btn btn-default">Précédent</a></div>
        <?php } ?>
        <ul class="nav nav-tabs">
            <li role="presentation"<?=$type == 'P' ? ' class="active"' : ""?>><a href="archives.php?type=P">Para</a></li>
            <li role="presentation"<?=$type == 'C' ? ' class="active"' : ""?>><a href="archives.php?type=C">Cryo</a></li>
        </ul>
        <?php
        if(empty($_POST) || !empty($erreur))
        {
        ?>
        <form class="form-horizontal form-archives" method="post" action="archives.php?type=<?=$type?>">
            <?php
            if(!empty($erreur))
            {
            ?>
            <div class="alert alert-danger">
                <?=$erreur?>
            </div>
            <?php
            }
            ?>
            <div class="form-group">
                <label for="annee" class="col-sm-5">Recherche par année (et mois, optionnel) : </label>
                <div class="col-md-7">
                    <input type="text" class="form-control" id="annee" name="annee" placeholder="YYYY-MM (ex: 2018 OU 2018-05)"/>
                </div>
            </div>
            <div class="form-group">
                <label for="commande" class="col-sm-5">Recherche par commande : </label>
                <div class="col-md-7">
                    <input type="text" class="form-control col-md-7" id="commande" name="commande" placeholder="ex : P1048-01-01-1"/>
                </div>
            </div>
            <div class="form-group">
                <label for="equipe" class="col-sm-5">Recherche par équipe : </label>
                <div class="col-md-7">
                    <select class="form-control col-md-7" id="equipe" name="equipe" disabled>
                        <option><?=$utilisateur['nomCentre'] . "-" . $utilisateur['nomEquipe']?></option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="utilisateur" class="col-sm-5">Recherche par utilisateur : </label>
                <div class="col-md-7">
                    <input type="text" class="form-control col-md-7" id="utilisateur" name="utilisateur" placeholder="Utilisateur"/>
                </div>
            </div>
            <div class="form-group">
                <label for="echantillon" class="col-sm-5">Recherche par identification animale : </label>
                <div class="col-md-7">
                    <input type="text" class="form-control col-md-7" id="echantillon" name="echantillon" placeholder="Identifiant animal"/>
                </div>
            </div>
            <a href="index.php" class="btn btn-default">Précédent</a>
            <input type="submit" class="btn btn-primary pull-right" value="Rechercher"/>
        </form>
        <?php
        }
        else {
            $commandes = $db->getArchives($type, $_POST['commande'], $utilisateur['nomCentre'] . "-" . $utilisateur['nomEquipe'], $_POST['annee'], $_POST['utilisateur'],
                $_POST['echantillon']);
            ?>
        <table class="table table-archives">
            <thead>
                <tr>
                    <th>
                        Date de la commande
                    </th>
                    <th>
                        Prénom
                    </th>
                    <th>
                        Nom
                    </th>
                    <th>
                        Numéro de commande
                    </th>
                    <th>
                        Etat
                    </th>
                    <th>
                        Commentaire
                    </th>
                    <th>
                        Date de retour
                    </th>
                    <th>
                        Récapitulatif
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($commandes as $commande)
                {
                ?>
                <tr data-id="<?= $commande['idCommande'] ?>">
                    <td>
                        <?=$commande['dateCommande']?>
                    </td>
                    <td>
                        <?=$commande['prenomUtilisateur']?>
                    </td>
                    <td>
                        <?=$commande['nomUtilisateur']?>
                    </td>
                    <td>
                        <?=$commande['numCommande']?>
                    </td>
                    <td>
                        <?=$commande['etat']?>
                    </td>
                    <td>
                        <?=$commande['commentairePlateau']?>
                    </td>
                    <td>
                        <?=$commande['dateRetourCommande']?>
                    </td>
                    <td class="btn-cases input-td">
                        <a class="btn btn-primary btn-sm" target="_blank" href="/commande/telecharger.php?idCommande=<?= $commande['idCommande'] ?>">PDF</a>
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <a href="archives.php?type=<?=$type?>" class="btn btn-default">Précédent</a>
            <?php
        }
        ?>
    </div>
</div>

<?php


include 'inc/recap-popup.html';
$scripts = ['archives.js'];

require "inc/footer.php";
