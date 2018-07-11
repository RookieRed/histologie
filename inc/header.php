<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Affichage du titre paramètré pour chaque page, si il est oublié
        on affiche juste "Horaires Décalés" -->
        <title><?=isset($titre) ? $titre . " | " : ""?>Histologie</title>
        <link rel="stylesheet" href="<?=$path?>/css/bootstrap.min.css" type="text/css">
        <link rel="stylesheet" href="<?=$path?>/css/bootstrap-datepicker3.min.css" type="text/css">
        <link rel="stylesheet" href="<?=$path?>/css/style.css" type="text/css">
        <link rel="stylesheet" href="<?=$path?>/css/sweetalert.css" type="text/css">
        <link rel="stylesheet" href="<?=$path?>/css/datatables.min.css" type="text/css">
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body<?= isset($_SESSION['idAdministrateur']) ? ' class="admin" ' : '' ; ?>>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <!-- Bouton hamburger pour afficher le menu sur mobile -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="http://www.inserm.fr/" target="_blank"><img alt="Inserm" src="/img/logo-inserm.jpg"></a>
                </div>
                <?php
                if(isset($utilisateur))
                {
                ?>
                <div class="hidden-xs text-center">
                    <span class="navbar-text"><?=$utilisateur['prenomUtilisateur'] . " " . $utilisateur['nomUtilisateur'] . ' Equipe ' . $utilisateur['nomEquipe']?></span>
                </div>
                <?php
                }
                ?>
                <div class="collapse navbar-collapse" id="navbar">
                    <ul class="nav navbar-nav navbar-right">
                        <?php if(estConnecte()) { ?>
                        <li>
                            <a href="<?=$path?>/logout.php">Déconnexion</a>
                        </li>
                        <?php } ?>
                        <li>
                            <a href="index.php" class="navbar-brand"><img src="<?=$path?>/img/histo.jpg" alt="Logo histographie"/></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
