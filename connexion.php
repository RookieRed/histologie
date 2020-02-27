<?php
require "inc/include.php";

if(!empty($_POST['mail']) && !empty($_POST['password']))
{
    $idUtilisateur = $db->getIdUtilisateur($_POST['mail']);
    if($idUtilisateur !== false)
    {
        $_SESSION['idUtilisateur'] = $idUtilisateur;
        $logger->log("Application", "INFO", "Utilisateur connecté : " . $_POST['mail'] . " ( " . $idUtilisateur . " )");
        header("Location: index.php");
        exit;
    }
    elseif(empty($message)) {
        $message = "Adresse mail ou mot de passe incorrect!";
    }
}
else
{
    $message = "Merci de remplir tous les champs";
}
$titre = "Connexion";
require "inc/header.php";
?>
<div class="container">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-primary aide" data-toggle="popover" data-placement="bottom">
            <div class="panel-heading">Connexion</div>
            <div class="panel-body">
                <?php
                //Si une tentative a été effectuée, mais qu'on est toujours sur l'index -> Erreur
                if(!empty($_POST['mail'])) {
                ?>
                <div class="alert alert-danger">
                    <?=$message?>
                </div>
                <?php
                }
                ?>
                <form method="POST" action="connexion.php">
                    <div class="form-group">
                        <label for="mail">Adresse mail</label>
                        <input class="form-control" type="text" name="mail" id="mail" value="<?=(!empty($_POST['mail']) ? $_POST['mail'] : "")?>" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input class="form-control" type="password" name="password" id="password" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-default">Connexion</button>
                        </div>
                        <div class="col-md-6">
                            <a class="pull-right" href="/plateau/connexion.php">Adrministration</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
require "inc/footer.php";
?>
