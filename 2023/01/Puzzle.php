<?php

class Puzzle
{
    public function convertInputToArr(string $file) : array
    {
        $input = file_get_contents($file, true);
        return array_filter(explode("\n", $input));
    }

    public function remapAllToIntegers(array $mixed_integers) : array
    {
        return array_map(function($item) {
            $value = match($item) {
                'one' => 1,
                'two' => 2,
                'three' => 3,
                'four' => 4,
                'five' => 5,
                'six' => 6,
                'seven' => 7,
                'eight' => 8,
                'nine' => 9,
                default => $item
            };
            return (integer) $value;

        }, $mixed_integers);
    }

    public function getArrayViaRegex(string $pattern, $string) : array
    {
        preg_match_all($pattern, $string, $matches);
        return $matches[0];
    }

    public function calcSubTot(array $integers) : int
    {
        $first_int = $integers[0];
        $last_int = $integers[count($integers)-1];
        return (integer) $first_int.$last_int;
    }
}
