<?php

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
        var_dump($rows);
        die();
    }





    private function convertInputToArr(): array
    {
        $input = file_get_contents($this->file, true);
        return array_filter(explode("\n", $input));
    }

}
