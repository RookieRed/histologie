<?php
require "../inc/include.php";
require "../inc/header.php";
?>
<div class="container">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-primary">
            <div class="panel-heading">Bienvenue!</div>
            <div class="panel-body text-center">
                <div class="col-md-4">
                    <a href="suivi.php?type=P" class="btn btn-primary">
                        Suivi paraffine
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="suivi.php?type=C" class="btn btn-primary">
                        Suivi cryo
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="facturation.php" class="btn btn-primary">
                        Facturation
                    </a>
                </div>
                <br /><br />
                <div class="col-md-4">
                    <a href="archives.php?type=P" class="btn btn-default">
                        Archives
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="parametres.php" class="btn btn-default">
                        Paramètres
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="administrateurs.php" class="btn btn-default">
                        Administrateurs
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require "../inc/footer.php";
?>
