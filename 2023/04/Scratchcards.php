<?php
// first match --> 1 point
// others matches (>1) --> each double point (ciascun match successivo al primo raddoppia i punti della card)

//Card 1: 41 48 83 86 17 | 83 86  6 31 17  9 48 53 --> winn numb: 48, 83, 86, 17  --> 4 matches, exponent is: 4-1=3  ==> (2^3) = 8 POINTS (2 is a const)
//Card 2: 13 32 20 16 61 | 61 30 68 82 17 32 24 19
//Card 3:  1 21 53 59 44 | 69 82 63 72 16 21 14  1
//Card 4: 41 92 73 84 69 | 59 84 76 51 58  5 54 83
//Card 5: 87 83 26 28 32 | 88 30 70 12 93 22 82 36
//Card 6: 31 18 13 56 72 | 74 77 10 23 35 67 36 11

// Card N: <winning_numbers> | <numbers>
// find which numbers are winning_numbers!!
// count matches and matches-1 is the exponent


class Scratchcards
{
    private string $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function run(): int
    {
        $rows = $this->convertInputToArr();
        $points = [];

        foreach ($rows as $row) {
            // $card = $this->getCardNumb($row);
            $winning_numbers = $this->getWinningNumbers($row);
            $my_numbers = $this->getMyNumbers($row);

            $common_values = array_intersect($winning_numbers, $my_numbers);

            if(count($common_values) > 0) {
                $exponent = count($common_values) - 1;
                $points[] = pow(2, $exponent);
            }else{
                $points[] = 0;
            }
        }

        return array_sum($points);
    }


    private function convertInputToArr(): array
    {
        $input = file_get_contents($this->file, true);
        return array_filter(explode("\n", $input));
    }

    private function getCardNumb(mixed $row) : string
    {
        preg_match('/Card [0-9]+/', $row, $matches);
        return substr($matches[0], 5);
    }

    private function getWinningNumbers(string $row) : array
    {
        preg_match('/: .*\|/', $row, $matches);
        preg_match_all('/[0-9]+/', $matches[0], $numbers);
        return $this->convertItemsToIntegers($numbers[0]);
    }

    private function getMyNumbers(string $row) : array
    {
        preg_match('/\|.*$/', $row, $matches);
        preg_match_all('/[0-9]+/', $matches[0], $numbers);
        return $this->convertItemsToIntegers($numbers[0]);
    }

    private function convertItemsToIntegers(array $numbers) : array
    {
        return array_map(function($item){
            return (int) $item;
        }, $numbers);
    }

}
