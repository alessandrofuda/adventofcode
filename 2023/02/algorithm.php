<?php
// 12 red
// 13 green
// 14 blue

// Game 1:     3 blue, 4 red;              1 red, 2 green, 6 blue;     2 green
// Game 2:     1 blue, 2 green;            3 green, 4 blue, 1 red;     1 green, 1 blue
// Game 3:     8 green, 6 blue, 20 red;    5 blue, 4 red, 13 green;    5 green, 1 red
// Game 4:     1 green, 3 red, 6 blue;     3 green, 6 red;             3 green, 15 blue, 14 red
// Game 5:     6 red, 1 blue, 3 green;     2 blue, 1 red, 2 green

// Game $n pass if: each RED < 12 && each GREEN < 13 && each BLUE < 14 --> PASS: $n (Game ID)

// TEST solution: 1+2+5 = 8
// NOT passing the test Games 3 and 4

require_once "Cube.php";
$cube = new Cube('input_test.txt');

// part one
echo 'Result 1st part: '. $cube->run()."\n";

//========================

// part two
// Game 1 --> 6*4*2=48  ==>> for each color get max number and then multiply it.
// Game 2 --> 4*3*1=12
// Game 3 --> 13*6*20=1560
// Game 4 --> 3*14*15=630
// Game 5 --> 6*2*3=36
// TOTs ----> 48+12+1560+630+36=2286

echo 'Result 2nd part: '. $cube->run2()."\n";
