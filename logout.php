<?php
session_start();
$prefix = isset($_SESSION['idAdministrateur']) ? '/plateau' : '';
session_destroy();
header("Location: $prefix/connexion.php");
exit;
