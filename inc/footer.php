<footer>
    &copy; <?=date('Y')?> Hugo Martinez &amp;
    <a href="http://www.rookie.red" style="color: unset;" target="_blank">Rookie<span style="color:red;">Red</span></a>
    - Chef de projet : Jean-Jérôme Soueix
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
