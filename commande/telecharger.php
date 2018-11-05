<?php

require_once $_SERVER['DOCUMENT_ROOT']. '/inc/include.php';

use Spipu\Html2Pdf\Exception\ExceptionFormatter;
use \Spipu\Html2Pdf\Html2Pdf;
use \Spipu\Html2Pdf\Exception\Html2PdfException;

$idCommande  = $_GET['idCommande'];

if (! isset($idCommande) || $idCommande === null) {
    header("Location: /index.php");
    exit;
}

$numCommande = $db->getNumCommande($idCommande);
$path = $_SERVER['DOCUMENT_ROOT'] . '/commande/pdf/';
$filename = 'Commande-' . $numCommande . '.pdf';

if (!file_exists($path . $filename)) {
    $commande = $db->getCommandeById($idCommande);
    if(!isset($commande)) {
        header("HTTP/1.0 404 Not Found");
        exit;
    }
    $utilisateur = $db->getUtilisateur($commande['idUtilisateur']);
    try {
        ob_start();
        require_once 'fiche.php';
        $content = ob_get_clean();

        $html2pdf = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', [8, 10, 8, 10]);
        $html2pdf->writeHTML($content);
        $html2pdf->output($path . $filename, 'FD');
    } catch (Html2PdfException $e) {
        $html2pdf->clean();
        $formatter = new ExceptionFormatter($e);
        echo $formatter->getHtmlMessage();
        exit;
    }
} else {
    header('Content-type: application/pdf');
    header("Content-Disposition:attachment;filename='$filename'");
    readfile($path . $filename);
}
