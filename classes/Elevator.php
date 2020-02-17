<?php

/**
 * Class Elevator
 */
class Elevator
{
    private $isMoving = false; //boolean
    private $direction; // up, down
    private $requestedFloors = []; //array requestedFloor
    private $currentFloor; // current floor
    private $totalFloors;
    private $serviceFloors = [];

    /**
     * Elevator constructor.
     */
    public function __construct()
    {
        global $config;
        $this->totalFloors = $config['total_floors'];
        $this->serviceFloors = $config['service_floors'];
    }

    /**
     * Set moving
     * Input
     * @param $moving
     * @return void
     */
    public function setIsMoving($moving)
    {
        $this->isMoving = $moving;
    }

    /**
     * Set direction
     * @param $d
     * @return void
     */
    public function setDirection($d)
    {
        $this->direction = $d;
    }

    /**
     * Get direction
     * @return mixed
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set current floor
     * @param $floor
     * @return void
     */
    public function setCurrentFloor($floor)
    {
        $this->currentFloor = $floor;
    }

    /**
     * Add floor you want to move to requested Floors
     * Input  $level floor level you want to move to
     * $direction up or down
     * Output void
     * @param $level
     * @param $direction
     * @return void
     */
    public function requestFloor($level, $direction)
    {
        if ($this->isServiceFloor($level)) {
            Log::write("F" . $level . " is service.");
            return;
        }
        $floor = new RequestedFloor();
        $floor->setLevel($level);
        $floor->setDirection($direction);
        $this->addRequestedFloors($floor);
    }

    /**
     * @param $level
     * @param $direction
     * @return void
     */
    public function moveToFloor($level, $direction)
    {
        $this->requestFloor($level, $direction);
    }

    /**
     * Transport from this floor to another floor
     * Input  $fromLevel $toLevel
     * @param $fromLevel
     * @param $toLevel
     * @return void
     */
    public function transport($fromLevel, $toLevel)
    {

        if ($fromLevel < $toLevel) {
            $d = "up";
        } else {
            $d = "down";
        }

        Log::write("Transport from F" . $fromLevel . " " . $d . " to F" . $toLevel);

        $this->requestFloor($fromLevel, $d);
        $this->moveToFloor($toLevel, $d);
    }

    /**
     * Get current floor
     * @return mixed
     */
    public function getCurrentFloor()
    {
        return $this->currentFloor;
    }

    /**
     * Check requested floor existed or not
     * @param $requestedFloor
     * @return bool
     */

    public function existedRequestedFloor($requestedFloor)
    {
        foreach ($this->requestedFloors as $floor) {
            if ($floor->direction == $requestedFloor->direction && $floor->level == $requestedFloor->level) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get requested floors
     * @return array
     */
    public function getRequestedFloors()
    {
        return $this->requestedFloors;
    }

    /**
     * Get Service floors
     * @return array|mixed
     */
    public function getServiceFloors()
    {
        return $this->serviceFloors;
    }

    /**
     * Add requested floor
     * @param $requestedFloor
     * @return void
     */
    public function addRequestedFloors($requestedFloor)
    {
        if (!$this->existedRequestedFloor($requestedFloor)) {
            $this->requestedFloors[] = $requestedFloor;
            $this->sortRequestedFloors();
            $this->buildCost();
        }
    }

    /**
     * Remove requested floor
     * @param $requestedFloor
     * @return void
     */
    public function removeRequestedFloors($requestedFloor)
    {
        $floors = [];
        $total = $this->totalRequestedFloors();
        for ($i = 0; $i < $total; $i++) {
            $floor = $this->requestedFloors[$i];
            if ($floor->direction == $requestedFloor->direction && $floor->level == $requestedFloor->level) {
                unset($this->requestedFloors[$i]);
            }
        }
    }

    /**
     * Get total requested floors
     * @return int
     */
    public function totalRequestedFloors()
    {
        return count($this->requestedFloors);
    }

    /**
     *  Elevator is change direction from up to down or down to up
     * @return void
     */
    public function switchDirection()
    {
        if ($this->totalRequestedFloors() > 0) {
            Log::write("Switch direction from " . $this->direction . " to ");
            $this->setDirection($this->getDirection() == "up" ? "down" : "up");
        } else {
            $this->isMoving = false;
            $this->direction = "stand";
        }
    }

    /**
     * Auto swicth direction if elevator is at first floor or last floor
     * @return void
     */
    public function autoDetectSwitchDirection()
    {
        if ($this->currentFloor == 1) {
            $this->direction = "up";
        } else if ($this->currentFloor == $this->totalFloors) {
            $this->direction = "down";
        }
    }

    /**
     * Check Elevator is moving up or not
     * @return bool
     */
    public function isUp()
    {
        return $this->getDirection() == "up";
    }

    /**
     * Check Elevator is moving down or not
     * @return bool
     */
    public function isDown()
    {
        return $this->getDirection() == "down";
    }

    /**
     * Check Elevator is stand or moving
     * @return bool
     */
    public function isStand()
    {
        if ($this->direction == "stand" && $this->totalRequestedFloors() == 0) {
            return true;
        }
        return false;
    }

    /**
     * Open door
     * @return void
     */
    public function openDoor()
    {
        if (!$this->isServiceFloor($this->currentFloor)) {
            Log::write("Open door at F" . $this->currentFloor);
        } else {
            Log::write("This floor is service. Can not open door at Floor " . $this->currentFloor);
        }
    }

    /**
     * Close door
     * @return void
     */
    public function closeDoor()
    {
        if (!$this->isServiceFloor($this->currentFloor)) {
            Log::write("Close door at F" . $this->currentFloor);
        }
    }

    /**
     * Check floor is service or not
     * @param $floor
     * @return bool
     */
    public function isServiceFloor($floor)
    {
        return in_array($floor, $this->serviceFloors);
    }

    /**
     * E elevator will process when current floor is at requested floor
     * @param $floor
     * @return void
     */
    public function processAtRequestedFloor($floor)
    {
        if ($this->currentFloor == $floor->level) {
            $this->openDoor();
            $this->closeDoor();
            $this->removeRequestedFloors($floor);
            $this->buildCost();
        }

        $this->isMoving = true;
        $this->direction = $floor->direction;
        $maxFloor = $this->getMaxRequestFloorLevelByDirection($this->direction);
        if ($maxFloor == null) {
            $this->switchDirection();
        } else {
            $this->autoDetectSwitchDirection();
        }
        if ($this->isUp()) {
            $this->currentFloor += 1;
        } else if ($this->isDown()) {
            $this->currentFloor -= 1;
        }

        if ($this->totalRequestedFloors() > 0) {
            Log::write("Move " . $this->getDirection() . " F" . $this->getCurrentFloor() . "<br/> ==========");
        }
    }

    /**
     * run Elevator
     * @return void
     */
    public function run()
    {
        if ($this->isStand()) {
            $this->isMoving = false;
            $this->direction = "stand";
            Log::write("Elevator " . $this->getDirection() . " at F" . $this->getCurrentFloor() . "<br/>");
            return;
        }
        Log::write("In F" . $this->getCurrentFloor());
        $floor = $this->getMinCost();
        if ($floor == null) {
            return;
        }

        $this->processAtRequestedFloor($floor);

        $this->run();
    }

    /**
     * Get requested min cost of level to move elevator to this floor
     * @return mixed|null
     */
    public function getMinCost()
    {
        if ($this->currentFloor == 1 && $this->totalRequestedFloors() == 0) {
            return null;
        }
        $this->buildCost();
        $min = $this->totalFloors;
        $minFloor = null;
        foreach ($this->requestedFloors as $floor) {
            if ($floor->cost <= $min) {
                $min = $floor->cost;
                $minFloor = $floor;
            }
        }
        return $minFloor;
    }

    /**
     * Get requested last level to switch direction
     * @param $d
     * @return mixed|null
     */
    public function getMaxRequestFloorLevelByDirection($d)
    {
        if ($d == "up") {
            return $this->getRequestedMaxLevel($d);
        }

        if ($d == "down") {
            return $this->getRequestedMinLevel($d);
        }
    }

    /**
     * Get requested farthest level to move elevator to this floor
     * @param $d
     * @return mixed|null
     */
    public function getRequestedMaxLevel($d)
    {
        $max = 1;
        $maxFloor = null;
        foreach ($this->requestedFloors as $floor) {
            if ($floor->direction == $d && $floor->level > $max) {
                $max = $floor->level;
                $maxFloor = $floor;
            }
        }

        return $maxFloor;
    }

    /**
     * Get requested nearest level to move elevator to this floor
     * @param $d
     * @return mixed|null
     */
    public function getRequestedMinLevel($d)
    {
        $min = $this->totalFloors;
        $minFloor = null;
        foreach ($this->requestedFloors as $floor) {
            if ($floor->direction == $d && $floor->level <= $min) {
                $min = $floor->level;
                $minFloor = $floor;
            }
        }

        return $minFloor;
    }

    /**
     * Build cost in requested floors
     * @return void
     */
    public function buildCost()
    {
        $total = $this->totalFloors;
        $floors = [];
        foreach ($this->requestedFloors as $floor) {
            $floor->cost = $floor->level - $this->currentFloor;
            $floor->cost = $floor->cost < 0 ? -$floor->cost : $floor->cost;
            if ($this->isMoving && $this->direction != $floor->direction) {
                $floor->cost += $total;
            }

            $floors[] = $floor;
        }
        $this->requestedFloors = $floors;
    }

    /**
     * Sort request floors by direction
     * @return void
     */
    public function sortRequestedFloors()
    {
        $args = ["direction", "level"];
        usort($this->requestedFloors, function ($a, $b) use ($args) {
            $i = 0;
            $c = count($args);
            $cmp = 0;
            while ($cmp == 0 && $i < $c) {
                $i++;
            }

            return $cmp;
        });
    }

}
