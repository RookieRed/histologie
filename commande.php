<?php
require "inc/include.php";
if(!isset($_GET['type']) || ($_GET['type'] != "P" && $_GET['type'] != "C"))
{
    header("Location: index.php");
    exit;
}
require "inc/header.php";
if(!isset($_SESSION['commande']) || !isset($_SESSION['commande']['type']) || $_SESSION['commande']['type'] != $_GET['type'])
{
    $_SESSION['commande'] = [];
    $_SESSION['commande']['type'] = $_GET['type'];
}
$_SESSION['commande']['numProvisoire'] = $_GET['type'] . $utilisateur['nomCentre'] . "-" .
    $utilisateur['nomEquipe'] . "-" . date("y") . "-" . sprintf("%02d", $db->getNbCommandePourAnnee(date("Y"), $_GET['type']) + 1);
?>
<div class="container">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><?=$_SESSION['commande']['type'] == "P" ? "Paraffine" : "Cryo"?> - Commande N° <?=$_SESSION['commande']['numProvisoire']?></div>
            <div class="panel-body">
                <form action="echantillons.php" method="POST" class="form-inline">
                    <div class="form-group">
                        <label for="nbEchantillons">Nombre d'échantillons</label>
                        <input type="text" name="nbEchantillons" id="nbEchantillons" class="form-control"
                        value="<?=getVarSafe($_SESSION['commande']['nbEchantillons'])?>">
                        <input type="submit" class="btn btn-primary" value="OK">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
require "inc/footer.php";
?>
