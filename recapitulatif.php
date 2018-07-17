<?php
require "inc/include.php";
require "inc/header.php";
?>
<div class="container">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><?=$_SESSION['commande']['type'] == "P" ? "Paraffine" : "Cryo"?> - Commande N° <?=$_SESSION['commande']['numProvisoire']?></div>
            <div class="panel-body">
                <h3>Récapitulatif</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Numéro d'échantillon</th>
                            <th>Animal</th>
                            <th>Identification animal</th>
                            <th>Tissu</th>
                            <th>Inclusion</th>
                            <th>Coupe</th>
                            <th>Colorations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($_SESSION['commande']['echantillons'] as $key => $echantillon)
                        {
                            ?>
                        <tr>
                            <td><?=$_SESSION['commande']['numProvisoire'] . "-" . ($key + 1)?></td>
                            <td><?=$db->getNomAnimal($echantillon['animal'])?></td>
                            <td><?=$echantillon['identAnimal']?></td>
                            <td><?=$db->getNomOrgane($echantillon['organe'])?></td>
                            <td><?=$echantillon['inclusion'] ? $db->getNomInclusion($echantillon['sensInclusion']) : "/"?></td>
                            <td>
                                <?php
                                if($echantillon['coupe'])
                                {
                                    echo count($echantillon['lames']) . " lame" . (count($echantillon['lames']) > 1 ? "s" : "") .
                                        ", " . $echantillon['nbCoupes'] . " coupe" . ($echantillon['nbCoupes'] > 1 ? "s" : "") .
                                        ", " . $echantillon['epaisseurCoupes'] . "µm";
                                }
                                else {
                                    echo "/";
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if($echantillon['coloration'])
                                {
                                    ?>
                                    <ul>
                                    <?php
                                    foreach($echantillon['lames'] as $lame)
                                    {
                                        if($lame['coloration'] != null)
                                        {
                                            echo "<li>" . $db->getNomColoration($lame['coloration']) . "</li>";
                                        }
                                    }
                                    ?>
                                    </ul>
                                    <?php
                                }
                                else {
                                    echo "/";
                                }
                                ?>
                            </td>
                        </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td colspan="4" class="text-center">
                                <a href="echantillons.php" class="btn btn-default">Modifier</a>
                            </td>
                            <td>
                                <a class="btn btn-default <?=compterOperations("inclusion") ? "\"" : "disabled\" tabindex=\"-1\""?>
                                href="inclusion.php">Modifier</a>
                            </td>
                            <td>
                                <a class="btn btn-default <?=compterOperations("coupe") ? "\"" : "disabled\" tabindex=\"-1\""?>
                                href="coupe.php">Modifier</a>
                            </td>
                            <td>
                                <a class="btn btn-default <?=compterOperations("coloration") ? "\"" : "disabled\" tabindex=\"-1\""?>
                                href="coloration.php">Modifier</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <form method="POST" action="enregistrerCommande.php">
                    <div class="form-group">
                        <label for="commentaireUtilisateur">Commentaires :</label>
                        <textarea maxlength="1000" class="form-control" id="commentaireUtilisateur" name="commentaireUtilisateur"></textarea>
                    </div>
                    <input type="submit" class="btn btn-primary pull-right">
                </form>
            </div>
        </div>
    </div>
</div>
<?php
require "inc/footer.php";
?>
