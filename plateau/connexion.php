<?php
require "../inc/include.php";
if(!empty($_POST['login']) && !empty($_POST['password']))
{
    //Tente une connexion via la base de donnée
    $idAdministrateur = $db->connecterAdministrateur($_POST['login'], $_POST['password']);
    if($idAdministrateur !== false)
    {
        $_SESSION['idAdministrateur'] = $idAdministrateur;
        $logger->log("Application", "INFO", "Administrateur connecté : " . $_POST['login'] . " ( " . $idAdministrateur . " )");
        header("Location: index.php");
        exit;
    }
    elseif(empty($message)) {
        $message = "Login ou mot de passe incorrect!";
    }
}
else
{
    $message = "Merci de remplir tous les champs";
}
$titre = "Administration";
require "../inc/header.php";
?>
<div class="container">
    <div class="col-md-6 col-md-offset-3 admin">
        <div class="panel panel-primary aide" data-toggle="popover" data-placement="bottom">
            <div class="panel-heading">Connexion panel administration</div>
            <div class="panel-body">
                <?php
                //Si une tentative a été effectuée, mais qu'on est toujours sur l'index -> Erreur
                if(!empty($_POST['login'])) {
                ?>
                <div class="alert alert-danger">
                    <?=$message?>
                </div>
                <?php
                }
                ?>
                <form method="POST" action="connexion.php">
                    <div class="form-group">
                        <label for="login">Login</label>
                        <input class="form-control" type="text" name="login" id="login" value="<?=(!empty($_POST['login']) ? $_POST['login'] : "")?>" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input class="form-control" type="password" name="password" id="password" required>
                    </div>
                    <button type="submit" class="btn btn-default">Connexion</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
require "../inc/footer.php";
?>
