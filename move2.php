<?php
require_once("engine/header.php");
$page_config = array ();
	$page_config['page_id'] = "move";
	$page_config['page_title'] = "Новостройки Петербурга - newcity";
	$page_config['map_element'] = "mapsMain";
	$page_config['map_element_zoom'] = 15;
	$page_config['content_width'] = 880;
	
if ($_GET['act']=="add") {
	if (isset($_GET['obj'], $_GET['xcoord'], $_GET['ycoord'])) {
		if ($_GET['obj']!='' && $_GET['xcoord']!='' && $_GET['ycoord']!='') {
			$dip ="INET_ATON(".$_SERVER['REMOTE_ADDR'].")";
			$dxcoord = mysql_real_escape_string($_GET['xcoord']);
			$dycoord = mysql_real_escape_string($_GET['ycoord']);
			$dstatus = mysql_real_escape_string($_GET['status']);
			$dobj = mysql_real_escape_string($_GET['obj']);
			$move_query = "INSERT INTO `metsyscom_main`.`dinamic_objects` (`id`, `coord`, `ip`, `status`, `ptime`, `obj`)
		VALUES (NULL , GeomFromText( 'POINT(".$dxcoord." ".$dycoord.")', 0 ) , '".$dip."', '".$dstatus."', NULL, '".$dobj."');";
			$move_result = mysql_query($move_query) or die (mysql_error());
		}
	}
	header('Location: move.php?act=done&obj='.$dobj);
	exit();
}
	
$obj = mysql_real_escape_string($_GET['obj']);
$move_query = "SELECT x(`coord`) as x,y(`coord`) as y, `ip`, `status` 
FROM `dinamic_objects` WHERE `obj`='$obj' 
ORDER BY `ptime` DESC, `id` DESC LIMIT 1000";

$move_result = mysql_query($move_query);
$move_arr = array();

while ($row = mysql_fetch_assoc($move_result)) {
	$move_arr[] = $row;
} 
if (!isset($move_arr[0])) {
	$centerx="59.99098";
	$centery="30.318752";
} else {
	$centerx=$move_arr[0]['x'];
	$centery=$move_arr[0]['y'];
	$main_point=$move_arr[0];
}

$query = "SELECT x(`coord`) as x,y(`coord`) as y, `name`, `desc` FROM `static_objects` LIMIT 1000";
$result = mysql_query($query);
$arr = array();
while ($row = mysql_fetch_assoc($result)) {
	$arr[] = $row;
}


/*
status {
 0 - start
 1 - online
 2 - offline
 4 - end }
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<head> 
<link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="style.css">
<title><?=$page_config['page_title'];?></title>
    <script src="http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU"
            type="text/javascript"></script>
			
	<script src="http://code.jquery.com/jquery-1.10.2.js" type="text/javascript"></script>
	<!--Собственно, сам скрипт спойлера-->
    <script language="JavaScript" type="text/javascript">
    function openClose(id1, id2)
    {
    var obj = "";

    // Check browser compatibility
    if(document.getElementById)
		obj1 = document.getElementById(id1).style;
    else if(document.all)
		obj1 = document.all[id1];
    else if(document.layers)
		obj1 = document.layers[id1];
    else
    return 1;
	
	if(document.getElementById)
		obj2 = document.getElementById(id2).style;
    else if(document.all)
		obj2 = document.all[id2];
    else if(document.layers)
		obj2 = document.layers[id2];
    else
    return 2;

    // Do the magic 
    if(obj1.display == "")
		obj1.display = "none";
    else if(obj1.display != "none")
		obj1.display = "none";
    else {
		obj1.display = "block";
		obj2.display = "none"; }
    }
    </script>
    <!--/Собственно, сам скрипт спойлера-->
    <script type="text/javascript">
        // Как только будет загружен API и готов DOM, выполняем инициализацию
        ymaps.ready(init);

        function init () {
		// создание кластеризатора
		// создадим карту, на которой необходимо кластеризовать геообъекты
		var mapapp = new ymaps.Map('<?=$page_config['map_element'];?>', {center: [<?=$centerx;?>, <?=$centery;?>], 
			zoom: <?=$page_config['map_element_zoom'];?>});
			
		// создадим массив геообъектов
		mapapp.controls
			// Кнопка изменения масштаба.
			.add('zoomControl', { left: 5, top: 5 })
			// Список типов карты
			.add('typeSelector')
			// Стандартный набор кнопок
			.add('mapTools', { left: 35, top: 5 });
		
		//map.behaviors.enable('scrollZoom');
		myGeoObjects = [];
		
		
		<?php
		$vid = 0;
		if (isset($main_point)) {
		?> 
		myGeoObjects[0] = new ymaps.GeoObject({
			geometry: {type: "Point", coordinates: [<?=$main_point['x'];?>, <?=$main_point['y'];?>]},
			properties: {
				clusterCaption: 'Последнее местоположение <?=$main_point['name'];?>',
				balloonContentBody: 'Последнее местоположение <?=$main_point['desc'];?>'
			}
		});
		<?php $vid++; } ?>
		<?php
		foreach ($arr as &$value) {
		?>
		myGeoObjects[<?=$vid;?>] = new ymaps.GeoObject({
			geometry: {type: "Point", coordinates: [<?=$value['x'];?>, <?=$value['y'];?>]},
			properties: {
				clusterCaption: '<?=$value['name'];?>',
				balloonContentBody: '<?=$value['desc'];?>'
			}
		});
		<?php $vid++; } ?>
		// создадим кластеризатор и запретим приближать карту при клике на кластеры
		clusterer = new ymaps.Clusterer({clusterDisableClickZoom: true});
		clusterer.add(myGeoObjects);
		mapapp.geoObjects.add(clusterer);

		
    mapapp.events.add('click', function (e) {
		
        if (!mapapp.balloon.isOpen()) {
		mapapp.setCenter(e.get('coordPosition'));	
            var coords = e.get('coordPosition');
            mapapp.balloon.open(coords, {
                contentHeader:'Координаты обновлены!',
                contentBody:'<p>Координаты щелчка: ' + [
                    coords[0].toPrecision(6),
                    coords[1].toPrecision(6)
                    ].join(', ') + '</p>',
                contentFooter:'<sup>Щелкните, чтобы выбрать другое место</sup>'
            });
		document.getElementById ('xcoord1').value = coords[0].toPrecision(6);
		document.getElementById ('ycoord1').value = coords[1].toPrecision(6);
		
		document.getElementById ('xcoord2').value = coords[0].toPrecision(6);
		document.getElementById ('ycoord2').value = coords[1].toPrecision(6);
		
		
        }
        else {
            mapapp.balloon.close();
        }
		
    });
	<?php if (isset($move_arr)) { ?>
			var geometry = [
			<?php
			$vid = 0;
			foreach ($move_arr as &$value) {
			?> 
			[<?=$value['x'];?>,<?=$value['y'];?>],
			<?php $vid++; } unset($value);?>
			],
 
			properties = {
				hintContent: "Маршрут"
			},
			options = {
				draggable: true,
				strokeColor: '#ff0000',
				strokeWidth: 5
			},
			polyline = new ymaps.Polyline(geometry, properties, options);
 
			mapapp.geoObjects.add(polyline);
	<?php } ?>

	}
    </script>
	<script type="text/javascript" src="//vk.com/js/api/openapi.js?95"></script>
	<script type="text/javascript">VK.init({apiId: 3647316, onlyWidgets: true});</script>


</head> 
<body id="top" class="page- ">
<div id="page"> 
<?php require_once ("engine/menu.php"); ?>
<section id="content-container">
	<div id="<?=$page_config['map_element'];?>" style="width:<?=$page_config['content_width'];?>px;height:600px"></div>
	<div class="home-infobox"> 
		<form action="move.php" method="GET">
		Идентификатор: <input type="text" name="obj" value="<?=$obj?>"/>
		Время: <input type="text" name="time_since" value=""/> по <input type="text" name="time_until" value="<?=date("d.m.Y H:i:s");?>"/> <?php $dt = date("d.m.Y H:i:s"); //$ts = strptime($dt,'%d.%m.%Y %H:%i:%s'); 
		//echo  $timestamp = DateTime::getTimestamp('%d.%m.%Y %H:%i:%s', $dt); ?>
		<input type="submit" value="Показать"/>
		</form></p>
			<div class="spoilertop">
			<a onClick="openClose('1', '2')">Добавить точку</a> / <a onClick="openClose('2', '1')">Добавить маршурт</a>
			</div>
			<div class="spoilerbox" id="1" style="display:none;">
			<form action="move.php?act=add" method="get">
			<input type="hidden" name="act" value="add"/>
			 <p><b>Объект:</b> <input type="text" name="obj" value="<?=$obj?>" /> <input type="submit" value="Добавить" /></p>
			 <p><b>Координаты точки</b>
				X: <input type="text" name="xcoord" id="xcoord1"/> Y: <input type="text" name="ycoord" id="ycoord1"/>
			</p>
			</form>
			</div>
			<div class="spoilerbox" id="2" style="display:none;">
			<form action="move.php?act=add" method="get">
			<input type="hidden" name="act" value="add"/>
			 <p><b>Объект:</b> <input type="text" name="obj" value="<?=$obj?>" /> <input type="submit" value="Добавить" /></p>
			 <p><b>Координаты маршура</b>
				X: <input type="text" name="xcoord" id="xcoord2"/> Y: <input type="text" name="ycoord" id="ycoord2"/>
			</p>
			</form>
			</div>
	</div>
	
	<!--<div id="vk_comments"></div>
	<script type="text/javascript">
	VK.Widgets.Comments("vk_comments", {limit: 15, width: "<?=$page_config['content_width'];?>", attach: false}, 
		"<?=$page_config['page_id'];?>");
	</script>-->
	
<div id="names">sad
</div>
<script>
$.getJSON('https://api.foursquare.com/v2/venues/search?ll=40.7,-74&query=mcdonalds&client_id=2POUFAUU4ZBJ2MTDOY3S2YHR2NIT52FYW0LUTPHBMNTJFJNQ&client_secret=YFDZI1YWV3ZI5S5SPM2DZJEQIEBPIDJ5XFZBWTIKIQZVQNYM&v=20120101',
    function(data) {
        $.each(data.response.venues, function(i,venues){
            content = '<p>' + venues.name + '</p>';
            $(content).appendTo("#names");
       });
});
</script>
</section> 
<?php require_once ("engine/footer.php"); ?>
</div> 
<div class="soc-fixed"> </div> 

</body> 
</html>