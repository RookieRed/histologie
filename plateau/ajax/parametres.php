<?php
require "../../inc/include.php";
$message = "";
$success = false;
if(!isset($administrateur))
{
    $message = 'Votre session a expiré, merci de vous reconnecter.';
}
else
{
    if(empty($_GET['action']) || empty($_GET['ressource']))
    {
        $message = 'Informations manquantes!';
    }
    else {
        if(!in_array($_GET['action'], ['ajouter', 'supprimer', 'modifier']))
        {
            $message = 'Action non défninie!';
        }
        else {
            if(!in_array($_GET['ressource'], ['organe', 'animal', 'inclusion', 'coloration', 'equipe', 'centre']))
            {
                $message = 'Ressource non défninie!';
            }
            else {
                $method = new ReflectionMethod($db, $_GET['action'] . ucfirst($_GET['ressource']));
                if($method->getNumberOfRequiredParameters() !== count($_POST['data']))
                {
                    $message = "Mauvais paramètres!";
                }
                else {
                    if(!call_user_func_array([$db, $_GET['action'] . ucfirst($_GET['ressource'])], $_POST['data']))
                    {
                        $message = "Erreur SQL";
                    }
                    else {
                        $success = true;
                        $logger->log("Aplication", "INFO", ucfirst($_GET['ressource']) . " " .
                            str_replace("er", "é", $_GET['action']) . " par l'administrateur #" . $_SESSION['idAdministrateur']);
                    }
                }
            }
        }
    }
}
echo json_encode(["success" => $success, "message" => $message]);
?>
