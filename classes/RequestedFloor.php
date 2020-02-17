<?php

/**
 * Class RequestedFloor
 */
class RequestedFloor
{

    public $level;
    public $direction;
    public $cost = -1;

    public function setLevel($f)
    {
        $this->level = $f;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setDirection($d)
    {
        $this->direction = $d;
    }

    public function getDirection()
    {
        return $this->direction;
    }

}
