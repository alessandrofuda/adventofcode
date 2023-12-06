<?php

class Cube
{
    protected string $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function run() : int
    {
        $games = $this->convertInputToArr();
        $validated_games = [];

        foreach ($games as $game) {
            $sets = $this->getSets($game);

            $break = false;

            foreach ($sets as $set) {
                if(!$this->passingChecks($set)){ // check colors/thresholds
                    $break = true;
                    break;
                }
            }

            if(!$break) {
                $validated_games[] = $this->getGameId($game);
            }
        }

        $tot = $this->sumAllItemsOfArray($validated_games);
        return $tot;

    }

    private function getGameId(mixed $game) : int
    {
        // return $id;
    }
}
