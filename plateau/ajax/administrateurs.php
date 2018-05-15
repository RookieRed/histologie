<?php
require "../../inc/include.php";
$success = false;
$message = "";
if(!isset($administrateur))
{
    $message = "Votre session a expiré, merci de vous reconnecter";
}
else {
    if(!isset($_GET['action']) || !in_array($_GET['action'], ['ajouter', 'supprimer', 'modifier']))
    {
        $message = "Action incorrecte!";
    }
    else {
        $action = $_GET['action'];
        switch($action)
        {
            case "ajouter":
                if(empty($_POST['login']) || empty($_POST['password']))
                {
                    $message = "Informations manquantes!";
                }
                else {
                    $login = htmlspecialchars($_POST['login']);
                    $password = $_POST['password'];
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    if(!$db->insererAdministrateur($login, $password))
                    {
                        $message = "Erreur SQL!";
                    }
                    else {
                        $message = "Administrateur ajouté avec succès!";
                        $success = true;
                    }
                }
            break;
            case "supprimer":
                if(empty($_POST['idAdministrateur']))
                {
                    $message = "Informations manquantes!";
                }
                else {
                    $idAdministrateur = intval($_POST['idAdministrateur']);
                    if($idAdministrateur == $_SESSION['idAdministrateur'])
                    {
                        $message = "Vous ne pouvez pas vous supprimer!";
                    }
                    else
                    {
                        if(!$db->supprimerAdministrateur($idAdministrateur))
                        {
                            $message = "Erreur SQL!";
                        }
                        else {
                            $message = "Administrateur supprimé avec succès!";
                            $success = true;
                        }
                    }
                }
            break;
            case "modifier":
                if(empty($_POST['idAdministrateur']) || empty($_POST['password']))
                {
                    $message = "Informations manquantes!";
                }
                else {
                    $password = $_POST['password'];
                    $idAdministrateur = intval($_POST['idAdministrateur']);
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    if(!$db->modifierMdpAdministrateur($idAdministrateur, $password))
                    {
                        $message = "Erreur SQL";
                    }
                    else {
                        $message = "Mot de passe modifié avec succès!";
                        $success = true;
                    }
                }
            break;

        }
    }
}
echo json_encode(["success" => $success, "message" => $message]);
