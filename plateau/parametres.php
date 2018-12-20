<?php
require "../inc/include.php";

$err = false;

// Téléversement du logo
if (isset($_POST['submit-logo']) && !empty($_FILES)) {

    if ($_FILES['import'] == null || $_FILES['import']['error']) {
        $err = true;
        $message = 'Erreur lors du chargement du logo.';
    } else {
        $extension = explode('.', $_FILES['import']['name']);
        $extension = $extension[count($extension) - 1];
        $logoPath = '/img/logos/';
        $fileName = md5(uniqid($_FILES['import']['name'])) . '.' . $extension;
        if (!move_uploaded_file($_FILES['import']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $logoPath . $fileName)) {
            $err = true;
            $message = 'Erreur lors de l\'enregistrement du logo';
        } else {
            if ($db->ajouterLogo($fileName, $logoPath)) {
                header('Location /plateau/parametres.php');
            }
        }
    }
}

require "../inc/header.php";
?>
<div class="container">
    <?php if ($err) { ?>
        <div class="alert alert-error text-center">
            <?= $err ?>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-md-12">
            <a href="index.php" class="btn btn-default">
                Précédent
            </a>
        </div>
    </div>
    <br>
    <br>
    <div class="row centered-content">
        <div class="col-md-8">
            <div class="panel panel-primary">
                <div class="panel-heading">Affichage</div>
                <div class="panel-body text-center">
                    <p>Changer le logo de l'application : </p>
                    <form action="/plateau/parametres.php" method="post" enctype="multipart/form-data">
                        <label class="btn btn-default" ><span class="glyphicon glyphicon-file"></span> Sélectionner une image
                            <input style="display: none !important;" name="import" type="file"  accept="image/png, image/gif, image/jpeg">
                        </label>
                        <input type="submit" name="submit-logo" disabled id="send-logo-btn" class="btn btn-default" value="Enregistrer le logo">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row centered-content">
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
    </div>
    <div class="row centered-content">
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
