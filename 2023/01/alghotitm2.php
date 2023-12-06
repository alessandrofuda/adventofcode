<?php

require_once "Puzzle.php";

$puzzle = new Puzzle();

$array = $puzzle->convertInputToArr('input2.txt');

$tot = 0;

foreach ($array as $item) {

    $mixed_integers = $puzzle->getArrayViaRegex('/[0-9]|one|two|three|four|five|six|seven|eight|nine/', $item);
    $integers = $puzzle->remapAllToIntegers($mixed_integers);

    if(!empty($integers)) {
        $tot += $puzzle->calcSubTot($integers);
    }
}

echo $tot."\n";


