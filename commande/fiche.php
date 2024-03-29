<?php

if(!isset($idCommande) || !isset($newCommand)) {
    echo 'Nope';
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Fiche Commande N°<?=$newCommand['numCommande']?></title>
        <link rel="stylesheet" href="../css/print.css" type="text/css">
    </head>
    <body>
        <h1>Commande <?=$newCommand['numCommande']?></h1>
        <h2><?=$utilisateur['prenomUtilisateur'] . " " . $utilisateur['nomUtilisateur'] . " - " .
            $utilisateur['nomCentre'] . ", équipe " . $utilisateur['nomEquipe']?></h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Numéro d'échantillon</th>
                    <th>Animal</th>
                    <th>Id animal</th>
                    <th>Tissu</th>
                    <th>Inclusion</th>
                    <th>Coupe</th>
                    <th>Coloration</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($newCommand['echantillons'] as $echantillon)
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
        <p class="date">Edité le <?=date('d/m/Y H:i')?></p>
        <?php if (strlen($newCommand['commentaireUtilisateur']) > 0) { ?>
        <fieldset>
            <legend>
                Commentaire utilisateur
            </legend>
            <p class="bloc-commentaire"><?=$newCommand['commentaireUtilisateur']?></p>
        </fieldset>
        <?php } ?>
    </body>
</html>
