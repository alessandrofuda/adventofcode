<?php

class Puzzle
{
    protected string $file;
    protected string $regex;

    public function __construct($file, $regex)
    {
        $this->file = $file;
        $this->regex = $regex;
    }

    public function run() : int
    {
        $array = $this->convertInputToArr();

        $tot = 0;

        foreach ($array as $item) {

            $item = $this->normalizeNumbersWithOverlappedLetters($item); // ex: fivecgtwotwo3oneighth ==> must BE 58 (NOT 51 !!!!)
            $mixed_integers = $this->getArrayViaRegex($item);

            $integers = $this->remapAllToIntegers($mixed_integers);

            if(!empty($integers)) {
                $tot += $this->calcSubTot($integers);
            }
        }

        return $tot;
    }

    private function convertInputToArr() : array
    {
        $input = file_get_contents($this->file, true);
        return array_filter(explode("\n", $input));
    }

    private function remapAllToIntegers(array $mixed_integers) : array
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

    private function getArrayViaRegex(string $string) : array
    {
        preg_match_all($this->regex, $string, $matches);
        return $matches[0];
    }

    private function calcSubTot(array $integers) : int
    {
        $first_int = $integers[0];
        $last_int = $integers[count($integers)-1];
        return (integer) $first_int.$last_int;
    }

    private function normalizeNumbersWithOverlappedLetters(string $string) : string
    {
        // var_dump($string);  // fivecgtwotwo3oneighth
        $numbs = ['one','two','three','four','five','six','seven','eight','nine'];
        foreach ( $numbs as $n) {
            $string = str_replace($n, $this->getFirstLetter($n).$n,$string);
        }

        return $string;
    }

    private function getFirstLetter(string $string) : string
    {
        return substr($string, 0, 1);
    }
}
