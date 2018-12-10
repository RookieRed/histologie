<?php

if (!isset($_SESSION['commande'])) {
    throw new Exception('Le fil d\'Ariane ne peut être inclu sur cette page.');
}
$newCommand = $_SESSION['commande'];

$BREADCRUMB = [
    [
        'name' => 'Echantillons',
        'link' => '/echantillons.php'
    ],
    [
        'name' => 'Inclusion',
        'link' => '/inclusion.php'
    ],
    [
        'name' => 'Coupe',
        'link' => '/coupe.php'
    ],
    [
        'name' => 'Coloration',
        'link' => '/coloration.php'
    ],
    [
        'name' => 'Récapitulatif',
        'link' => '/recapitulatif.php'
    ],
    [
        'name' => 'Confirmation',
        'link' => '/enregistrerCommande.php'
    ],
];

if ($newCommand['type'] === 'C') {
    unset($BREADCRUMB[1]);
    $BREADCRUMB = array_values($BREADCRUMB);
}

$actualIndex = (function() use($BREADCRUMB, $newCommand) {
    $index = 0;
    $actualIndex = array_reduce($BREADCRUMB, function($ret, $elem) use(&$index) {
        $index++;
        if ($ret === null && strstr($_SERVER['REQUEST_URI'], $elem['link'])) {
            return $index;
        }
        return $ret;
    });
    return $actualIndex;
})();

$maxIndex = (function() use($actualIndex) {
    return $_SESSION['commande']['maxIndex'] = max($actualIndex, $_SESSION['commande']['maxIndex']);
})();

function isClickable($elem, $j) {
    global $newCommand;
    global $maxIndex;
    global $BREADCRUMB;
    global $actualIndex;
    return $actualIndex >= $j && !isDisabled($elem) && $maxIndex <= count($BREADCRUMB) - 1 && !($elem['name'] === 'Inclusion' && $newCommand['type'] === 'C');
}

function isDisabled($elem) {
    global $BREADCRUMB;
    global $maxIndex;
    switch ($elem['name']) {
        case 'Echantillons':
            return false;
        case 'Inclusion':
        case 'Coupe':
        case 'Coloration':
            return compterOperations(strtolower($elem['name'])) == 0;
        case 'Récapitulatif':
            return $maxIndex <= count($BREADCRUMB) - 2;
        case 'Confirmation':
            return $maxIndex <= count($BREADCRUMB) - 1;
    }
    return true;
}

unset($j);
$j = 0;
?>
<ul id="breadcrumb">
    <?php
    foreach ($BREADCRUMB as $elem) {
        $j++;
        ?>
        <li class="step<?= $j <= $actualIndex ? ' passed' : '' ?><?= isDisabled($elem) ? ' disabled' : ''?> <?= isClickable($elem, $j) ? 'clickable' : '' ?>">
            <a <?= isClickable($elem, $j) ? 'href="'.$elem['link'].'"' : '' ?>>
                <span class="number"><?= $j ?></span>
                <span class="link-label"><?= $elem['name'] ?></span>
            </a>
        </li>
        <?php
    }
    ?>
</ul>
