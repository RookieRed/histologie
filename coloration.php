<?php
require "inc/include.php";
if(!isset($_SESSION['commande']) || !isset($_SESSION['commande']['nbEchantillons']) || empty($_SESSION['commande']['echantillons']))
{
    if(isset($_SESSION['commande']['nbEchantillons']))
    {
        header("Location: echantillons.php");
    }
    elseif(isset($_SESSION['commande']))
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
    if($echantillon['inclusion'] == 1 && !isset($echantillon["sensInclusion"]))
    {
        //Au moins une inclusion n'est pas définie, on redirige l'utilisateur
        header('Location: inclusion.php');
        exit;
    }
}
//On vérifie que les coupes ont bien été remplies
foreach($_SESSION['commande']['echantillons'] as $key => $echantillon)
{
    if($echantillon['coupe'] == 1 && empty($echantillon["lames"]))
    {
        //Au moins une coupe n'est pas définie, on redirige l'utilisateur
        header('Location: coupe.php');
        exit;
    }
    if($echantillon['coloration'] == 1 && empty($echantillon["lames"]))
    {
        $_SESSION['commande']['echantillons'][$key]['lames'] = [["coloration" => null]];
    }
}
//Si on a pas de coloration, alors la commande est terminée
if(compterOperations("coloration") == 0)
{
    header("Location: recapitulatif.php");
    exit;
}
if(isset($_POST['coloration']))
{
    $err = false;
    $message = "";
    $nbColorations = 0;
    //On compte le nombre de lames qui peuvent nécessiter une coloration au maximuim
    foreach($_SESSION['commande']['echantillons'] as $echantillon)
    {
        if($echantillon['coloration'] == 1)
        {
            $nbColorations += count($echantillon['lames']);
        }
    }
    //Pour ensuite vérifier que tous les champs sont bien présents
    if(!is_array($_POST['coloration']) || $nbColorations != count($_POST['coloration']))
    {
        $err = true;
        $message .= "Données incorrectes.<br>";
    }
    if(!$err)
    {
        //Avancement dans le formulaire
        $i = 0;
        //Boucle pour le tableau d'échantillons
        for($j = 0; $j < $_SESSION['commande']['nbEchantillons']; $j++)
        {
            //On passe les échantillons qui ne nécessitent pas d'inclusion
            if($_SESSION['commande']['echantillons'][$j]['coloration'] != 1)
                continue;
            for ($k=0; $k < count($_SESSION['commande']['echantillons'][$j]['lames']); $k++) {
                if((!$db->existeColoration(intval($_POST['coloration'][$i])) && intval($_POST['coloration'][$i]) > 0) ||
                    (intval($_POST['coloration'][$i]) == -1 && empty($_POST['colorationAutre'][$i])))
                {
                    $err = true;
                    $message .= "Le sens d'inclusion sélectionné n'existe pas pour l'échantillon " . ($j + 1) . ".<br>";
                }
                else {
                    if(intval($_POST['coloration'][$i]) == -1)
                    {
                        $_SESSION['commande']['echantillons'][$j]['lames'][$k]['coloration'] = $db->ajouterColoration(htmlspecialchars($_POST['colorationAutre'][$i]), false);
                    }
                    elseif(intval($_POST['coloration'][$i]) == 0)
                    {
                        $_SESSION['commande']['echantillons'][$j]['lames'][$k]['coloration'] = null;
                    }
                    else {
                        $_SESSION['commande']['echantillons'][$j]['lames'][$k]['coloration'] = intval($_POST['coloration'][$i]);
                    }
                }
                $i++;
            }
        }
    }
    if(!$err)
    {
        header("Location: recapitulatif.php");
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
                <h3>Coloration</h3>
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
                <form action="coloration.php" method="POST">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Numéro d'échantillon</th>
                                <th>Numéro de lame</th>
                                <th>Animal</th>
                                <th>Identification</th>
                                <th>Tissu</th>
                                <th>Coloration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($_SESSION['commande']['echantillons'] as $i => $echantillon)
                            {
                                if($echantillon['coloration'] == 1)
                                {
                                    foreach($echantillon['lames'] as $j => $lame)
                                    {
                            ?>
                            <tr class="echantillon" id="<?=($i+1)?>">
                                <td><?=$_SESSION['commande']['numProvisoire'] . "-" . sprintf("%03d", $i+1)?></td>
                                <td><?=$_SESSION['commande']['numProvisoire'] . "-" . sprintf("%03d", $i+1) . "-" . sprintf("%03d", $j+1)?></td>
                                <td><?=$db->getNomAnimal($echantillon['animal'])?></td>
                                <td><?=$echantillon['identAnimal']?></td>
                                <td><?=$db->getNomOrgane($echantillon['organe'])?></td>
                                <td>
                                    <select name="coloration[]" class="coloration" required>
                                        <option value="0">Aucune</option>
                                        <?php
                                        $colorations = $db->getColorations();
                                        $coloTrouvee = false;
                                        $idColoration = getVarSafe($_POST['coloration'][$i]) ?: getVarSafe($lame['coloration']);
                                        foreach($colorations as $coloration)
                                        {
                                            $attr = "";
                                            if($idColoration == $coloration['idColoration'])
                                            {
                                                $coloTrouvee = true;
                                                $attr = "selected";
                                            }
                                        ?>
                                        <option value="<?=$coloration['idColoration']?>" <?=$attr?>>
                                            <?=$coloration['nomColoration']?>
                                        </option>
                                        <?php
                                        }
                                        $nomColoAutre = "";
                                        $coloAutre = !$coloTrouvee && (isset($_POST['coloAutre'][$i]) || isset($lame['coloration']));
                                        if($coloAutre)
                                        {
                                            $nomColoAutre = getVarSafe($_POST['colorationAutre'][$i]) ?:
                                                $db->getNomColoration($lame['coloration']);
                                        }
                                        ?>
                                        <option value="-1" <?=($coloAutre ? "selected" : "")?>>
                                            Autre
                                        </option>
                                    </select>
                                    <br>
                                    <input type="text" class="colorationAutre <?=!$coloAutre ? "hidden" : ""?>" name="colorationAutre[]" value="<?=$nomColoAutre?>">
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
                            }
                            ?>
                        </tbody>
                    </table>
                    <a href="coupe.php" class="btn btn-default">Précédent</a>
                    <input type="submit" class="btn btn-primary pull-right" value="Suivant">
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$scripts = ["coloration.js"];
require "inc/footer.php";
?>
