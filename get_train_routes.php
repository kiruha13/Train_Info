<?php

require dirname(__DIR__) . '/TrainSchedule/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['departureCode'])) {
    $departureCode = $_POST['departureCode'];
    $arrivalCode = $_POST['arrivalCode'];
    $departDate = $_POST['departureDate'];
    $dateObject = date('d.m.Y',strtotime($departDate));


    if ($departureCode !== '') {
        $params = [
            'dir' => 0,
            'tfl' => 3,
            'checkSeats' => 1,
            'code0' => $departureCode,
            'code1' => $arrivalCode,
            'dt0' => $dateObject
        ];

        $api = new Rzd\Api();
        $trainRoutes = $api->trainRoutes($params);

        echo $trainRoutes;
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method']);
}
