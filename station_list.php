<?php
require dirname(__DIR__) . '/TrainSchedule/vendor/autoload.php';

$api = new Rzd\Api();

if (isset($_GET['trainNumber'])) {
    $trainNumber = $_GET['trainNumber'];
    $departDate = $_GET['datet'];
    $dateObject = date('d.m.Y',strtotime($departDate));


    $params = [
        'trainNumber' => $trainNumber,
        'depDate' => $dateObject,
    ];
    $trainStation = $api->trainStationList($params);

} else {
    echo 'Номер поезда не указан.';
}
?>
<html>
<head>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>

</head>
<body>
<div class="container">
    <div class="row">
        <div class="col">
            <h2 id="info" class="text-left"></h2>
        </div>
        <div class="col text-right">
            <a href="index.php" class="btn btn-primary mt-3 mb-3">Вернуться на главную страницу</a>
        </div>
    </div>
</div>



<table id="trainStationsTable" class="table table-striped">
    <thead class="thead-dark">
    <tr>
        <th>Станция</th>
        <th>Время прибытия</th>
        <th>Время стоянки</th>
        <th>Время отправления</th>
    </tr>
    </thead>
    <tbody id="trainStationsBody">
    <!-- Здесь будут данные о станциях -->
    </tbody>
</table>

<script>
    function displayTrainRoutes(trainRoutes) {
        var inform = $('#info');
        var tableBody = $('#trainStationsTable tbody');
        tableBody.empty();
        var count = 0;
        inform.append("Информация о станциях поезда" + " <?php echo($trainNumber); ?>")
        trainRoutes.routes.forEach(function (routes) {
            count += 1;
            if(count === 1) {
                routes.stops.forEach(function (stops) {
                    var waitingTimeText = stops.waitingTime !== null ? stops.waitingTime : '0';
                    var row = $('<tr>').append(
                        $('<td>').text(stops.station.name),
                        $('<td>').text(stops.arvTime),
                        $('<td>').text(waitingTimeText + ' Мин.'),
                        $('<td>').text(stops.depTime),
                    );
                    tableBody.append(row);
                });
            }
        });
    }

    // Получение данных PHP в JavaScript
    var trainStationData = <?php echo($trainStation); ?>;

    // Вызов функции для отображения данных
    displayTrainRoutes(trainStationData);
</script>
</body>
</html>

