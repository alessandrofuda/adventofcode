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
    private array $rows;
    private string $input_compiled;

    public function __construct($file)
    {
        $this->file = $file;
        $this->rows = $this->convertInputToArr();
        $this->input_compiled = $this->createNewInputCompiledFile();
    }

    public function run() : int
    {
        $points = [];

        foreach ($this->rows as $row) {
            // $card = $this->getCardNumb($row);
            $winning_numbers = $this->getWinningNumbers($row);
            $my_numbers = $this->getMyNumbers($row);

            $matching_values = array_intersect($winning_numbers, $my_numbers);

            if(count($matching_values) > 0) {
                $exponent = count($matching_values) - 1;
                $points[] = pow(2, $exponent);
            }else{
                $points[] = 0;
            }
        }

        return array_sum($points);
    }

    /**
     * @throws Exception
     */
    public function run2() : int
    {
        $matches_per_card = $this->getMatchesPerCards();
        $first_item = 0;
        $created_per_iteration = 1;

        while ($created_per_iteration > 0) {
            $input_compiled_rows = $this->getInputCompiledContent();

            $created = [];
            for($i=$first_item; $i<count($input_compiled_rows); $i++) {
                $card = $this->getCardNumb($input_compiled_rows[$i]);
                $matches_count = $this->getMatchingNumberInCard($matches_per_card, $card);
                $created[] = $this->createCardCopies($matches_count, $card);
                $first_item = $i+1;

            }
            $created_per_iteration = array_sum($created);
        }

        return count($this->getInputCompiledContent());
    }

    private function convertInputToArr(): array
    {
        $input = file_get_contents($this->file, true);
        return array_filter(explode("\n", $input));
    }

    private function getCardNumb(string $row) : int
    {
        preg_match('/Card[ ]+[0-9]+/', $row, $matches);
        $string = substr($matches[0], 5);
        return (int) trim($string);
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

    /**
     * @throws Exception
     */
    private function createNewInputCompiledFile() : string
    {
        $new_filename = 'input_compiled.txt';
        copy($this->file, $new_filename) or die('Error in '.__METHOD__);

        return $new_filename;
    }

    private function createCardCopies(int $matching_values_count, int $current_card_number) : int
    {
        $file = fopen($this->input_compiled, 'a') or die("Unable to open file!");
        $cards_to_copy = $this->cardsToCopy($current_card_number, $matching_values_count);
        foreach ($cards_to_copy as $card) {
            fwrite($file, $this->rows[$card-1]."\n");
        }
        fclose($file);
        return count($cards_to_copy); // copied
    }

    private function cardsToCopy(int $current_card_number, int $matching_values_count) : array
    {
        $cards_to_copy = [];
        for ($i=$current_card_number+1;$i<=($current_card_number+$matching_values_count);$i++){
            $cards_to_copy[] = $i;
        }
        return $cards_to_copy;
    }

    private function getMatchesPerCards() : array
    {
        $matches_per_card = [];
        foreach ($this->rows as $key => $row) {
            $winning_numbers = $this->getWinningNumbers($row);
            $my_numbers = $this->getMyNumbers($row);
            $matching_values_count = count(array_intersect($winning_numbers, $my_numbers));
            $matches_per_card[] = ['card_number' => $key+1, 'matches_number' => $matching_values_count];
        }
        return $matches_per_card;
    }

    private function getInputCompiledContent() : array
    {
        $input = file_get_contents($this->input_compiled, true);
        return array_filter(explode("\n", $input));
    }

    private function getMatchingNumberInCard(array $matches_per_card, int $card) : int
    {
        $matches = array_map(function($item) use ($card) {
            if($item['card_number'] == $card){
                return $item['matches_number'];
            }else{
                return null;
            }
        }, $matches_per_card);

        $matches = array_values(array_filter($matches)); // filter & reindex
        return $matches[0] ?? 0;
    }

}
