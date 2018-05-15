<?php
require "inc/include.php";
if(!isset($_SESSION['commande']) || !isset($_SESSION['commande']['nbEchantillons']) || empty($_SESSION['commande']['echantillons']))
{
    if(isset($_SESSION['commande']) && isset($_SESSION['commande']['nbEchantillons']))
    {
        header("Location: echantillons.php");
    }
    elseif(isset($_SESSION['commande']) && isset($_SESSION['commande']['type']))
    {
        header("Location: commande.php?type=" . $_SESSION['commande']['type']);
    }
    else
    {
        header("Location: index.php");
    }
    exit;
}
//On vérifie que les inclusions ont bien été remplies
foreach($_SESSION['commande']['echantillons'] as $echantillon)
{
    if($_SESSION['commande']['type'] == "P" && $echantillon['inclusion'] == 1 && !isset($echantillon["sensInclusion"]))
    {
        //Au moins une inclusion n'est pas définie, on redirige l'utilisateur
        header('Location: inclusion.php');
        exit;
    }
}
if(!compterOperations("coupe"))
{
    if(!compterOperations("coloration"))
    {
        //Si il ne reste ni coupe ni coloration, on a terminé
        header("Location: recapitulatif.php");
    }
    else {
        //Il reste des colorations, on passe sur la page coloration
        header("Location: coloration.php");
    }
    exit;
}
if(isset($_POST['nbLames']))
{
    $err = false;
    $message = "";
    $nbCoupes = compterOperations("coupe");

    if(!isset($_POST['epaisseur']) || !is_array($_POST['epaisseur']) || $nbCoupes != count($_POST['epaisseur']) ||
        !isset($_POST['nbLames']) || !is_array($_POST['nbLames']) || $nbCoupes != count($_POST['nbLames']) ||
        !isset($_POST['nbCoupes']) || !is_array($_POST['nbCoupes']) || $nbCoupes != count($_POST['nbCoupes']))
    {
        $err = true;
        $message = "Données incorrectes.<br>";
    }
    if(!$err)
    {
        $i = 0;
        //Boucle pour le tableau d'échantillons
        for($j = 0; $j < $_SESSION['commande']['nbEchantillons']; $j++)
        {
            //On passe les échantillons qui ne nécessitent pas de coupe
            if($_SESSION['commande']['echantillons'][$j]['coupe'] != 1)
            {
                continue;
            }
            $epaisseur = intval($_POST['epaisseur'][$i]);
            $nbLames = intval($_POST['nbLames'][$i]);
            $nbCoupes = intval($_POST['nbCoupes'][$i]);
            if($epaisseur <= 0)
            {
                $err = true;
                $message .= "L'épaisseur de la coupe doit être supérieure à 0 pour l'échantillon " . ($j + 1) . ".<br>";
            }
            if($nbLames <= 0)
            {
                $err = true;
                $message .= "Il doit y avoir au moins 1 lame par bloc pour l'échantillon " . ($j + 1) . ".<br>";
            }
            if($nbCoupes <= 0)
            {
                $err = true;
                $message .= "Il doit y avoir au moins 1 coupe par lame pour l'échantillon " . ($j + 1) . ".<br>";
            }
            if(!$err) {
                $_SESSION['commande']['echantillons'][$j]['nbCoupes'] = $nbCoupes;
                $_SESSION['commande']['echantillons'][$j]['epaisseurCoupes'] = $epaisseur;
                for($k = 0; $k < $nbLames; $k++)
                {
                    if(!isset($_SESSION['commande']['echantillons'][$j]['lames'][$k]['coloration']))
                    {
                        $_SESSION['commande']['echantillons'][$j]['lames'][$k]['coloration'] = null;
                    }
                }
            }
            $i++;
        }
    }
    if(!$err)
    {
        if(compterOperations("coloration") > 0)
        {
            header("Location: coloration.php");
        }
        else {
            header("Location: recapitulatif.php");
        }
        exit;
    }
}
require "inc/header.php";
?>
<div class="container">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><?=$_SESSION['commande']['type'] == "P" ? "Paraffine" : "Cryo"?> - Commande N° <?=$_SESSION['commande']['numProvisoire']?></div>
            <div class="panel-body">
                <h3>Coupe</h3>
                <?php
                if(isset($err) && $err)
                {
                ?>
                <div class="alert alert-danger">
                    <?=$message?>
                </div>
                <?php
                }
                ?>
                <form action="coupe.php" method="POST">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Numéro d'échantillon</th>
                                <th>Animal</th>
                                <th>Identification</th>
                                <th>Tissu</th>
                                <th>Epaisseur de coupe</th>
                                <th>Nombre de lames par bloc</th>
                                <th>Nombre de coupes par lame</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($_SESSION['commande']['echantillons'] as $i => $echantillon)
                            {
                                if($echantillon['coupe'] == 1)
                                {
                            ?>
                            <tr class="echantillon" id="<?=($i+1)?>">
                                <td><?=$_SESSION['commande']['numProvisoire'] . "-" . sprintf("%03d", $i+1)?></td>
                                <td><?=$db->getNomAnimal($echantillon['animal'])?></td>
                                <td><?=$echantillon['identAnimal']?></td>
                                <td><?=$db->getNomOrgane($echantillon['organe'])?></td>
                                <td>
                                    <input type="number" placeholder="en µm" name="epaisseur[]" required min="1"
                                    value="<?=getVarSafe($_POST['epaisseur'][$i]) ?: getVarSafe($_SESSION['commande']['echantillons'][$i]['epaisseurCoupes'])?>"
                                    class="epaisseur">
                                </td>
                                <td>
                                    <?php
                                    $nbLames = getVarSafe($_POST['nbLames'][$i]) ?: count(getVarSafe($_SESSION['commande']['echantillons'][$i]['lames']));
                                    ?>
                                    <input type="number" name="nbLames[]" required min="1" class="nbLames"
                                    value="<?=$nbLames > 0 ? $nbLames : ""?>">
                                </td>
                                <td>
                                    <input type="number" name="nbCoupes[]" required min="1" class="nbCoupes"
                                    value="<?=getVarSafe($_POST['nbCoupes'][$i]) ?: getVarSafe($_SESSION['commande']['echantillons'][$i]['nbCoupes'])?>">
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-xs btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            Actions
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="javascript:void(0);" class="copier">Copier</a></li>
                                            <li><a href="javascript:void(0);" class="coller">Coller</a></li>
                                            <li role="separator" class="divider"></li>
                                            <li><a href="javascript:void(0);" class="appliquerPartout">Appliquer partout</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                    $precedent = "inclusion.php";
                    if(compterOperations("inclusion") == 0)
                    {
                        $precedent = "echantillons.php";
                    }
                    ?>
                    <a href="<?=$precedent?>" class="btn btn-default">Précédent</a>
                    <input type="submit" class="btn btn-primary pull-right" value="Suivant">
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$scripts = ["coupe.js"];
require "inc/footer.php";
?>
