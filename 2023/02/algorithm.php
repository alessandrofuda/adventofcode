<?php
// 12 red
// 13 green
// 14 blue

// Game 1:     3 blue, 4 red;              1 red, 2 green, 6 blue;     2 green
// Game 2:     1 blue, 2 green;            3 green, 4 blue, 1 red;     1 green, 1 blue
// Game 3:     8 green, 6 blue, 20 red;    5 blue, 4 red, 13 green;    5 green, 1 red
// Game 4:     1 green, 3 red, 6 blue;     3 green, 6 red;             3 green, 15 blue, 14 red
// Game 5:     6 red, 1 blue, 3 green;     2 blue, 1 red, 2 green

// Game 1 pass if: each RED < 12 && each GREEN < 13 && each BLUE < 14 --> PASS: 1 (Game ID)

// Game $n pass if: each RED < 12 && each GREEN < 13 && each BLUE < 14 --> PASS: $n (Game ID)

// TEST solution: 1+2+5 = 8
// NOT passing the test Games 3 and 4

require_once "Cube.php";

$cube = new Cube('input_test.txt');
$result = $cube->run();

echo $result."\n";

// todo ..tbc..

