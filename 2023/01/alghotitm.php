<?php

require_once "Puzzle.php";

$puzzle = new Puzzle('input.txt', '/[0-9]/');
$result = $puzzle->run();

echo $result."\n";
