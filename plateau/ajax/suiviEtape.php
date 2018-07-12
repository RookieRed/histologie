<?php
require "../../inc/include.php";
$success = false;
$message = "";
if(!isset($administrateur))
{
    $message = "Votre session a expiré, merci de vous reconnecter.";
}
else {
    if(!isset($_GET['etape']) || !isset($_POST['dates']) || !isset($_POST['lignesIds']))
    {
        $message = "Informations manquantes!";
    }
    else
    {
        if(!is_array($_POST['lignesIds']) || !is_array($_POST['dates']) || count($_POST['lignesIds']) != count($_POST['dates']))
        {
            $message = "Données incorrectes.";
        }
        else {
            $lignesIds = $_POST['lignesIds'];
            $dates = $_POST['dates'];
            $db->getHandler()->beginTransaction();
            $erreurTransaction = false;
            for($i = 0; $i < count($dates); $i++)
            {
                $ligneId = intval($lignesIds[$i]);
                $date = $dates[$i];
                if($ligneId <= 0 || strtotime($date) === false)
                {
                    $erreurTransaction = true;
                    var_dump($lignesIds);
                }
                else {
                    $date = date("Y-m-d", strtotime($date));
                    $args = [$ligneId, $date];
                    if($_GET['etape'] == 5 && !empty($_POST['commentaires'][$i]))
                    {
                        $args[] = htmlspecialchars($_POST['commentaires'][$i]);
                    }
                    //Appel de la fonction permettant de valider une ligne
                    if(!call_user_func_array($descEtapes[$_GET['etape']]['methodes']['set'], $args))
                    {
                        $erreurTransaction = true;
                    }
                }
            }
            if($erreurTransaction)
            {
                $db->getHandler()->rollBack();
                $message = "Une erreur est survenu, merci de vérifier les données puis de réessayer.";
            }
            else {
                $db->getHandler()->commit();
                if($descEtapes[$_GET['etape']]['mail'])
                {
                    foreach($lignesIds as $i => $ligneId)
                    {
                        $utilisateur = $db->getUtilisateurFromCommande($ligneId);
                        $destinataire = [
                            "mail" => $utilisateur['mailUtilisateur'],
                            "name" => $utilisateur['prenomUtilisateur'] . " " . $utilisateur['nomUtilisateur']
                        ];
                        $commentaire = "";
                        if(!empty($_POST['commentaires'][$i]))
                        {
                            //On échappe les potentiels caractères HTML et on ajouter des retours à la ligne HTML pour garder le formattage
                            $commentaire = 'Commentaire plateau : ' . nl2br(htmlspecialchars($_POST['commentaires'][$i]));
                        }
                        sendMail("histologie-i2mc@inserm.fr", $destinataire, $configStable['objet_commande_prete'], "mail_commande_prete",
                            ["{nom}", "{prenom}", "{commentaire}", "{numCommande}"],
                            [$utilisateur['nomUtilisateur'], $utilisateur['prenomUtilisateur'], $commentaire, $db->getNumCommande($ligneId)]);
                    }
                }
                $logger->log("Application", "INFO", $descEtapes[$_GET['etape']]['nom'] . " validée avec succès par l'administrateur #" . $_SESSION['idAdministrateur']);
                $message = "Ligne" . (count($dates) > 1 ? "s" : "") . " validée" . (count($dates) > 1 ? "s" : "") . " avec succès!";
                $success = true;
            }
        }
    }
}
echo json_encode(["success" => $success, "message" => $message]);
?>
