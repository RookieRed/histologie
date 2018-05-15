<?php
require "../inc/include.php";
if(!isset($_GET['type']) || ($_GET['type'] != 'C' && $_GET['type'] != 'P'))
{
    header("Location: index.php");
    exit;
}
$type = $_GET['type'];
require "../inc/header.php";
?>
<div class="container">
    <div class="col-md-12">
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
                <label for="annee" class="col-sm-5">Recherche par année : </label>
                <div class="col-md-7">
                    <input type="text" class="form-control" id="annee" name="annee"/>
                </div>
            </div>
            <div class="form-group">
                <label for="commande" class="col-sm-5">Recherche par commande : </label>
                <div class="col-md-7">
                    <input type="text" class="form-control col-md-7" id="commande" name="commande"/>
                </div>
            </div>
            <div class="form-group">
                <label for="equipe" class="col-sm-5">Recherche par équipe : </label>
                <div class="col-md-7">
                    <select class="form-control col-md-7" id="equipe" name="equipe">
                        <option value="">Toutes</option>
                        <?php
                        $equipes = $db->getEquipes();
                        foreach($equipes as $equipe)
                        {
                        ?>
                        <option><?=$equipe['nomCentre'] . "-" . $equipe['nomEquipe']?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="utilisateur" class="col-sm-5">Recherche par utilisateur : </label>
                <div class="col-md-7">
                    <input type="text" class="form-control col-md-7" id="utilisateur" name="utilisateur"/>
                </div>
            </div>
            <div class="form-group">
                <label for="echantillon" class="col-sm-5">Recherche par échantillons : </label>
                <div class="col-md-7">
                    <input type="text" class="form-control col-md-7" id="echantillon" name="echantillon"/>
                </div>
            </div>
            <div class="form-group">
                <label for="bloc" class="col-sm-5">Recherche par bloc : </label>
                <div class="col-md-7">
                    <input type="text" class="form-control col-md-7" id="bloc" name="bloc"/>
                </div>
            </div>
            <a href="index.php" class="btn btn-default">Précédent</a>
            <input type="submit" class="btn btn-primary pull-right" value="Rechercher"/>
        </form>
        <?php
        }
        else {
            $commandes = $db->getArchives($type, $_POST['commande'], $_POST['equipe'], $_POST['annee'], $_POST['utilisateur'],
                $_POST['echantillon'], $_POST['bloc']);
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
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($commandes as $commande)
                {
                ?>
                <tr>
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
require "../inc/footer.php";
