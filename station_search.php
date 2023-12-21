<?php

require dirname(__DIR__) . '/TrainSchedule/vendor/autoload.php';

$api = new Rzd\Api();

if (isset($_GET['stationNamePart'])) {
    $params = [
        'stationNamePart' => $_GET['stationNamePart'],
        'compactMode' => 'y',
    ];

    $stations = $api->stationCode($params);
    if ($stations) {
        echo $stations;
    } else {
        echo 'Не найдено совпадений!';
    }
}

