<?php

$input = file_get_contents('input.txt', true);
$array = array_filter(explode("\n", $input));

$tot = 0;

foreach ($array as $item) {
    preg_match_all('/[0-9]/', $item, $matches);
    $integers = $matches[0];

    if(!empty($integers)) {
        $first_int = $integers[0];
        $last_int = $integers[count($integers)-1];
        $calibration_val = (integer) $first_int.$last_int;
        $tot += (integer) $calibration_val;
    }
}

echo $tot."\n";


// TODO -->> Input 2 elaboration


