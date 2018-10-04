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
//Si aucune inclusion n'est nécessaire
if(compterOperations("inclusion") == 0)
{
    header("Location: coupe.php");
    exit;
}
if(isset($_POST['sensInclusion']))
{
    $err = false;
    $message = "";
    $nbInclusions = compterOperations("inclusion");
    if(!is_array($_POST['sensInclusion']) || $nbInclusions != count($_POST['sensInclusion']))
    {
        $err = true;
        $message = "Données incorrectes.<br>";
    }
    if(!$err)
    {
        //Avancement dans le formulaire
        $i = 0;
        //Boucle pour le tableau d'échantillons
        for($j = 0; $j < $_SESSION['commande']['nbEchantillons']; $j++)
        {
            //On passe les échantillons qui ne nécessitent pas d'inclusion
            if($_SESSION['commande']['echantillons'][$j]['inclusion'] != 1)
                continue;
            //On vérifie que l'inclusion sélectionnée existe
            if((!$db->existeInclusion(intval($_POST['sensInclusion'][$i])) && intval($_POST['sensInclusion'][$i]) > -1) ||
                (intval($_POST['sensInclusion'][$i]) == -1 && empty($_POST['inclusionAutre'][$i])))
            {
                $err = true;
                $message .= "Le sens d'inclusion sélectionné n'existe pas pour l'échantillon " . ($j + 1) . ".<br>";
            }
            else {
                if(intval($_POST['sensInclusion'][$i]) > -1)
                {
                    //On ajoute le sens d'inclusion à la commande pour l'échantillon correspondant
                    $_SESSION['commande']['echantillons'][$j]['sensInclusion'] = intval($_POST['sensInclusion'][$i]);
                }
                else {
                    $_SESSION['commande']['echantillons'][$j]['sensInclusion'] = $db->ajouterInclusion(htmlspecialchars($_POST['inclusionAutre'][$i]), false);
                }
            }
            $i++;
        }
    }
    if(!$err)
    {
        //Si il reste des opérations
        if(compterOperations("coupe") > 0)
        {
            //On continue dans le formulaire
            header("Location: coupe.php");
        }
        elseif(compterOperations("coloration") > 0)
        {
            header("Location: coloration.php");
        }
        else {
            //Sinon on ve directement au récapitulatif
            header("Location: recapitulatif.php");
        }
        exit;
    }
}
require "inc/header.php";
?>
<div class="container">
    <?php include 'inc/breadcrumb.php'; ?>
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><?=$_SESSION['commande']['type'] == "P" ? "Paraffine" : "Cryo"?> - Commande N° <?=$_SESSION['commande']['numProvisoire']?></div>
            <div class="panel-body">
                <h3>Inclusion</h3>
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
                <form action="inclusion.php" method="POST">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Numéro d'échantillon</th>
                                <th>Animal</th>
                                <th>Identification</th>
                                <th>Tissu</th>
                                <th>Sens d'inclusion</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($_SESSION['commande']['echantillons'] as $i => $echantillon)
                            {
                                if($echantillon['inclusion'] == 1)
                                {
                            ?>
                            <tr class="echantillon" id="<?=($i+1)?>">
                                <td><?=$_SESSION['commande']['numProvisoire'] . "-" . sprintf("%03d", $i+1)?></td>
                                <td><?=$db->getNomAnimal($echantillon['animal'])?></td>
                                <td><?=$echantillon['identAnimal']?></td>
                                <td><?=$db->getNomOrgane($echantillon['organe'])?></td>
                                <td>
                                    <select name="sensInclusion[]" class="sensInclusion" required>
                                        <option value>-</option>
                                        <?php
                                        $inclusions = $db->getInclusions();
                                        $inclusionTrouve = false;
                                        $idInclusion = getVarSafe($_POST['inclusion'][$i]) ?: getVarSafe($_SESSION['commande']['echantillons'][$i]['sensInclusion']);
                                        foreach($inclusions as $inclusion)
                                        {
                                            $attr = "";
                                            if($idInclusion == $inclusion['idInclusion'])
                                            {
                                                $inclusionTrouve = true;
                                                $attr = "selected";
                                            }
                                        ?>
                                        <option value="<?=$inclusion['idInclusion']?>" <?=$attr?>>
                                            <?=$inclusion['nomInclusion']?>
                                        </option>
                                        <?php
                                        }
                                        $nomInclusionAutre = "";
                                        $inclusionAutre = !$inclusionTrouve && (!empty($_POST['inclusionAutre'][$i]) || isset($_SESSION['commande']['echantillons'][$i]['sensInclusion']));
                                        if($inclusionAutre)
                                        {
                                            $nomInclusionAutre = getVarSafe($_POST['inclusionAutre'][$i]) ?:
                                                $db->getNomInclusion($_SESSION['commande']['echantillons'][$i]['sensInclusion']);
                                        }
                                        ?>
                                        <option value="-1" <?=($inclusionAutre ? "selected" : "")?>>
                                            Autre
                                        </option>
                                    </select>
                                    <br>
                                    <input type="text" class="inclusionAutre <?=!$inclusionAutre ? "hidden" : ""?>" name="inclusionAutre[]" value="<?=isset($_POST['inclusionAutre'][$i])? $_POST['inclusionAutre'][$i] : ''?>">
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
                    <a href="echantillons.php" class="btn btn-default">Précédent</a>
                    <input type="submit" class="btn btn-primary pull-right" value="Suivant">
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$scripts = ["inclusion.js"];
require "inc/footer.php";
?>
