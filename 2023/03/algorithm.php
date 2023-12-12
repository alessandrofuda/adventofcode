<?php
// advent of code 2023 day 3
// foreach line get previous, get current, get next.
// in the current line get numbers and for each get positions
// of digits that compose it.
// in $previous_line search symbol in $position-1, $position, $position+1
// IF found it populate an array with the $current_number and break loop cycle.



require_once "Gear.php";
$gear = new Gear('input.txt');
echo 'Result: '.$gear->run()."\n";


// special characters in TEST: *#+$   --> Result: 4361 [OK]
// special characters in PROD: *#+$   /=&%@-  -->
// Result: ???
// 557560 [with regex WRONG!]  ,,
// 558477 (with !is_numeric($character) --> wrong, too high)
// 435463 [WRONG]
// 557705 [OK !!]











