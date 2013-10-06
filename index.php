<?php
require_once ("engine/header.php");
require_once ("engine/config.php");
$page_config = array ();
	$page_config['page_id'] = "main";
	$page_config['page_title'] = "Новостройки Петербурга - newcity";
	$page_config['map_element'] = "mapsMain";
	$page_config['map_element_zoom'] = 10;
	$page_config['content_width'] = 880;
	$page_config['content_height'] = 550;
	
	
$query = "SELECT x(`coord`) as x,y(`coord`) as y, `name`, `desc` FROM `static_objects`";
$result = mysql_query($query);
$arr = array();
while ($row = mysql_fetch_assoc($result)) {
	$arr[] = $row;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<head> 
	<link href="bundle.css" media="screen" rel="stylesheet" type="text/css" />
    
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
	
	<script src="obj.js" type="text/javascript"></script>
	<script src="http://code.jquery.com/jquery-1.10.2.js" type="text/javascript"></script>
	
	<link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="style.css">
	<title><?=$page_config['page_title'];?></title>

	<script src="http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU"
            type="text/javascript"></script>
			
	<!--Собственно, сам скрипт спойлера-->
    <script language="JavaScript" type="text/javascript">
    function show(id1,descr)
    {
	desc = '<p>' + descr + '</p>';
	jQuery("#descriptionteg").empty();
    $(desc).appendTo("#descriptionteg");
    }
    </script>
    <!--/Собственно, сам скрипт спойлера-->
    <script type="text/javascript">
        // Как только будет загружен API и готов DOM, выполняем инициализацию
	ymaps.ready(init);
	function init() {
	
    // Создание экземпляра карты.
    var nsmap = new ymaps.Map(<?=$page_config['map_element'];?>, {
            center: [59.99098, 30.318752],
            zoom: 10,
			type: 'yandex#satellite'   
        });	 
	nsmap.container.fitToViewport();
	
	    // Создаем экземпляр класса ymaps.control.SearchControl
    var mySearchControl = new ymaps.control.SearchControl({
            // Заменяем стандартный провайдер данных (геокодер) нашим собственным.
            
            // Не будем показывать еще одну метку при выборе результата поиска,
            // т.к. метки коллекции myCollection уже добавлены на карту.
            noPlacemark: true,
            resultsPerPage: 5
        });

    // Добавляем контрол в верхний правый угол,
    nsmap.controls
        .add(mySearchControl, { right: 100, top: 5 })
        

	
	myGeoObjects = []; 
	//nsmap.setType(YMaps.MapType.SATELLITE); // ???

	for (var i in groups) {
		var geop = [ groups[i].ll[1], groups[i].ll[0] ];
		myGeoObjects[i] = new ymaps.GeoObject({
			geometry: {type: "Point", coordinates: geop},
			properties: {
				iconContent: groups[i].name,
				clusterCaption: groups[i].name,
				balloonContentHeader: groups[i].name,
				//balloonContentBody: '<pre class="preElement">' + groups[i].description + '</pre>',
				balloonContentBody: '<div>' + groups[i].description + '</div>',
                balloonContentFooter:'<sup>Щелкните, чтобы выбрать другое место</sup>'
			},
		},{
		preset: "twirl#greyStretchyIcon",
        draggable: false	}
		);
	};
	//myGeoObjects.BaloonOpen(openClose('1'));
    // Перебираем все группы.
	clusterer = new ymaps.Clusterer({clusterDisableClickZoom: true});
	clusterer.options.set('icons', ymaps.option.presetStorage.get('twirl#greyClusterIcons').clusterIcons);
	clusterer.add(myGeoObjects);
	nsmap.geoObjects.add(clusterer);
	nsmap.controls
		.add('zoomControl', { left: 5, top: 5 })
		.add('typeSelector')
		.add('mapTools', { left: 35, top: 5 });
	nsmap.behaviors.enable('scrollZoom');
		
	myPlacemark1=[];
	
	// Круг счастья 
	// circle1 = new ymaps.Circle([[59.9503, 30.2702], 5000], null, { draggable: true });
	// nsmap.geoObjects.add(circle1);
	
	nsmap.events.add('click', function (e) {
		myPlacemark1.RemoveFromMap;
		myPlacemark1=[];
		nsmap.setCenter(e.get('coordPosition'));
		 nsmap.setZoom(17, {duration: 1000});
		
		//YMaps.Events.observe(nsmap, nsmap.Events.BalloonOpen, show('1','2323'));
	    
		var R = 6372795;
		var coords2 = e.get('coordPosition');

		lat1=coords2[0].toPrecision(6);
		long1=coords2[1].toPrecision(6);
		lat2=59.9503;
		long2=30.2702;
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
		 
		//вычисления радиуса большого круга
		var y = Math.sqrt(Math.pow(cl2 * sdelta, 2) + Math.pow(cl1 * sl2 - sl1 * cl2 * cdelta, 2));
		var x = sl1 * sl2 + cl1 * cl2 * cdelta;
		var ad = Math.atan2(y, x);
		var dist = ad * R/8+200; //расстояние между двумя координатами в метрах
	
		$.getJSON('https://api.foursquare.com/v2/venues/<?=$config['search_type'];?>?ll='+e.get('coordPosition')+'&radius='+dist+'&limit=40&client_id=2POUFAUU4ZBJ2MTDOY3S2YHR2NIT52FYW0LUTPHBMNTJFJNQ&client_secret=YFDZI1YWV3ZI5S5SPM2DZJEQIEBPIDJ5XFZBWTIKIQZVQNYM&v=20120101',
			
		function(data) {
			 finaltext='';
		    $.each(data.response.venues, function(i,venues){

			var texty= '<b>' + venues.name + '</b><br/>\n'+venues.location.distance+' м.';
			//if (complex!=''){
			if ((venues.location.distance<10000)&&(venues.location.distance>=2500)) 
				texty=texty+'\nПо пробкам долговато<br/>';
			if ((venues.location.distance<2500)&&(venues.location.distance>=500)) 
				texty=texty+'\nЛегко доехать на машине<br/>';
			if ((venues.location.distance<500)&&(venues.location.distance>=100)) 
				texty=texty+'\nПешеходная доступность<br/>';
				if (venues.location.distance<100) texty=texty+'\nТри шага и на месте\n';
			//if (i<10) finaltext=finaltext+texty;
			//if (i==10) show('1',finaltext);
			//}
			 myPlacemark1[i] = new ymaps.Placemark([venues.location.lat, venues.location.lng], {
				// Свойства.
				// Содержимое иконки, балуна и хинта.
				iconContent: '',//venues.categories[0].name,
				balloonContent: 	texty + ' (<a href="#plus">+</a>/<a href="#minus">-</a>)',
				hintContent: venues.categories[0].name
			}, {
				// Опции.
				// Стандартная фиолетовая иконка.
				
				iconImageHref: venues.categories[0].icon.prefix+'32.png',
				iconSize:[32,32]
				//preset:  'twirl#blueStretchyIcon'
			})
		if (venues.categories[0].icon.prefix+'32.png'!='https://foursquare.com/img/categories/building/default_32.png') 
			nsmap.geoObjects.add(myPlacemark1[i]);
        });
		});
		
		$.getJSON('https://api.foursquare.com/v2/venues/trending?ll='+e.get('coordPosition')+'&radius=1800&limit=8&client_id=2POUFAUU4ZBJ2MTDOY3S2YHR2NIT52FYW0LUTPHBMNTJFJNQ&client_secret=YFDZI1YWV3ZI5S5SPM2DZJEQIEBPIDJ5XFZBWTIKIQZVQNYM&v=20120101',
			
		function(data) {
			 finaltext='';
		    $.each(data.response.venues, function(i,venues){

			var texty= '<b>' + venues.name + '</b><br/>\n'+venues.location.distance+' м.';
			//if (complex!=''){
			if ((venues.location.distance<10000)&&(venues.location.distance>=1500)) 
				texty=texty+'\nПо пробкам долговато<br/>';
			if ((venues.location.distance<1500)&&(venues.location.distance>=500)) 
				texty=texty+'\nЛегко доехать на машине<br/>';
			if ((venues.location.distance<500)&&(venues.location.distance>=100)) 
				texty=texty+'\nПешеходная доступность<br/>';
				if (venues.location.distance<100) texty=texty+'\nТри шага и на месте\n';
			finaltext=finaltext+texty;
			//if (i==0)show('1',texty);
			
        });
		show('1',finaltext+'');
		});
		
    });
	};
    </script>
	<script type="text/javascript" src="//vk.com/js/api/openapi.js?95"></script>
	<script type="text/javascript">VK.init({apiId: 3647316, onlyWidgets: true});</script>
</head> 
<body id="top" class="page- ">
<div id="page"> 
<?php require_once ("engine/menu.php"); ?>
	<!--<div id="vk_comments"></div>
	<script type="text/javascript">
	VK.Widgets.Comments("vk_comments", {limit: 15, width: "<?=$page_config['content_width'];?>", attach: false});
	</script>-->
<section id="content-container">

	<div id="<?=$page_config['map_element'];?>" style="margin:0px 0 0 0px;width:<?=$page_config['content_width'];?>px;height:<?=$page_config['content_height'];?>px"></div>
<div class="home-infobox" >

				<div id="descriptionteg" name="descriptionteg"></div>
	</div>
</section> 
<?php require_once ("engine/footer.php"); ?>
</div> 
<div class="soc-fixed"> </div> 
</body> 
</html>