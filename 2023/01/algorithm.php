<?php

require_once "Puzzle.php";

// first part
$puzzle = new Puzzle('input.txt', '/[0-9]/');
$result = $puzzle->run();

echo $result."\n";


// second part
$puzzle2 = new Puzzle('input.txt', '/[0-9]|one|two|three|four|five|six|seven|eight|nine/');
$result2 = $puzzle2->run();

echo $result2."\n";
