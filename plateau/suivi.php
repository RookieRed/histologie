<?php
require "../inc/include.php";
if(empty($_GET['type']) || ($_GET['type'] != "P" && $_GET['type'] != "C"))
{
    header("Location: index.php");
    exit;
}
$type = $_GET['type'];
$title = "Suivi " . ($type == "P" ? "Paraffine" : "Cryo");
require "../inc/header.php";
?>
<div class="container">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-primary">
            <div class="panel-heading"><?=$title?></div>
            <div class="panel-body">
                <a href="index.php" class="btn btn-default">Accueil</a>
                <table class="suivi">
                    <tr>
                        <td>
                            Commandes à réceptionner : <?=$db->getNbCommandesAReceptionner($type)?>
                        </td>
                        <td>
                            <a href="suiviEtape.php?type=<?=$type?>&etape=1" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-arrow-right"></span>
                            </a>
                        </td>
                    </tr>
                    <?php
                    if($type == "P")
                    {
                    ?>
                    <tr>
                        <td>
                            Echantillons à inclure : <?=$db->getNbEchantillonsAInclure($type)?>
                        </td>
                        <td>
                            <a href="suiviEtape.php?type=<?=$type?>&etape=2" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-arrow-right"></span>
                            </a>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td>
                            Blocs à couper : <?=$db->getNbEchantillonsACouper($type)?>
                        </td>
                        <td>
                            <a href="suiviEtape.php?type=<?=$type?>&etape=3" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-arrow-right"></span>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Lames à colorer : <?=$db->getNbLamesAColorer($type)?>
                        </td>
                        <td>
                            <a href="suiviEtape.php?type=<?=$type?>&etape=4" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-arrow-right"></span>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Commandes à renvoyer : <?=$db->getNbCommandesARenvoyer($type)?>
                        </td>
                        <td>
                            <a href="suiviEtape.php?type=<?=$type?>&etape=5" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-arrow-right"></span>
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
require "../inc/footer.php";
?>
