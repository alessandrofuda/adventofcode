<?php

require_once "Puzzle.php";

$puzzle = new Puzzle('input2.txt', '/[0-9]|one|two|three|four|five|six|seven|eight|nine/');
$result = $puzzle->run();

echo $result."\n";


