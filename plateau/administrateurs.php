<?php
require "../inc/include.php";

require "../inc/header.php";
?>
<div class="container">
    <div class="col-md-6 col-md-offset-3">
    <a href="index.php" class="btn btn-default">Précédent</a>
        <br />
        <br />
        <div class="panel panel-primary">
            <div class="panel-heading">
                Administrateurs
            </div>
            <table class="table">
                <?php
                $administrateurs = $db->getAdministrateurs();
                foreach($administrateurs as $administrateur)
                {
                ?>
                <tr data-id="<?=$administrateur['idAdmin']?>">
                    <td>
                        <?=$administrateur['nomAdmin']?>
                    </td>
                    <td class="text-right">
                        <button class="btn btn-primary btn-xs modifierMdp">Modifier le mot de passe</button>
                        <button class="btn btn-danger btn-xs supprimerAdmin">Supprimer</button>
                    </td>
                </tr>
                <?php
                }
                ?>
            </table>
        </div>
        <button class="btn btn-success ajouterAdminModal pull-right">Ajouter un administrateur</button>
    </div>
</div>
<div class="modal fade" id="modalAjouterAdmin" tabindex="-1" role="dialog" aria-labelledby="modalAjouterAdminLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalAjouterAdminLabel">Ajouter un administrateur</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="login">Login</label>
                        <input type="text" class="form-control" id="login" placeholder="Login" tabindex="1">
                    </div>
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" class="form-control" id="password" placeholder="Mot de passe" tabindex="2">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" tabindex="4">Annuler</button>
                <button type="button" class="btn btn-primary" tabindex="3" id="ajouterAdmin">Ajouter un administrateur</button>
            </div>
        </div>
    </div>
</div>
<?php
$scripts = ['administrateurs.js'];
require "../inc/footer.php";
