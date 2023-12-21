<?php

require dirname(__DIR__) . '/TrainSchedule/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['departureCode'])) {
    $departureCode = $_POST['departureCode'];
    $arrivalCode = $_POST['arrivalCode'];
    $departDate = $_POST['departureDate'];
    $arrivalDate = $_POST['returnDate'];
    $dateObject = date('d.m.Y',strtotime($departDate));
    $dateObject1 = date('d.m.Y',strtotime($arrivalDate));


    if ($departureCode !== '') {
        $params = [
            'dir' => 1,
            'tfl' => 3,
            'checkSeats' => 1,
            'code0' => $departureCode,
            'code1' => $arrivalCode,
            'dt0' => $dateObject,
            'dt1' => $dateObject1
        ];

        $api = new Rzd\Api();
        $trainRoutes = $api->trainRoutesReturn($params);

        echo $trainRoutes;
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method']);
}

