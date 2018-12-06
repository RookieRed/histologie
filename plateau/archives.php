<?php
require "../inc/include.php";
if(!isset($_GET['type']) || ($_GET['type'] != 'C' && $_GET['type'] != 'P'))
{
    header("Location: index.php");
    exit;
}
$type = $_GET['type'];

/**
 * Créer un zip des backups CSV de l'année sélectionnée, force le téléchargement.
 * @param $year
 */
function downloadZipBackup($year) {
    $parentDir = realpath('../bdd/backups/');
    $dir = $parentDir . '/' . $year;

    if (is_dir($dir)) {
        $filename = 'archives-' . $year . '.zip';

        if (file_exists($parentDir . '/' . $filename)) {
            unlink($parentDir . '/' . $filename);
        }

        $zip = new ZipArchive();
        $zip->open($parentDir . '/' . $filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($dir) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();

        header('Content-type: application/zip');
        header("Content-Disposition:attachment;filename='$filename'");
        readfile($parentDir . '/' . $filename);
        unlink($parentDir . '/' . $filename);
        exit;
    }
}

if (isset($_POST) && $_POST['backup-action']) {
    $year = $_POST['backup-year'];
    if (preg_match('/\d{4}/', $year)) {
        downloadZipBackup($year);
    }
}

$backupsAvailable = (function () {
    $parentDir = '../bdd/backups';
    return array_values(array_filter(scandir($parentDir), function ($elem) use ($parentDir) {
        return is_dir($parentDir . '/' . $elem)
            && is_readable($parentDir . '/' . $elem)
            && count(scandir($parentDir . '/' . $elem)) > 2
            && preg_match('/\d{4}/', $elem);
    }));
})();

require "../inc/header.php";
?>
<div class="container">
    <div class="col-md-12">
    <?php if (!empty($_POST) && empty($erreur)) { ?>
        <div class="down-btn-container">
            <a href="#" onclick="scrollToBottom(event)" class="down-btn">&#9662;&#9662;</a>
        </div>
        <div class="row" style="margin-bottom: 10px;"><a href="archives.php?type=<?=$type?>" class="btn btn-default">Précédent</a></div>
    <?php } ?>
        <div class="row">
        <ul class="nav nav-tabs">
            <li role="presentation"<?=$type == 'P' ? ' class="active"' : ""?>><a href="archives.php?type=P">Para</a></li>
            <li role="presentation"<?=$type == 'C' ? ' class="active"' : ""?>><a href="archives.php?type=C">Cryo</a></li>
        </ul>
        <?php
        if(empty($_POST) || isset($_POST['backup-action']) || !empty($erreur))
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
                    <input type="text" class="form-control col-md-7" id="commande" name="commande" placeholder="ex: P1048-01-01-1"/>
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
            <input type="submit" name="search" class="btn btn-primary pull-right" value="Rechercher"/>
        </form>
        </div>

        <?php if (!empty($backupsAvailable)) { ?>
        <div class="row" id="encart-dl-backup">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">Télécharger les archives des années précédentes</div>
                    <div class="panel-body text-center">
                        <p class="info">Vous pouvez télécharger ici les commandes archivées qui ne sont plus présentes dans l'application. Sélectionnez l'année correspondante.</p>
                        <form action="" method="post" class="form-horizontal">
                            <div class="form-group">
                                <label for="backup-year" class="col-md-4">Année des archives : </label>
                                <div class="col-md-8">
                                    <select class="form-control col-md-8" name="backup-year" id="backup-year" required="required">
                                        <option value="" selected="selected">Sélectionnez une année</option>
                                        <?php foreach ($backupsAvailable as $year) { ?>
                                            <option value="<?= $year ?>"><?= $year ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <input type="submit" value="Télécharger" name="backup-action" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        }
        elseif (!empty($_POST) && empty($erreur) && isset($_POST['search'])) {
            $commandes = $db->getArchives($type, $_POST['commande'], $_POST['equipe'], $_POST['annee'], $_POST['utilisateur'],
                $_POST['echantillon']) ?? [];
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
</div>

<?php

include '../inc/recap-popup.html';
$scripts = ['archives.js'];

require "../inc/footer.php";
