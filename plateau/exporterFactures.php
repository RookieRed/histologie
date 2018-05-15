<?php
require "../inc/include.php";
if(!isset($_GET['idsCommandes']) || !is_array($_GET['idsCommandes']) || !isset($administrateur))
{
    exit;
}
$factures = $db->getCommandesAFacturerSelectionnees($_GET['idsCommandes']);
//En-têtes du fichier CSV
$headers = array("Numéro de commande", "Utilisateur", "Equipe", "Nombre d'inclusions", "Nombre de lames blanches", "Nombre de lames colorées", "Nombre de colorations");
//Ouvertur du flux sortant de PHP
$fp = fopen('php://output', 'w');
if ($fp) {
    //Ecriture des en-têtes HTTP afin de préciser le type de document
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="export.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) )); //caracères BOM afin de supporter l'UTF-8
    fputcsv($fp, $headers, ";"); //Ecriture des en-têtes
    foreach($factures as $facture)
    {
        $ligne = [
            $facture["numCommande"],
            $facture['prenomUtilisateur'] . " " . $facture['nomUtilisateur'],
            $facture['nomCentre'] . "-" . $facture['nomEquipe'],
            $facture["nbIncl"],
            $facture["lamesBlanches"],
            $facture["lamesColores"],
            $facture["nbColo"]
        ];
        //Ecriture de chaque ligne au format CSV
        fputcsv($fp, $ligne, ";");
    }
    die;
}
