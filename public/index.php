<?php

function __autoload($classname) {
    $filename = "./../app/class/". $classname .".php";
    if (file_exists($filename)) {
        include_once($filename);
    }
}

$app = new App();

if (!isset($_GET['api'])) {
    die('Please confirm usage with <a href="https://github.com/anlide/trip-sorter">https://github.com/anlide/trip-sorter</a>');
}

// Anyway next code we will render Json
header('Content-type: application/json');

try {
    /*$cities1 = $app->getCities();
    $cities2 = $app->getCities();

    foreach ($cities1 as $city1) {
        foreach ($cities2 as $city2) {
            if ($city1 == $city2) continue;
            var_dump($city1 . $city2 . 'cheapest');
            $app->findPath($city1, $city2, 'cheapest');
            var_dump($city1 . $city2 . 'fastest');
            $app->findPath($city1, $city2, 'fastest');
        }
    }
    exit;*/

    switch ($_GET['api']) {
        case 'city':
            echo json_encode(['cities' => $app->getCities()]);
            break;
        case 'transport':
            echo json_encode(['transports' => $app->getTransports()]);
            break;
        case 'findpath':
            // Check input params
            if (!isset($_GET['departure'])) {
                throw new Exception('Missed "departure" param');
            }
            if (!isset($_GET['arrival'])) {
                throw new Exception('Missed "arrival" param');
            }
            if (!isset($_GET['algorithm'])) {
                throw new Exception('Missed "algorithm" param');
            }
            if (!in_array($_GET['algorithm'], ['cheapest', 'fastest'])) {
                throw new Exception('Param "algorithm" should be "cheapest" or "fastest"');
            }

            // Find path
            echo json_encode(['deals' => $app->findPath($_GET['departure'], $_GET['arrival'], $_GET['algorithm'])->getJsonData()]);
            break;
        default:
            throw new Exception('Wrong API type');
            break;
    }
} catch (Exception $exception) {
    echo json_encode(['error' => $exception->getMessage()]);
}
