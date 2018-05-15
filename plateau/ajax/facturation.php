<?php
require "../../inc/include.php";
$success = false;
$message = "";
if(!isset($administrateur))
{
    $message = "Votre session a expiré, merci de vous reconnecter.";
}
else {
    if(!isset($_POST['idsCommandes']) || !is_array($_POST['idsCommandes']))
    {
        $message = "Données incorrectes.";
    }
    else
    {
        $idsCommandes = $_POST['idsCommandes'];
        $db->getHandler()->beginTransaction();
        $success = true;
        foreach($idsCommandes as $idCommande)
        {
            if(intval($idCommande) <= 0)
            {
                $success = false;
            }
            else {
                $success = $success && $db->setCommandeFacturee($idCommande);
            }
        }
        if($success)
        {
            $db->getHandler()->commit();
            $message = "Facturation validée!";
        }
        else {
            $db->getHandler()->rollBack();
            $message = "Transaction SQL échouée.";
        }
    }
}
echo json_encode(["success" => $success, "message" => $message]);
exit;
