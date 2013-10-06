<?php
require_once("engine/header.php");
if ($_GET['act']=="do") {
	if (isset($_POST['name'], $_POST['xcoord'], $_POST['ycoord'])) {
		if ($_POST['name']!='' && $_POST['xcoord']!='' && $_POST['ycoord']!='') {
			$aname = mysql_real_escape_string($_POST['name']);
			$axcoord = mysql_real_escape_string($_POST['xcoord']);
			$aycoord = mysql_real_escape_string($_POST['ycoord']);
			$adesc = mysql_real_escape_string($_POST['desc']);
			$query = "INSERT INTO `metsyscom_main`.`static_objects` (`id` , `coord` , `name` , `desc` )
		VALUES (NULL , GeomFromText( 'POINT(".$axcoord." ".$aycoord.")', 0 ) , '".$aname."', '".$adesc."');";
			$result = mysql_query($query);
		}
	}
	header('Location: index.php?act=done');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>yandex api test</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU"
            type="text/javascript"></script>
    <script type="text/javascript">
        // Как только будет загружен API и готов DOM, выполняем инициализацию
        ymaps.ready(init);

        function init () {
    myMap = new ymaps.Map("mapsID", {
        center: [59.9624, 30.3063], // Углич
        zoom: 11
    }, {
        balloonMaxWidth: 200
    });
	myMap.controls
				// Кнопка изменения масштаба.
				.add('zoomControl', { left: 5, top: 5 })
				// Список типов карты
				.add('typeSelector')
				// Стандартный набор кнопок
				.add('mapTools', { left: 35, top: 5 });

    // Обработка события, возникающего при щелчке
    // левой кнопкой мыши в любой точке карты.
    // При возникновении такого события откроем балун.
    myMap.events.add('click', function (e) {
        if (!myMap.balloon.isOpen()) {
            var coords = e.get('coordPosition');
            myMap.balloon.open(coords, {
                contentHeader:'Координаты обновлены!',
                contentBody:'<p>Координаты щелчка: ' + [
                    coords[0].toPrecision(6),
                    coords[1].toPrecision(6)
                    ].join(', ') + '</p>',
                contentFooter:'<sup>Щелкните, чтобы выбрать другое место</sup>'
            });
		document.getElementById ('xcoord').value = coords[0].toPrecision(6);
		document.getElementById ('ycoord').value = coords[1].toPrecision(6);
        }
        else {
            myMap.balloon.close();
        }
    });

    // Обработка события, возникающего при щелчке
    // правой кнопки мыши в любой точке карты.
    // При возникновении такого события покажем всплывающую подсказку
    // в точке щелчка.
    myMap.events.add('contextmenu', function (e) {
        myMap.hint.show(e.get('coordPosition'), 'Кто-то щелкнул правой кнопкой');
    });
			
        }
    </script>
</head>

<body>
<h1>Тестирование возможностей Yandex API</h1>
<?
//<div><a href="#add.php">Добавить объект</a></div>
?>
<form action="add.php?act=do" method="post">
 <p><b>Название:</b> <input type="text" name="name" /></p>
 <p><b>Описание:</b> <input type="text" name="desc" /></p>
 <p><b>Координаты</b>
	<p> X: <input type="text" name="xcoord" id="xcoord"/> Y: <input type="text" name="ycoord" id="ycoord"/></p>
	<p> </p>
</p>
<p><input type="submit" value="Добавить" /></p>
</form>
<div id="mapsID" style="width:800px;height:600px"></div>
<div><a href="index.php">Вернуться на главную</a></div>
</body>

</html>
