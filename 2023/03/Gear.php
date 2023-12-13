<?php

class Gear
{
    private string $file;
    private array $validated;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function run(): int
    {
        $rows = $this->convertInputToArr();
        $validated = [];

        foreach ($rows as $key => $row) {

            $numbers = $this->getNumbersInCurrentRow($row);
            $offset = 0;

            foreach ($numbers as $number) {
                try {

                    $positions = $this->getNumberPositions($number, $row, $offset); // ex 4,5,6
                    $offset = end($positions)+1;

                    $this->searchSymbolInLine($row, $positions, $number);

                    if ($key > 0) {
                        $this->searchSymbolInLine($rows[$key - 1], $positions, $number);
                    }

                    if ($key < (count($rows) - 1)) {
                        $this->searchSymbolInLine($rows[$key + 1], $positions, $number);
                    }

                } catch (Exception $e) {
                    // echo json_decode($e->getMessage(), true)['msg']."\n";
                    echo $e->getMessage()."\n";
                    // $validated[] = ['number' => (integer) $number, 'symbol' => json_decode($e->getMessage(), true)['symbol']];
                    $validated[] = (integer) $number;
                }
            }
        }

        // $this->validated = $validated;
        // $validated_numbers = array_map(fn($item) => $item['number'], $validated);

        return array_sum($validated);
    }

    private function convertInputToArr(): array
    {
        $input = file_get_contents($this->file, true);
        return array_filter(explode("\n", $input));
    }

    private function getNumbersInCurrentRow($row) : array
    {
        preg_match_all('/\d+/', $row, $matches);
        return $matches[0];
    }

    private function getNumberPositions($number,$row, $offset) : array // error IF: ..24..4.. (find 4 in this row..)
    {
        $first_pos=strpos($row,$number, $offset); // !!! IMP: add offset to avoid BUG !!!

        $length=strlen((string) $number);
        $positions=[$first_pos];

        for($x=1;$x<$length;$x++){
            $positions[]=$first_pos+$x;
        }

        return $positions;
    }

    /**
     * @throws Exception
     */
    private function searchSymbolInLine($row, $positions, $number) : void
    {
        if($positions[0]>0){
            array_unshift($positions,$positions[0]-1);
        }

        if(end($positions) < strlen($row)){
            array_push($positions, end($positions)+1);
        }

        foreach ($positions as $position){

            $character = substr($row,$position,1);
            $found=(bool) preg_match('/[^\d.]/',$character,$matches);

            if($found){
                // throw new Exception(json_encode(['msg' => "adjacent symbol found: $matches[0] - Include number: $number", 'symbol' => $matches[0]]));
                throw new Exception("adjacent symbol found: $matches[0] - Include number: $number");
            }
        }
    }

    public function run2() : int
    {
        $rows = $this->convertInputToArr();

        foreach ($rows as $key => $row) {

            $asterisk_positions = $this->getAsterisksPositInCurrentRow($row);
            $adj_numbers_in_current_line = [];
            $adj_numbers_in_previous_line = [];
            $adj_numbers_in_next_line = [];

            foreach ($asterisk_positions as $asterisk_position) {

                $adj_numbers_in_current_line[] = $this->searchAdjNumbersInLine($row, $asterisk_position, true);

                if ($key > 0) {
                    $adj_numbers_in_previous_line[] = $this->searchAdjNumbersInLine($rows[$key - 1], $asterisk_position, false);
                }

            if ($key < (count($rows) - 1)) {
                    $adj_numbers_in_next_line[] = $this->searchAdjNumbersInLine($rows[$key + 1], $asterisk_position, false);
                }

            }

            $adj_numbers_in_current_line = $this->remapFilterArr($adj_numbers_in_current_line);
            $adj_numbers_in_previous_line = $this->remapFilterArr($adj_numbers_in_previous_line);
            $adj_numbers_in_next_line = $this->remapFilterArr($adj_numbers_in_next_line);

            var_dump($adj_numbers_in_current_line);
            var_dump($adj_numbers_in_next_line);

            die(); // end-first-row

        }

        // die(); // end all rows


        return array_sum($products);
    }


//    private function getAdjAsterisksNumbers() : array
//    {
//        $asterisks_numbers = array_filter(array_map(fn($item) => ($item['symbol'] == '*') ? $item['number'] : null, $this->validated));
//        return array_values($asterisks_numbers); // re-index arr
//    }
    private function getAsterisksPositInCurrentRow(mixed $row) : array
    {
        preg_match_all('/\*/', $row, $matches, PREG_OFFSET_CAPTURE);
        $matches = $matches[0];
        return array_map(fn($item) => $item[1], $matches);
    }

    private function searchAdjNumbersInLine(string $row, int $position, bool $is_current_line = false) : array
    {
        preg_match_all('/\d+/', $row, $matches, PREG_OFFSET_CAPTURE);
        $numbers = $matches[0];
        $adjacent_numbers = [];

        foreach ($numbers as $number) {
            if($this->numberIsAdjacent($position, $number, $is_current_line)){
                $adjacent_numbers[] = $number[0];
            }
        }

        return $adjacent_numbers;
    }

    private function numberIsAdjacent(int $asterisk_position, array $number, bool $is_current_line) : bool  // number: [(string)number, (int)position]
    {
        $n = $number[0]; // string
        $n_start_pos = $number[1]; // int
        $n_lenght = strlen($n);
        $n_positions = [$n_start_pos];

        for ($i=1;$i<$n_lenght; $i++){
            $n_positions[] = $n_start_pos + $i;
        }
        if($is_current_line) {
            return (in_array($asterisk_position+1, $n_positions) || in_array($asterisk_position-1, $n_positions));
        }else{
            return (in_array($asterisk_position+1, $n_positions) || in_array($asterisk_position-1, $n_positions) || in_array($asterisk_position, $n_positions));
        }
    }

    private function remapFilterArr(array $adj_numbers_in_current_line) : array
    {
        $adj_numbers_in_current_line = array_values(array_filter($adj_numbers_in_current_line));
        return array_map(fn($item) => $item[0], $adj_numbers_in_current_line);
    }


}


