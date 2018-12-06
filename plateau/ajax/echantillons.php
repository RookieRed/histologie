<?php

require '../../inc/include.php';

$response = new stdClass();
$response->error = true;
$statusCode = 400;

if (!isset($administrateur) && !isset($utilisateur)) {
    $response->errorMessage = 'Déconnecté';
    $statusCode = 401;
}

else {
    // Vérification des données
    $idCommande = $_GET['idCommande'];
    if ($idCommande == null || !is_numeric($idCommande)) {
        $response->errorMessage = 'Paramètre idCommande invalide';
    }

    else {
        $response->commande = $db->getCommandeById($idCommande);

        if ($response->commande != null) {
            // Utilisateur dont la commande ne lui appartient pas
            if (!isset($administrateur) && $utilisateur['idUtilisateur'] != $response->commande['idUtilisateur']) {
                $statusCode = 401;
                $response->commande = null;
                $response->errorMessage = 'Non authorisé';
            }

            else {
                foreach ($response->commande['echantillons'] as &$echantillon) {
                    $echantillon['coupeText'] = getRecapCoupe($echantillon);
                    $echantillon['lamesHTML'] = getRecapColoration($echantillon['lames']);
                }
                $response->error = false;
                $statusCode = 200;
            }
        } else {
            $response->errorMessage = 'Commande non trouvée';
            $statusCode = 404;
        }
    }

}

header('Content-type: application/json; charset=utf-8', true, $statusCode);
echo json_encode($response);
