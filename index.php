<?php
require_once("config/config.php");
echo "<pre>";

echo "<h3>Прімер тестового запуска</h3><br/>";
$e = new Elevator();
echo "Лифт стоит на первом этаже. <br/>";
$e->setCurrentFloor(1);
$e->setIsMoving(false);
$e->setDirection("stand");
/**
 * Call elevators as you wish =)
 */
echo "Запрос с 6-го этажа спуститься на первый этаж. <br/>";
$e->transport(6, 1);

echo "Запрос с 5-го этажа подняться на 7-й этаж. <br/>";
//$e->requestFloor(5, "up");
//$e->moveToFloor(7, "up");
$e->transport(5, 7);

echo "Запрос с 3-го этажа спуститься на первый этаж. <br/>";
//$e->requestFloor(3, "down");
//$e->moveToFloor(1, "down");
$e->transport(3, 1);

echo "<br/>===========================<br/>";
echo "<h3>Данные лифта:</h3><br/>";
echo "Current floor level <strong>#" . $e->getCurrentFloor() . "</strong><br/>";
echo "Current direction <strong>" . $e->getDirection() . "</strong><br/>";
echo "Service floors: <br/>";
echo "<ul>";
$serviceFloors = $e->getServiceFloors();
foreach ($serviceFloors as $f) {
    echo "<li>";
    echo "Floor level: <strong>#{$f}</strong>";
    echo "</li>";
}
$requestedFloors = $e->getRequestedFloors();
foreach ($requestedFloors as $f) {
    echo "<li>";
    echo "Direction: <strong>" . $f->direction . "</strong> - ";
    echo "Floor level: <strong>#" . $f->level . "</strong>";
    echo "</li>";
}
echo "</ul>";
echo "<br/>===========================<br/>";
echo "<h3>Движение лифта</h3><br>===========================<br>";
$e->run();
echo "<h3>Остановка движения лифта</h3><br/>===========================<br/>";
echo "</pre>";