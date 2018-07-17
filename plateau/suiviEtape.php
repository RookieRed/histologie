<?php
require "../inc/include.php";
if(empty($_GET['type']) || ($_GET['type'] != "P" && $_GET['type'] != "C"))
{
    header("Location: index.php");
    exit;
}
$type = $_GET['type'];
if(empty($_GET['etape']) || $_GET['etape'] < 1 || $_GET['etape'] > 5)
{
    header("Location: suivi.php");
    exit;
}
$etape = $_GET['etape'];
$title = "Suivi " . ($type == "P" ? "Paraffine" : "Cryo");
$titre = $title;
$lignes = call_user_func($descEtapes[$etape]['methodes']['get'], $type);
require "../inc/header.php";
?>
<?php if (count($lignes) >= 10) { ?>
<div class="down-btn-container">
    <a href="#" onclick="scrollToBottom(event)" class="down-btn">&#9662;&#9662;</a>
</div>
<?php } ?>
<div class="container">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><?=$title?></div>
            <div class="panel-body">
                <h3><?=$descEtapes[$etape]["nom"]?></h3>
                <?php if (count($lignes) >= 10) { ?>
                    <a href="suivi.php?type=<?=$type?>" class="btn btn-default">Précédent</a>
                    <button class="btn btn-primary pull-right validerLignes">Valider les lignes sélectionnées</button>
                <?php }
                if(!empty($lignes))
                {
                ?>
                <input type="hidden" id="etape" value="<?=$etape?>">
                <table class="table with-checkboxes">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectionnerLignes"></th>
                            <?php
                            $keys = array_keys($lignes[0]);
                            unset($keys[0]);
                            foreach($keys as $key)
                            {
                            ?>
                            <th><?=$key?></th>
                            <?php
                            }
                            ?>
                            <th>Date</th>
                            <?php
                            if($etape == 5)
                            {
                                ?>
                            <th>Commentaire</th>
                                <?php
                            }
                            ?>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($lignes as $ligne)
                        {
                        ?>
                        <tr data-id="<?=$ligne['id']?>">
                            <td><input id="cb-<?=$ligne['id']?>" type="checkbox"></td>
                            <?php
                            foreach($keys as $key)
                            {
                            ?>
                                <td><label class="all-line" for="cb-<?=$ligne['id']?>">
                                        <?=($key == "Echantillon N°") ? getNumCommandeHtml($ligne[$key]) : $ligne[$key] ?>
                                    </label></td>
                            <?php
                            }
                            ?>
                            <td class="input-td"><input type="text" class="datepicker" value="<?=date("d-m-Y")?>"></td>
                            <?php
                            if($etape == 5)
                            {
                                ?>
                            <td class="input-td"><textarea placeholder="Commentaire" maxlength="2000" class="form-control"></textarea></td>
                                <?php
                            }
                            ?>
                            <td class="input-td"><input type="button" class="btn btn-default btn-sm validerLigne" value="OK"></td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
                }
                ?>
                <a href="suivi.php?type=<?=$type?>" class="btn btn-default">Précédent</a>
                <button class="btn btn-primary pull-right validerLignes">Valider les lignes sélectionnées</button>
            </div>
        </div>
    </div>
</div>
<?php
$scripts = ["suiviEtape.js"];
require "../inc/footer.php";
?>
