<?php
require "inc/include.php";
if(!isset($_SESSION['commande']) || ((!isset($_POST['nbEchantillons']) || intval($_POST['nbEchantillons']) <= 0)
    && !isset($_SESSION['commande']['nbEchantillons'])))
{
    if(isset($_SESSION['commande']) && isset($_SESSION['commande']['type']))
    {
        header("Location: commande.php?type=" . $_SESSION['commande']['type']);
    }
    else
    {
        header("Location: index.php");
    }
    exit;
}

if(isset($_POST['change-nb-enchantillons']) || !isset($_SESSION['commande']['nbEchantillons']))
{
    $_SESSION['commande']['nbEchantillons'] = $_POST['nbEchantillons'];
}

$err = false;
// Vérification import
if (isset($_POST['submit-import']) && !empty($_FILES)) {

    if ($_FILES['import'] == null || $_FILES['import']['error']) {
        $err = true;
        $message = 'Erreur lors du téléversement du ficiher.';
    }
    $inputFileName = $_FILES['import']['tmp_name'];
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
    $sheetData = array_values($spreadsheet->getActiveSheet()->toArray(null, false, true, true));

    if (!isset($_SESSION['commande']['echantillons'])) {
        $_SESSION['commande']['echantillons'] = [];
    }

    for ($i = 1; $i < count($sheetData); $i++) {
        if (!isset($_SESSION['commande']['echantillons'][$i - 2])) {
            $_SESSION['commande']['echantillons'][$i - 1] = [];
        }
        $_SESSION['commande']['echantillons'][$i - 1]['identAnimal'] = $sheetData[$i]['A'];
    }
    $_SESSION['commande']['nbEchantillons'] = max($_SESSION['commande']['nbEchantillons'], count($sheetData) - 1);
}

$nbEchantillons = $_SESSION['commande']['nbEchantillons'];
if(!isset($_POST['change-nb-enchantillons'])  && isset($_POST['suivant']))
{
    //Vérification du formulaire :
    //  -Champs existent
    //  -Nombre de champs == nombre d'échantillons
    //  -Champs renseignés
    $message = "";
    //Variables pour savoir si la prochaine page sera inclusion, coupe ou coloration
    $inclusion = false;
    $coupe = false;
    $coloration = false;
    if(!isset($_POST['animal']) || !is_array($_POST['animal']) || count($_POST['animal']) != $nbEchantillons
        || !isset($_POST['identAnimal']) || !is_array($_POST['identAnimal']) || count($_POST['identAnimal']) != $nbEchantillons
        || !isset($_POST['organe']) || !is_array($_POST['organe']) || count($_POST['organe']) != $nbEchantillons
        || ($_SESSION['commande']['type'] == "P" &&
        (!isset($_POST['inclusion']) || !is_array($_POST['inclusion']) || count($_POST['inclusion']) != $nbEchantillons))
        || !isset($_POST['coupe']) || !is_array($_POST['coupe']) || count($_POST['coupe']) != $nbEchantillons
        || !isset($_POST['coloration']) || !is_array($_POST['coloration']) || count($_POST['coloration']) != $nbEchantillons)
    {
        $err = true;
        $message .= "Données incorrectes<br>";
    }


    if(!$err)
    {
        $_SESSION['commande']['echantillons'] = array_slice($_SESSION['commande']['echantillons'], 0, $nbEchantillons);
        for($i = 0; $i < $nbEchantillons; $i++)
        {
            if((!$db->existeAnimal(intval($_POST['animal'][$i])) && intval($_POST['animal'][$i]) > -1) ||
                (intval($_POST['animal'][$i]) == -1 && empty($_POST['animalAutre'][$i])))
            {
                $err = true;
                $message .= "L'animal sélectionné pour l'échantillon " . ($i + 1) . " n'existe pas.<br>";
            }
            if((!$db->existeOrgane(intval($_POST['organe'][$i])) && intval($_POST['organe'][$i]) > -1) ||
                (intval($_POST['organe'][$i]) == -1 && empty($_POST['organeAutre'][$i])))
            {
                $err = true;
                $message .= "L'organe sélectionné pour l'échantillon " . ($i + 1) . " n'existe pas.<br>";
            }
            if(empty($_POST['identAnimal'][$i]))
            {
                $err = true;
                $message .= "Vous devez préciser une identification de l'animal.<br>";
            }
            if($_SESSION['commande']['type'] == "P" && intval($_POST['inclusion'][$i]) != 0 && intval($_POST['inclusion'][$i]) != 1)
            {
                $err = true;
                $message .= "Valeur incorrecte pour l'inclusion de l'échantillon " . ($i + 1) . "<br>";
            }
            if(intval($_POST['coupe'][$i]) != 0 && intval($_POST['coupe'][$i]) != 1)
            {
                $err = true;
                $message .= "Valeur incorrecte pour la coupe de l'échantillon " . ($i + 1) . "<br>";
            }
            if(intval($_POST['coloration'][$i]) != 0 && intval($_POST['coloration'][$i]) != 1)
            {
                $err = true;
                $message .= "Valeur incorrecte pour la coloration de l'échantillon " . ($i + 1) . "<br>";
            }
            if(($_SESSION['commande']['type'] == "C" || intval($_POST['inclusion'][$i]) == 0) &&
                intval($_POST['coupe'][$i]) == 0 && intval($_POST['coloration'][$i]) == 0)
            {
                $err = true;
                $message .= "Vous devez sélectionner au moins un \"Oui\" pour l'échantillon " . ($i + 1) . "<br>";
            }
            if($_SESSION['commande']['type'] == "P" && intval($_POST['inclusion'][$i]) == 1 &&
                intval($_POST['coupe'][$i]) == 0 && intval($_POST['coloration'][$i]) == 1)
            {
                $err = true;
                $message .= "Vous ne pouvez pas demander une inclusion et une coloration sans coupe pour l'échantillon " . ($i + 1) . "<br>";
            }
            if(intval($_POST['animal'][$i]) == -1)
            {
                $_SESSION['commande']['echantillons'][$i]['animal'] = $db->ajouterAnimal(htmlspecialchars($_POST['animalAutre'][$i]), false);
            }
            else {
                $_SESSION['commande']['echantillons'][$i]['animal'] = intval($_POST['animal'][$i]);
            }
            $_SESSION['commande']['echantillons'][$i]['identAnimal'] = $_POST['identAnimal'][$i];
            if(intval($_POST['organe'][$i]) == -1)
            {
                $_SESSION['commande']['echantillons'][$i]['organe'] = $db->ajouterOrgane(htmlspecialchars($_POST['organeAutre'][$i]), false);
            }
            else {
                $_SESSION['commande']['echantillons'][$i]['organe'] = intval($_POST['organe'][$i]);
            }
            if($_SESSION['commande']['type'] == "P")
            {
                $_SESSION['commande']['echantillons'][$i]['inclusion'] = intval($_POST['inclusion'][$i]);
            }
            else {
                //Si la commande est une commande Cryo, alors elle ne nécessitera jamais d'inclusion
                $_SESSION['commande']['echantillons'][$i]['inclusion'] = 0;
            }
            $_SESSION['commande']['echantillons'][$i]['coupe'] = intval($_POST['coupe'][$i]);
            $_SESSION['commande']['echantillons'][$i]['coloration'] = intval($_POST['coloration'][$i]);
            if(!isset($_SESSION['commande']['echantillons'][$i]['nbCoupes']) &&
                !isset($_SESSION['commande']['echantillons'][$i]['epaisseurCoupes']) &&
                empty($_SESSION['commande']['echantillons'][$i]['lames']))
            {
                $_SESSION['commande']['echantillons'][$i]['nbCoupes'] = null;
                $_SESSION['commande']['echantillons'][$i]['epaisseurCoupes'] = null;
                $_SESSION['commande']['echantillons'][$i]['lames'] = [];
            }
            if($_SESSION['commande']['type'] == "P" && intval($_POST['inclusion'][$i]) == 1)
            {
                $inclusion = true;
            }
            if(intval($_POST['coupe'][$i]) == 1)
            {
                $coupe = true;
            }
            if(intval($_POST['coloration'][$i]) == 1)
            {
                $coloration = true;
            }
        }
    }

    if(!$err)
    {
        if($inclusion)
        {
            header("Location: inclusion.php");
        }
        elseif($coupe)
        {
            header("Location: coupe.php");
        }
        elseif($coloration)
        {
            header("Location: coloration.php");
        }
        exit;
    }
    else {
        $_SESSION['commande']['echantillons'] = [];
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
                <h3>Identification des échantillons</h3>

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
                <form action="echantillons.php" method="post" id="main">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Numéro d'échantillon</th>
                                <th>Animal</th>
                                <th>Identification animale</th>
                                <th>Tissu</th>
                                <?php
                                if($_SESSION['commande']['type'] == "P")
                                {
                                ?>
                                <th>Inclusion</th>
                                <?php
                                }
                                ?>
                                <th>Coupe</th>
                                <th>Coloration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for($i = 0; $i < $_SESSION['commande']['nbEchantillons']; $i++)
                            {
                            ?>
                            <tr class="echantillon" id="<?=($i+1)?>">
                                <td><?=$_SESSION['commande']['numProvisoire'] . "-" . sprintf("%03d", $i+1)?></td>
                                <td>
                                    <select name="animal[]" class="animal">
                                        <option value>-</option>
                                        <?php
                                        $animaux = $db->getAnimaux();
                                        $animalFound = false;
                                        $idAnimal = getVarSafe($_POST['animal'][$i]) ?: getVarSafe($_SESSION['commande']['echantillons'][$i]['animal']);
                                        foreach($animaux as $animal)
                                        {
                                            $attr = "";
                                            if($idAnimal == $animal['idAnimal'])
                                            {
                                                $animalFound = true;
                                                $attr = "selected";
                                            }
                                            ?>
                                            <option value="<?=$animal['idAnimal']?>" <?=$attr?>>
                                                <?=$animal['typeAnimal']?>
                                            </option>
                                            <?php
                                        }
                                        $nomAnimalAutre = "";
                                        $animalAutre = !$animalFound && $_POST['animal'][$i] == -1;
                                        if($animalAutre)
                                        {
                                            $nomAnimalAutre = $_POST['animalAutre'][$i] ?:
                                                $db->getNomAnimal($_SESSION['commande']['echantillons'][$i]['animal']);
                                        }
                                        ?>
                                        <option value="-1" <?=($animalAutre ? "selected" : "")?>>
                                            Autre
                                        </option>
                                    </select>
                                    <br>
                                    <input type="text" class="animalAutre <?=!$animalAutre ? "hidden" : ""?>" name="animalAutre[]" value="<?=$nomAnimalAutre?>">
                                </td>
                                <td>
                                    <input type="text" name="identAnimal[]" class="identAnimal"
                                    value="<?=getVarSafe($_POST['identAnimal'][$i]) ?: getVarSafe($_SESSION['commande']['echantillons'][$i]['identAnimal'])?>">
                                </td>
                                <td>
                                    <select name="organe[]" class="organe">
                                        <option value="-">-</option>
                                        <?php
                                        $organes = $db->getOrganes();
                                        $organeTrouve = false;
                                        $idOrgane = getVarSafe($_POST['organe'][$i]) ?: getVarSafe($_SESSION['commande']['echantillons'][$i]['organe']);
                                        foreach($organes as $organe)
                                        {
                                            $attr = "";
                                            if($idOrgane == $organe['idOrgane'])
                                            {
                                                $organeTrouve = true;
                                                $attr = "selected";
                                            }
                                            ?>
                                            <option value="<?=$organe['idOrgane']?>" <?=$attr?>>
                                                <?=$organe['nomOrgane']?>
                                            </option>
                                            <?php
                                        }
                                        $nomOrganeAutre = "";
                                        $organeAutre = !$organeTrouve && $_POST['organe'][$i] == -1;
                                        if($organeAutre)
                                        {
                                            $nomOrganeAutre = getVarSafe($_POST['organeAutre'][$i]) ?:
                                                $db->getNomOrgane($_SESSION['commande']['echantillons'][$i]['organe']);
                                        }
                                        ?>
                                        <option value="-1" <?=($organeAutre ? "selected" : "")?>>
                                            Autre
                                        </option>
                                    </select>
                                    <br>
                                    <input type="text" class="organeAutre <?=!$organeAutre ? "hidden" : ""?>" name="organeAutre[]" value="<?=$nomOrganeAutre?>">
                                </td>
                                <?php
                                if($_SESSION['commande']['type'] == "P")
                                {
                                ?>
                                <td>
                                    <select name="inclusion[]" class="inclusion">
                                        <option value="1">Oui</option>
                                        <option value="0"
                                        <?=(getVarSafe($_POST['inclusion'][$i]) ?: getVarSafe($_SESSION['commande']['echantillons'][$i]['inclusion'])) == 0 ? "selected" : ""?>>
                                            Non
                                        </option>
                                    </select>
                                </td>
                                <?php
                                }
                                ?>
                                <td>
                                    <select name="coupe[]" class="coupe">
                                        <option value="1">Oui</option>
                                        <option value="0"
                                        <?=(getVarSafe($_POST['coupe'][$i]) ?: getVarSafe($_SESSION['commande']['echantillons'][$i]['coupe'])) == 0 ? "selected" : ""?>>
                                            Non
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <select name="coloration[]" class="coloration">
                                        <option value="1">Oui</option>
                                        <option value="0"
                                        <?=(getVarSafe($_POST['coloration'][$i]) ?: getVarSafe($_SESSION['commande']['echantillons'][$i]['coloration'])) == 0 ? "selected" : ""?>>
                                            Non
                                        </option>
                                    </select>
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
                            ?>
                        </tbody>
                    </table>
                    <div id="encart-nb-echantillon">
                        <div><label for="nb-echantillons">Changer le nombre de ligne :</label></div>
                        <div><input id="nb-echantillons" class="form-control" type="number" value="<?=$nbEchantillons?>" min="0" name="nbEchantillons"/></div>
                        <div><input type="submit" class="btn btn-primary" name="change-nb-enchantillons" value="Changer"></div>
                    </div>

                    <a href="commande.php?type=<?=$_SESSION['commande']['type']?>" class="btn btn-default">Précédent</a>
                    <input type="submit" class="btn btn-primary pull-right" name="suivant" value="Suivant">
                </form>

                <form id="import-xls" action="echantillons.php" method="post" enctype="multipart/form-data">
                    <div id="encart-import-xls">
                        <h4>Importer les identifications animales</h4>
                        <p class="info text-center">Vous pouvez importer les identifiants des animaux à partir d'un fichier Excel. Pour se faire,
                            <a href="/modele_import_identifiants.csv" target="_blank" download>téléchargez le modèle de fichier en cliquant ici</a>,
                            puis remplissez-le avec les identifiants animaux, avant de l'importer avec les boutons ci-dessous.</p>
                        <div class="row text-center">
                            <label class="btn btn-default" ><span class="glyphicon glyphicon-file"></span> Sélectionner un fichier
                                <input style="display: none !important;" name="import" type="file" accept=".csv, .xls, .xlsx">
                            </label>
                            <input type="submit" name="submit-import" disabled id="send-import-btn" class="btn btn-default" value="Envoyer le fichier">
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<?php
$scripts = ["echantillons.js"];
require "inc/footer.php";
?>
