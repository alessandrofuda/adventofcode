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

            foreach ($numbers as $number) {

                try {
                    $positions = $this->getNumberPositions($number, $row); // ex 4,5,6

                    $this->searchSymbolInLine($row, $positions);

                    if ($key > 0) {
                        $this->searchSymbolInLine($rows[$key - 1], $positions);
                    }

                    if ($key < (count($rows) - 1)) {
                        $this->searchSymbolInLine($rows[$key + 1], $positions);
                    }

                } catch (Exception $e) {
                    echo $e->getMessage();
                    $validated[] = (integer)$number;
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
        $pattern='/\d+/';
        preg_match_all($pattern, $row, $matches);
        return $matches[0];
    }

    function getNumberPositions($number,$row) : array
    {
        $first_pos=strpos($row,$number);
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
    function searchSymbolInLine($row, $positions) : void
    {
        if($positions[0]>0){
            array_unshift($positions,$positions[0]-1);
        }

        if(end($positions) < strlen($row)){
            array_push($positions, end($positions)+1);
        }

        foreach ($positions as $position){

            $character = substr($row,$position,1);
            $pattern='/[^\d.]/';
            $found=(bool) preg_match($pattern,$character);

            if($found){
                throw new Exception("symbol found!\n");
            }
        }
    }
}


