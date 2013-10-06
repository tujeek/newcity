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
    ymaps.ready(init);

function init() {
    // Создание экземпляра карты.
    var map = new ymaps.Map('myMap', {
            center: [59.99098, 30.318752],
            zoom: 10,
			type: 'yandex#satellite'
        });	 
		map.container.fitToViewport();
	myGeoObjects = []; 
	//map.setType(YMaps.MapType.SATELLITE);

	for (var i in groups) {
	var geop = [ groups[i].ll[1], groups[i].ll[0] ];
	myGeoObjects[i] = new ymaps.GeoObject({
			geometry: {type: "Point", coordinates: geop},
			properties: {
				iconContent: groups[i].name,
				clusterCaption: groups[i].name,
				balloonContentHeader: groups[i].name,
				balloonContentBody: '<pre class="preElement">' + groups[i].description + '</pre>'
			},
		},{
		preset: "twirl#greyStretchyIcon",
        draggable: false	}
		);
	};
    // Перебираем все группы.
	clusterer = new ymaps.Clusterer({clusterDisableClickZoom: true});
	clusterer.options.set('icons', ymaps.option.presetStorage.get('twirl#greyClusterIcons').clusterIcons);
	clusterer.add(myGeoObjects);
	map.geoObjects.add(clusterer);
			map.controls
				.add('zoomControl', { left: 5, top: 5 })
				.add('typeSelector')
				.add('mapTools', { left: 35, top: 5 });
		map.behaviors.enable('scrollZoom');
		
		myPlacemark1=[];
			
	    map.events.add('click', function (e) {
		myPlacemark1.RemoveFromMap;
		myPlacemark1=[];
		map.setCenter(e.get('coordPosition'));
		map.setZoom(17);
	    
		 var R = 6372795;
     var coords2 = e.get('coordPosition');


	 lat1=coords2[0].toPrecision(6);
	 long1=coords2[1].toPrecision(6);
	 lat2=59.9503;
	 long2=30.3152;
    //перевод коордитат в радианы
    lat1 *= Math.PI / 180;
    lat2 *= Math.PI / 180;
    long1 *= Math.PI / 180;
    long2 *= Math.PI / 180;
     
    //вычисление косинусов и синусов широт и разницы долгот
    var cl1 = Math.cos(lat1);
    var cl2 = Math.cos(lat2);
    var sl1 = Math.sin(lat1);
    var sl2 = Math.sin(lat2);
    var delta = long2 - long1;
    var cdelta = Math.cos(delta);
    var sdelta = Math.sin(delta);
     
    //вычисления длины большого круга
    var y = Math.sqrt(Math.pow(cl2 * sdelta, 2) + Math.pow(cl1 * sl2 - sl1 * cl2 * cdelta, 2));
    var x = sl1 * sl2 + cl1 * cl2 * cdelta;
    var ad = Math.atan2(y, x);
    var dist = ad * R/8+400; //расстояние между двумя координатами в метрах

	
	
		$.getJSON('https://api.foursquare.com/v2/venues/<?=$config['search_type'];?>?ll='+e.get('coordPosition')+'&radius='+dist+'&limit=30&client_id=2POUFAUU4ZBJ2MTDOY3S2YHR2NIT52FYW0LUTPHBMNTJFJNQ&client_secret=YFDZI1YWV3ZI5S5SPM2DZJEQIEBPIDJ5XFZBWTIKIQZVQNYM&v=20120101',
			function(data) {
        $.each(data.response.venues, function(i,venues){
	
         myPlacemark1[i] = new ymaps.Placemark([venues.location.lat, venues.location.lng], {
            // Свойства.
            // Содержимое иконки, балуна и хинта.
            iconContent: '',//venues.categories[0].name,
            balloonContent: venues.name,
            hintContent: venues.categories[0].name
        }, {
            // Опции.
            // Стандартная фиолетовая иконка.
			
			iconImageHref: venues.categories[0].icon.prefix+'32.png',
            iconSize:[32,32]
            //preset:  'twirl#blueStretchyIcon'
        })
	 map.geoObjects.add(myPlacemark1[i]);   
	 
	/* clusterer2 = new ymaps.Clusterer({clusterDisableClickZoom: true});
	 clusterer.options.set({
    gridSize: 100,
    disableClickZoom: true,
	maxzoom:20
});
clusterer2.add(myPlacemark1);
map.geoObjects.add(clusterer2);*/
       });
});
		
    });
	
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