<footer>
    Programmation : Hugo Martinez ft Rookie<span style="color:red;">Red</span> - Chef de projet : Jean-Jérôme Soueix - 2017
</footer>
<script src="<?=$path?>/js/jquery-3.2.0.min.js"></script>
<script src="<?=$path?>/js/bootstrap.min.js"></script>
<script src="<?=$path?>/js/bootstrap-datepicker.min.js"></script>
<script src="<?=$path?>/js/bootstrap-datepicker.fr.min.js"></script>
<script src="<?=$path?>/js/sweetalert.min.js"></script>
<script src="<?=$path?>/js/datatables.min.js"></script>
<script src="<?=$path?>/js/main.js"></script>
<?php
if(!empty($scripts))
{
    foreach($scripts as $script)
    {
        ?>
        <script src="<?=$path?>/js/<?=$script?>"></script>
        <?php
    }
}
?>
</body>
</html>
