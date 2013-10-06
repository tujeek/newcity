<?php require_once "engine/header.php";?><?php require_once "engine/config.php";?><!DOCTYPE html PUBLIC"-//W3C//DTD HTML 4.01 Transitional//EN""http://www.w3.org/TR/html4/loose.dtd"> <html><head><title>Инкубатор - Волшебная карта новостроек</title><script src="http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU"
            type="text/javascript"></script>

<script src="obj.js" type="text/javascript"></script><script src="http://code.jquery.com/jquery-1.10.2.js" type="text/javascript"></script><script type="text/javascript">
ymaps.ready(init);

function init() {
    // Создание экземпляра карты.
    var map = new ymaps.Map('myMap', {
            center: [59.99098, 30.318752],
            zoom: 10,			type: 'yandex#satellite'   
        });	 	map.container.fitToViewport();
	myGeoObjects = []; 	//map.setType(YMaps.MapType.SATELLITE); // ???
	for (var i in groups) {
		var geop = [ groups[i].ll[1], groups[i].ll[0] ];
		myGeoObjects[i] = new ymaps.GeoObject({
			geometry: {type: "Point", coordinates: geop},
			properties: {
				iconContent: groups[i].name,
				clusterCaption: groups[i].name,
				balloonContentHeader: groups[i].name,
				//balloonContentBody: '<pre class="preElement">' + groups[i].description + '</pre>',				balloonContentBody: '<div>' + groups[i].description + '</div>',                balloonContentFooter:'<sup>Щелкните, чтобы выбрать другое место</sup>'
			},
		},{
		preset: "twirl#greyStretchyIcon",
        draggable: false	}
		);	};
    // Перебираем все группы.
	clusterer = new ymaps.Clusterer({clusterDisableClickZoom: true});	clusterer.options.set('icons', ymaps.option.presetStorage.get('twirl#greyClusterIcons').clusterIcons);
	clusterer.add(myGeoObjects);
	map.geoObjects.add(clusterer);
	map.controls
		.add('zoomControl', { left: 5, top: 5 })
		.add('typeSelector')
		.add('mapTools', { left: 35, top: 5 });
	map.behaviors.enable('scrollZoom');			myPlacemark1=[];		// Круг счастья 	// circle1 = new ymaps.Circle([[59.9503, 30.2702], 5000], null, { draggable: true });	// map.geoObjects.add(circle1);		map.events.add('click', function (e) {		myPlacemark1.RemoveFromMap;		myPlacemark1=[];		map.setCenter(e.get('coordPosition'));		map.setZoom(16, {duration: 1000});			    		var R = 6372795;		var coords2 = e.get('coordPosition');		lat1=coords2[0].toPrecision(6);		long1=coords2[1].toPrecision(6);		lat2=59.9503;		long2=30.2702;		//перевод коордитат в радианы		lat1 *= Math.PI / 180;		lat2 *= Math.PI / 180;		long1 *= Math.PI / 180;		long2 *= Math.PI / 180;     		//вычисление косинусов и синусов широт и разницы долгот		var cl1 = Math.cos(lat1);		var cl2 = Math.cos(lat2);		var sl1 = Math.sin(lat1);		var sl2 = Math.sin(lat2);		var delta = long2 - long1;		var cdelta = Math.cos(delta);		var sdelta = Math.sin(delta);		 		//вычисления длины большого круга		var y = Math.sqrt(Math.pow(cl2 * sdelta, 2) + Math.pow(cl1 * sl2 - sl1 * cl2 * cdelta, 2));		var x = sl1 * sl2 + cl1 * cl2 * cdelta;		var ad = Math.atan2(y, x);		var dist = ad * R/8+200; //расстояние между двумя координатами в метрах			$.getJSON('https://api.foursquare.com/v2/venues/<?=$config['search_type'];?>?ll='+e.get('coordPosition')+'&radius='+dist+'&limit=40&client_id=2POUFAUU4ZBJ2MTDOY3S2YHR2NIT52FYW0LUTPHBMNTJFJNQ&client_secret=YFDZI1YWV3ZI5S5SPM2DZJEQIEBPIDJ5XFZBWTIKIQZVQNYM&v=20120101',					function(data) {		    $.each(data.response.venues, function(i,venues){			var texty= '<b>' + venues.name + '</b><br/>\n'+venues.location.distance+' м.';			//if (complex!=''){			if ((venues.location.distance<10000)&&(venues.location.distance>=2500)) 				texty=texty+'\nПо пробкам долговато';			if ((venues.location.distance<2500)&&(venues.location.distance>=500)) 				texty=texty+'\nЛегко доехать на машине';			if ((venues.location.distance<500)&&(venues.location.distance>=100)) 				texty=texty+'\nПешеходная доступность';			if (venues.location.distance<100) texty=texty+'\nТри шага и на месте';//}			 myPlacemark1[i] = new ymaps.Placemark([venues.location.lat, venues.location.lng], {				// Свойства.				// Содержимое иконки, балуна и хинта.				iconContent: '',//venues.categories[0].name,				balloonContent: texty + ' (<a href="#plus">+</a>/<a href="#minus">-</a>)',				hintContent: venues.categories[0].name			}, {				// Опции.				// Стандартная фиолетовая иконка.								iconImageHref: venues.categories[0].icon.prefix+'32.png',				iconSize:[32,32]				//preset:  'twirl#blueStretchyIcon'			})		if (venues.categories[0].icon.prefix+'32.png'!='https://foursquare.com/img/categories/building/default_32.png') 			map.geoObjects.add(myPlacemark1[i]);		        });		});    });
};</script></head><body>
	<div id="myMap" style="cursor: progress; width:800px; height:600px;"/>
</body></html>