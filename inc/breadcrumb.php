<?php

if (!isset($_SESSION['commande'])) {
    throw new Exception('Le fil d\'Ariane ne peut être inclu sur cette page.');
}
$newCommand = $_SESSION['commande'];

const BREADCRUMB = [
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

$actualIndex = (function() use($newCommand) {
    $index = 0;
    $actualIndex = array_reduce(BREADCRUMB, function($ret, $elem) use(&$index) {
        $index++;
        if ($ret === null && strstr($_SERVER['REQUEST_URI'], $elem['link'])) {
            return $index;
        }
        return $ret;
    });
    if ($newCommand['type'] === 'C' && $actualIndex >= 3) {
        return $actualIndex - 1;
    }
    return $actualIndex;
})();

$maxIndex = (function() use($actualIndex) {
    return $_SESSION['commande']['maxIndex'] = max($actualIndex, $_SESSION['commande']['maxIndex']);
})();

function isVisible($elem) {
    global $newCommand;
    return !($elem['name'] === 'Inclusion' && $newCommand['type'] === 'C');
}

function isDisabled($elem) {
    global $newCommand;
    global $maxIndex;
    if ($maxIndex === 5 && $newCommand['type'] === 'C' || $maxIndex === 6) {
        return true;
    }
    switch ($elem['name']) {
        case 'Echantillons':
            return false;
        case 'Inclusion':
        case 'Coupe':
        case 'Coloration':
            return compterOperations(strtolower($elem['name'])) == 0;
        case 'Récapitulatif':
            return !($maxIndex > 4 || ($maxIndex >= 4 && $newCommand['type'] === 'C'));
    }
    return true;
}

unset($j);
$j = 0;
?>
<pre><?php var_dump($maxIndex, $actualIndex, $_SESSION); ?></pre>
<ul id="breadcrumb">
    <?php
    foreach (BREADCRUMB as $elem) {
        if (isVisible($elem)) {
            $j++;
            ?>
            <li class="step<?= $j <= $actualIndex ? ' passed' : '' ?><?= $j > $maxIndex || isDisabled($elem) ? ' disabled' : ''?>">
                <a <?= $j > $maxIndex || isDisabled($elem) ? '' : 'href="'.$elem['link'].'"'?> <?= $j === $actualIndex ? 'class="actual"' : '' ?>>
                    <span class="number"><?= $j ?></span>
                    <span class="link-label"><?= $elem['name'] ?></span>
                </a>
            </li>
            <?php
        }
    }
    ?>
</ul>
