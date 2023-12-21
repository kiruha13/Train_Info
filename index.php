<!DOCTYPE html>
<html>
<head>
    <title>Поиск станций</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <style>
        /* Центрирование формы */
        body {
            justify-content: center;
            align-items: center;
            margin: 15px;
        }
        .custom-form {
            max-width: 800px; /* Ширина формы */
            width: 100%;
            text-align: left; /* Текст слева в форме */
        }
        .form-group select {
            margin-top: 10px; /* Регулировка размера отступа */
        }
        .red-text {
            color: red;
            font-weight: bold;
        }
        .bold-text{
            font-weight: bold;
        }
        #trainRoutesTable thead th {
            background-color: #ccc; /* Серый цвет фона */
        }

    </style>
</head>
<body>
<div class="container custom-form">
    <h2 class="text-center">Бронирование жд билетов</h2>
    <form id="stationForm">
        <div class="form-group">
            <label for="departureStationInput" class="form-label">Введите станцию отправления:</label>
            <input type="text" class="form-control" id="departureStationInput" name="departureStationInput"
                   oninput="getStations(this.value, 'departure')" onclick="showList('departure')">

            <select id="departureStationsList" class="form-control" multiple style="display: none;"></select>
        </div>

        <div class="form-group">
            <label for="arrivalStationInput" class="form-label">Введите станцию прибытия:</label>
            <input type="text" class="form-control" id="arrivalStationInput" name="arrivalStationInput"
                   oninput="getStations(this.value, 'arrival')" onclick="showList('arrival')">

            <select id="arrivalStationsList" class="form-control" multiple style="display: none;"></select>
        </div>
        <div class="form-group">
            <label for="departureDateInput" class="form-label">Выберите дату отправления:</label>
            <input type="date" class="form-control" id="departureDateInput" name="departureDateInput">
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" id="bothDirectionsCheckbox" onclick="showSecondDateField()">
                В обе стороны
            </label>
        </div>

        <div class="form-group" id="secondDateField" style="display: none;">
            <label for="arrivalDateInput" class="form-label">Выберите дату прибытия:</label>
            <input type="date" class="form-control" id="arrivalDateInput" name="arrivalDateInput">
        </div>

        <div class="form-group text-center">
            <button type="button" class="btn btn-primary" onclick="getTrainRoutes()">Найти</button>
        </div>
    </form>

</div>
<div id="trainRoutesContainer" class="container mt-4">
    <div class="table-responsive">
        <table id="trainRoutesTable" class="table table-bordered">
            <thead>
            <tr>
                <th>№ поезда</th>
                <th>Пункт отправления</th>
                <th>Время в пути</th>
                <th>Пункт прибытия</th>
                <th>Места</th>
                <th>Стоимость</th>
                <th>Подробнее</th>
            </tr>
            </thead>
            <tbody>
            <!-- Здесь будут данные о поездах -->
            </tbody>
        </table>
    </div>
</div>
<div id="trainRoutesContainer" class="container mt-4">
    <div class="table-responsive">
        <table id="returntrainRoutesTable" class="table table-bordered" style="display: none;">
            <thead>
            <tr>
                <th>№ поезда</th>
                <th>Пункт отправления</th>
                <th>Время в пути</th>
                <th>Пункт прибытия</th>
                <th>Места</th>
                <th>Стоимость</th>
                <th>Подробнее</th>
            </tr>
            </thead>
            <tbody>
            <!-- Здесь будут данные о поездах -->
            </tbody>
        </table>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#departureStationInput').on('input', function() {
            this.value = this.value.toUpperCase();
        });
        $('#arrivalStationInput').on('input', function() {
            this.value = this.value.toUpperCase();
        });
    });

    function showSecondDateField() {
        var checkbox = document.getElementById('bothDirectionsCheckbox');
        var secondDateField = document.getElementById('secondDateField');

        if (checkbox.checked) {
            secondDateField.style.display = 'block';
        } else {
            secondDateField.style.display = 'none';
        }
    }

    function getStations(inputValue, type) {
        $.ajax({
            url: 'station_search.php',
            method: 'GET',
            data: {stationNamePart: inputValue},
            dataType: 'json',
            success: function (data) {
                if (type === 'departure') {
                    displayStations(data, 'departure');
                } else if (type === 'arrival') {
                    displayStations(data, 'arrival');
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    // Добавление объекта для хранения кодов станций
    var selectedStations = {
        departure: {},
        arrival: {}
    };

    function displayStations(stations, type) {
        var stationsList;
        if (type === 'departure') {
            stationsList = $('#departureStationsList');
        } else if (type === 'arrival') {
            stationsList = $('#arrivalStationsList');
        }

        stationsList.empty();
        stations.forEach(function (station) {
            var option = $('<option>', {
                text: station.station
            }).click(function () {
                // Сохранение значения кода станции
                selectedStations[type][station.station] = station.code;

                setInputValue(station.station, type);
                hideList(type);
            });
            stationsList.append(option);
        });

        stationsList.show();
    }

    // Функция для получения кода станции по её названию
    function getCode(stationName, type) {
        return selectedStations[type][stationName];
    }


    function setInputValue(value, type) {
        if (type === 'departure') {
            $('#departureStationInput').val(value);
        } else if (type === 'arrival') {
            $('#arrivalStationInput').val(value);
        }
    }

    function showList(type) {
        var stationsList;
        if (type === 'departure') {
            stationsList = $('#departureStationsList');
        } else if (type === 'arrival') {
            stationsList = $('#arrivalStationsList');
        }

        stationsList.show();
    }

    function hideList(type) {
        var stationsList;
        if (type === 'departure') {
            stationsList = $('#departureStationsList');
        } else if (type === 'arrival') {
            stationsList = $('#arrivalStationsList');
        }

        stationsList.hide();
    }

    function getTrainRoutes() {
        var departureCode = getCode($('#departureStationInput').val(), 'departure');
        var arrivalCode = getCode($('#arrivalStationInput').val(), 'arrival');
        var departureDate = document.getElementById('departureDateInput').value;
        var returnDate = null; // Инициализация второй даты как null по умолчанию

        // Проверка состояния чекбокса "В обе стороны"
        if (document.getElementById('bothDirectionsCheckbox').checked) {
            returnDate = document.getElementById('arrivalDateInput').value;
        }

        var url = 'get_train_routes.php'; // Установка URL-адреса по умолчанию

        // Если вторая дата задана, меняем URL-адрес и добавляем в data
        if (returnDate) {
            url = 'return_routes.php';
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                departureCode: departureCode,
                arrivalCode: arrivalCode,
                departureDate: departureDate,
                returnDate: returnDate // Добавляем вторую дату в data
            },
            dataType: 'json',
            success: function (data) {
                displayTrainRoutes(data);
                console.log(data);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }


    function displayTrainRoutes(trainRoutes) {
        var tableBody = $('#trainRoutesTable tbody');
        var tableBody1 = $('#returntrainRoutesTable tbody');
        tableBody1.empty();
        tableBody.empty();
        if (document.getElementById('bothDirectionsCheckbox').checked) {
            document.getElementById('returntrainRoutesTable').style.display = 'block';
            trainRoutes.forward.forEach(function (forward) {
                var row = $('<tr>').append(
                    $('<td>').text(forward.number),
                    $('<td>').append(
                        $('<div>').html('<span class="bold-text">' + forward.station0 + '</span>'),
                        $('<div>').html('<span class="red-text">' + forward.time0 + '</span>'),
                        $('<div>').text(forward.date0)
                    ),
                    $('<td>').text(forward.timeInWay),
                    $('<td>').append(
                        $('<div>').html('<span class="bold-text">' + forward.station1 + '</span>'),
                        $('<div>').html('<span class="red-text">' + forward.time1 + '</span>'),
                        $('<div>').text(forward.date1)
                    )
                );
                // Создаем элемент <td> для мест в поезде
                var carsData = $('<td>');
                var carsPrice = $('<td>');
                // Перебираем массив объектов cars и выводим информацию о доступных местах
                forward.cars.forEach(function (car) {
                    carsData.append($('<div>').text(car.type + ': ' + car.freeSeats));
                    carsPrice.append($('<div>').html('<span class="red-text">' + car.tariff + ' РУБ.' + '</span>'));
                });
                // Создание ячейки для кнопки "Маршрут" и добавление кнопки в эту ячейку
                var routeButtonCell = $('<td>');
                var routeButton = $('<button>')
                    .text('Маршрут')
                    .click(function () {
                        // При клике на кнопку "Маршрут" переходим на страницу station_list.php с номером поезда
                        window.location.href = 'station_list.php?trainNumber=' + forward.number + '&datet=' + document.getElementById('departureDateInput').value;
                    });

                routeButtonCell.append(routeButton);

                row.append(carsData, carsPrice);
                row.append(routeButtonCell);
                tableBody.append(row);
            });

            trainRoutes.back.forEach(function (back) {
                var row = $('<tr>').append(
                    $('<td>').text(back.number),
                    $('<td>').append(
                        $('<div>').html('<span class="bold-text">' + back.station0 + '</span>'),
                        $('<div>').html('<span class="red-text">' + back.time0 + '</span>'),
                        $('<div>').text(back.date0)
                    ),
                    $('<td>').text(back.timeInWay),
                    $('<td>').append(
                        $('<div>').html('<span class="bold-text">' + back.station1 + '</span>'),
                        $('<div>').html('<span class="red-text">' + back.time1 + '</span>'),
                        $('<div>').text(back.date1)
                    )
                );
                // Создаем элемент <td> для мест в поезде
                var carsData = $('<td>');
                var carsPrice = $('<td>');
                // Перебираем массив объектов cars и выводим информацию о доступных местах
                back.cars.forEach(function (car) {
                    carsData.append($('<div>').text(car.type + ': ' + car.freeSeats));
                    carsPrice.append($('<div>').html('<span class="red-text">' + car.tariff + ' РУБ.' + '</span>'));
                });
                // Создание ячейки для кнопки "Маршрут" и добавление кнопки в эту ячейку
                var routeButtonCell = $('<td>');
                var routeButton = $('<button>')
                    .text('Маршрут')
                    .click(function () {
                        // При клике на кнопку "Маршрут" переходим на страницу station_list.php с номером поезда
                        window.location.href = 'station_list.php?trainNumber=' + back.number + '&datet=' + document.getElementById('departureDateInput').value;
                    });

                routeButtonCell.append(routeButton);

                row.append(carsData, carsPrice);
                row.append(routeButtonCell);
                tableBody1.append(row);
            });
        }
        else {
            document.getElementById('returntrainRoutesTable').style.display = 'none';
            trainRoutes.forEach(function (train) {
                var row = $('<tr>').append(
                    $('<td>').text(train.number),
                    $('<td>').append(
                        $('<div>').html('<span class="bold-text">' + train.station0 + '</span>'),
                        $('<div>').html('<span class="red-text">' + train.time0 + '</span>'),
                        $('<div>').text(train.date0)
                    ),
                    $('<td>').text(train.timeInWay),
                    $('<td>').append(
                        $('<div>').html('<span class="bold-text">' + train.station1 + '</span>'),
                        $('<div>').html('<span class="red-text">' + train.time1 + '</span>'),
                        $('<div>').text(train.date1)
                    )
                );
                // Создаем элемент <td> для мест в поезде
                var carsData = $('<td>');
                var carsPrice = $('<td>');
                // Перебираем массив объектов cars и выводим информацию о доступных местах
                train.cars.forEach(function (car) {
                    carsData.append($('<div>').text(car.type + ': ' + car.freeSeats));
                    carsPrice.append($('<div>').html('<span class="red-text">' + car.tariff + ' РУБ.' + '</span>'));
                });
                // Создание ячейки для кнопки "Маршрут" и добавление кнопки в эту ячейку
                var routeButtonCell = $('<td>');
                var routeButton = $('<button>')
                    .text('Маршрут')
                    .click(function() {
                        // При клике на кнопку "Маршрут" переходим на страницу station_list.php с номером поезда
                        window.location.href = 'station_list.php?trainNumber=' + train.number + '&datet=' + document.getElementById('departureDateInput').value;
                    });

                routeButtonCell.append(routeButton);

                row.append(carsData,carsPrice);
                row.append(routeButtonCell);
                tableBody.append(row);
            });
        }
    }

</script>

</body>
</html>
