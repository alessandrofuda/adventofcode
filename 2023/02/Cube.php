<?php

class Cube
{
    const THRESHOLDS = [
        'red' => 12,
        'green' => 13,
        'blue' => 14,
    ];
    private string $file;
    private array $games;
    private array $colors;

    public function __construct($file)
    {
        $this->file = $file;
        $this->games = $this->convertInputToArr();
        $this->colors = array_keys(self::THRESHOLDS);
    }

    public function run() : int
    {
        $validated_games = [];

        foreach ($this->games as $k => $game) {
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


    // ----------------------------------------------------------


    public function run2() : int
    {
        $products = [];
        foreach ($this->games as $game) {
            $sets = $this->getSets($game);

            $max_values = [];
            foreach ($this->colors as $color) {
                $max = $this->getMaxValuePerColorBetweenSets($color, $sets);
                $max_values[] = $max;
            }
            $product = array_product($max_values);
            $products[] = $product;
        }
        return array_sum($products);
    }

    private function getMaxValuePerColorBetweenSets(string $color, array $sets) : int
    {
        $assoc_arr = $this->convertSetsToArray($sets);

        $sets_per_color = array_map(function($item) use ($color){
            return $item[$color] ?? null;
        }, $assoc_arr);

        return max($sets_per_color);
    }

    private function convertSetsToArray(array $sets) : array
    {
        $assoc_arr = [];
        foreach ($sets as $set) {
            $assoc_arr[] = $this->convertSetToAssArray($set);
        }
        return $assoc_arr;
    }
}
