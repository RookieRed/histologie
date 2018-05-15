<?php
require "../inc/include.php";
require "../inc/header.php";
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <a href="index.php" class="btn btn-default">
                Précédent
            </a>
        </div>
    </div>
    <br>
    <br>
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-primary" data-ressource="animal">
                <div class="panel-heading">
                    Animaux
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                Nom
                            </th>
                            <th>
                                Modifier
                            </th>
                            <th>
                                Supprimer
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $animaux = $db->getAnimaux();
                        foreach($animaux as $animal)
                        {
                        ?>
                        <tr data-id="<?=$animal['idAnimal']?>">
                            <td>
                                <?=$animal['typeAnimal']?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary btn-xs modifier">Modifier</button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-xs supprimer">Supprimer</button>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <td colspan="2">
                                <input type="text" class="form-control">
                            </td>
                            <td>
                                <button type="button" class="btn btn-success ajouter">Ajouter</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-primary" data-ressource="inclusion">
                <div class="panel-heading">
                    Inclusions
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                Nom
                            </th>
                            <th>
                                Modifier
                            </th>
                            <th>
                                Supprimer
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $inclusions = $db->getInclusions();
                        foreach($inclusions as $inclusion)
                        {
                        ?>
                        <tr data-id="<?=$inclusion['idInclusion']?>">
                            <td>
                                <?=$inclusion['nomInclusion']?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary btn-xs modifier">Modifier</button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-xs supprimer">Supprimer</button>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <td colspan="2">
                                <input type="text" class="form-control">
                            </td>
                            <td>
                                <button type="button" class="btn btn-success ajouter">Ajouter</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-primary" data-ressource="organe">
                <div class="panel-heading">
                    Organes
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                Nom
                            </th>
                            <th>
                                Modifier
                            </th>
                            <th>
                                Supprimer
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $organes = $db->getOrganes();
                        foreach($organes as $organe)
                        {
                        ?>
                        <tr data-id="<?=$organe['idOrgane']?>">
                            <td>
                                <?=$organe['nomOrgane']?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary btn-xs modifier">Modifier</button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-xs supprimer">Supprimer</button>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <td colspan="2">
                                <input type="text" class="form-control" >
                            </td>
                            <td>
                                <button type="button" class="btn btn-success ajouter">Ajouter</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-primary" data-ressource="coloration">
                <div class="panel-heading">
                    Coloration
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                Nom
                            </th>
                            <th>
                                Modifier
                            </th>
                            <th>
                                Supprimer
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $colorations = $db->getColorations();
                        foreach($colorations as $coloration)
                        {
                        ?>
                        <tr data-id="<?=$coloration['idColoration']?>">
                            <td>
                                <?=$coloration['nomColoration']?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary btn-xs modifier">Modifier</button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-xs supprimer">Supprimer</button>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <td colspan="2">
                                <input type="text" class="form-control">
                            </td>
                            <td>
                                <button type="button" class="btn btn-success ajouter">Ajouter</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$scripts = ['parametres.js'];
require "../inc/footer.php";
?>
