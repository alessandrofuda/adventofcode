<?php

class Cube
{
    const THRESHOLDS = [
        'red' => 12,
        'green' => 13,
        'blue' => 14,
    ];
    protected string $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function run() : int
    {
        $games = $this->convertInputToArr();

        $validated_games = [];

        foreach ($games as $k => $game) {
            $sets = $this->getSets($game);

            $break = false;

            foreach ($sets as $set) {
                if(!$this->passingChecks($set)){ // check colors/thresholds
                    $break = true;
                    break;
                }
            }

            if(!$break) {
                $validated_games[] = $k+1;
            }
        }

        return array_sum($validated_games);

    }

    private function convertInputToArr() : array
    {
        $input = file_get_contents($this->file, true);
        return array_filter(explode("\n", $input));
    }

    private function getSets(string $game) : array
    {
        $game = explode(':', $game);
        $sets = trim($game[1]);
        return explode('; ', $sets);
    }


    private function passingChecks(string $set) : bool
    {
        $set = $this->convertSetToAssArray($set);

        if(
            (array_key_exists('blue', $set) && ($set['blue'] > self::THRESHOLDS['blue']))
            || (array_key_exists('green', $set) && ($set['green'] > self::THRESHOLDS['green']))
            || (array_key_exists('red', $set) && ($set['red'] > self::THRESHOLDS['red']))
        )
        {
            return false;
        }
        return true;
    }

    private function convertSetToAssArray(string $set) : array
    {
        $n_cols = explode(', ', $set);
        $assoc = [];

        foreach ($n_cols as $n_col) {
            $arr = explode(' ', $n_col);
            $assoc[$arr[1]] = (int) $arr[0];
        }

        return $assoc;
    }

}
