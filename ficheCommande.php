<?php
require "inc/include.php";
if(!isset($_GET['idCommande']))
{
    header("Location: index.php");
    exit;
}
$idCommande = intval($_GET['idCommande']);
$pourImpression = isset($_GET['pourImpression']) && $_GET['pourImpression'];
$commande = $db->getCommandePourUtilisateur($idCommande, $_SESSION['idUtilisateur']);
if(!isset($commande))
{
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Fiche Commande N°<?=$commande['numCommande']?></title>
        <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
        <link rel="stylesheet" href="css/style.css" type="text/css">
    </head>
    <body>
        <h2>Récapitulatif de la commande <?=$commande['numCommande']?></h2>
        <h3>
            <?=$utilisateur['prenomUtilisateur'] . " " . $utilisateur['nomUtilisateur'] . " - " .
            $utilisateur['nomCentre'] . "-" . $utilisateur['nomEquipe']?>
        </h3>
        <table class="table">
            <thead>
                <tr>
                    <th>
                        Numéro d'échantillon
                    </th>
                    <th>
                        Animal
                    </th>
                    <th>
                        Id animal
                    </th>
                    <th>
                        Tissu
                    </th>
                    <th>
                        Inclusion
                    </th>
                    <th>
                        Coupe
                    </th>
                    <th>
                        Coloration
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($commande['echantillons'] as $echantillon)
                {
                ?>
                <tr>
                    <td><?=getNumCommandeHtml($echantillon['numEchantillon'])?></td>
                    <td><?=$echantillon['typeAnimal']?></td>
                    <td><?=$echantillon['identAnimalEchantillon']?></td>
                    <td><?=$echantillon['nomOrgane']?></td>
                    <td><?=$echantillon['nomInclusion'] == null ? "/" : $echantillon['nomInclusion']?></td>
                    <td><?=getRecapCoupe($echantillon)?></td>
                    <td><?=getRecapColoration($echantillon['lames'])?></td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <?php if (!$pourImpression || strlen($commande['commentaireUtilisateur']) > 0) { ?>
        <fieldset>
            <legend>
                Commentaire utilisateur
            </legend>
            <?php if ($pourImpression) { ?>
            <p class="bloc-commentaire"><?=$commande['commentaireUtilisateur']?></p>
            <?php } else { ?>
            <textarea disabled class="form-control"><?=$commande['commentaireUtilisateur']?></textarea>
            <?php } ?>
        </fieldset>
        <?php } ?>
        <br><br>
        <?php if (!$pourImpression) { ?>
        <button onclick="window.print()" class="btn btn-default hidden-print" id="imprimer">Imprimer</button>
        <?php } ?>
    </body>
</html>
