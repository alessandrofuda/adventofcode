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
        $sums_per_line = [];

        foreach ($rows as $key => $row) {

            $asterisk_positions = $this->getAsterisksPositInCurrentRow($row);
            $adjacent_numbers = [];

            foreach ($asterisk_positions as $asterisk_position) {

                $adjacent_numbers[] = $this->searchAdjNumbersInLine($key, $row, $asterisk_position, true);

                if ($key > 0) {
                    $adjacent_numbers[] = $this->searchAdjNumbersInLine($key, $rows[$key-1], $asterisk_position, false);
                }

                if ($key < (count($rows) - 1)) {
                    $adjacent_numbers[] = $this->searchAdjNumbersInLine($key, $rows[$key+1], $asterisk_position, false);
                }
            }

            $adjacent_numbers = $this->remapFilterArr($adjacent_numbers);
            // print_r('Not grouped: ');
            // print_r($adjacent_numbers);
            $grouped = $this->groupArrayByKeys(['line','asterisc_position'], $adjacent_numbers);
            // print_r('Grouped:');
            // print_r($grouped);
            $sums_per_line[] = $this->multiplyAdjacentNumbersIfArePairsAndSumIt($grouped);
            // var_dump($sums_per_line);
            // die('stopp'); // end-first-row

        }
        // die('stop');
        var_dump($sums_per_line);
        return array_sum($sums_per_line);
    }

    private function getAsterisksPositInCurrentRow(mixed $row) : array
    {
        preg_match_all('/\*/', $row, $matches, PREG_OFFSET_CAPTURE);
        $matches = $matches[0];
        return array_map(fn($item) => $item[1], $matches);
    }

    private function searchAdjNumbersInLine(int $row_index, string $row, int $position, bool $is_current_line = false) : array
    {
        preg_match_all('/\d+/', $row, $matches, PREG_OFFSET_CAPTURE);
        $numbers = $matches[0];
        $adjacent_numbers = [];

        foreach ($numbers as $number) {
//            print_r('row: '. $row."\n");
//            print_r('asterisc position: '.$position."\n");
//            print_r('number: '.$number[0]."\n");
//            print_r('number position: '.$number[1]."\n");
//            var_dump($this->numberIsAdjacent($position, $number, $is_current_line));
//            print_r('----------------'."\n");

            if($this->numberIsAdjacent($position, $number, $is_current_line)){
                $adjacent_numbers[] = ['line' => $row_index, 'asterisc_position' => $position, 'adjacent_number' => $number[0]];
            }
        }

        return $adjacent_numbers;
    }

    private function numberIsAdjacent(int $asterisk_position, array $number, bool $is_current_line) : bool  // number: [(string)number, (int)position]
    {
        $n = $number[0]; // string  // 25
        $n_start_pos = $number[1]; // int  // 2
        $n_lenght = strlen($n);
        $n_positions = [$n_start_pos];

        for ($i=1;$i<$n_lenght; $i++){
            $n_positions[] = $n_start_pos + $i;   // [2,3]
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

    private function groupArrayByKeys(array $keys, array $adjacent_numbers) : array
    {
        $grouped = [];
        $lines = $this->getDistinctValuesOfLineAttr($adjacent_numbers);

        foreach ($lines as $line) {
            $positions = $this->getDistinctPairValuesLineAsteriscPosition($adjacent_numbers, $line); // [4,9];
            foreach ($positions as $position) {
                foreach ($adjacent_numbers as $item){

                    if($item['line'] == $line && $item['asterisc_position'] == $position){
                        $grouped['line-'.$line]['position-'.$position]['line'] = $line;
                        $grouped['line-'.$line]['position-'.$position]['asterisc_position'] = $position;
                        $grouped['line-'.$line]['position-'.$position]['adjacent_numbers'][] = $item['adjacent_number'];
                    }
                }
            }
        }
        return $grouped;
    }

    private function getDistinctValuesOfLineAttr(array $adjacent_numbers) : array
    {
        $lines = array_map(function($item){
            return $item['line'];
        }, $adjacent_numbers);

        return array_unique($lines);
    }

    private function getDistinctPairValuesLineAsteriscPosition(array $adjacent_numbers, mixed $line) : array
    {
        $positions = array_map(function($item) use ($line) {
            if($item['line'] === $line) {
                return $item['asterisc_position'];
            }
        }, $adjacent_numbers);

        return array_unique($positions);
    }

    private function multiplyAdjacentNumbersIfArePairsAndSumIt(array $grouped) : int
    {
        $products = [];
        foreach ($grouped as $items) {
            foreach ($items as $item) {
                if(count($item['adjacent_numbers']) > 1) {
                    $products[] = array_product($this->convertToInt($item['adjacent_numbers']));
                }
            }
        }
        return array_sum($products);
    }

    private function convertToInt(array $adjacent_numbers) : array
    {
        return array_map(function($item){
            return (int) $item;
        }, $adjacent_numbers);
    }

}


