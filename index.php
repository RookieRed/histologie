<?php
require "inc/include.php";
require "inc/header.php";
?>
<div class="container">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-primary">
            <div class="panel-heading">Créer une commande</div>
            <div class="panel-body text-center">
                <a class="btn btn-primary pull-left" href="commande.php?type=P">
                    Paraffine
                </a>
                <a href="commande.php?type=C" class="btn btn-primary pull-right">
                    Cryo
                </a>
                <br /><br />
                <a href="archives.php?type=P" class="btn btn-default">
                    Archives
                </a>
            </div>
        </div>
    </div>
</div>
<?php
require "inc/footer.php";
?>
