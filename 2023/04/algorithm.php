<?php
// advent of code 2023 day 3
// foreach line get previous, get current, get next.
// in the current line get numbers and for each get positions
// of digits that compose it.
// in $previous_line search symbol in $position-1, $position, $position+1
// IF found it populate an array with the $current_number and break loop cycle.

require_once "Scratchcards.php";
$scratchcards = new Scratchcards('input_test.txt');

// 1st part
echo 'Result 1: '.$scratchcards->run()."\n";
// TEST Result: 13
// PROD Result:

// 2nd part
// echo 'Result 2: '.$scratchcards->run2()."\n";
// TEST Result:
// PROD Result:

