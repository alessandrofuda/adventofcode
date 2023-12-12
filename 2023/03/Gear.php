<?php

class Gear
{
    private string $file;

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
                    echo $e->getMessage();
                    $validated[] = (integer) $number;
                }
            }
        }
        return array_sum($validated);
    }

    private function convertInputToArr(): array
    {
        $input = file_get_contents($this->file, true);
        return array_filter(explode("\n", $input));
    }

    function getNumbersInCurrentRow($row) : array
    {
        preg_match_all('/\d+/', $row, $matches);
        return $matches[0];
    }

    function getNumberPositions($number,$row, $offset) : array // error IF: ..24..4.. (find 4 in this row..)
    {
        var_dump('Offset: '.$offset);
        $first_pos=strpos($row,$number, $offset); // BUG !!! todo add offsett!!!
        //$res=preg_match_all("/[^\d]?$number.*[^\d]?/", $row, $matches, PREG_OFFSET_CAPTURE);  // BUG ..24..24*..

        var_dump('Number: '.$number);
        var_dump('First pos: '.$first_pos);
        //var_dump($matches);
        // die();
        //$first_pos = $matches[0][1]+1;

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
    function searchSymbolInLine($row, $positions, $number) : void
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
            // $found=(!is_numeric($character)) && $character != '.';
            // var_dump($found);
            if($found){
                throw new Exception("---adjacent symbol found!---:  - Included number: $number\n"); // $matches[0]
                // throw new Exception();
            }
        }
    }
}


