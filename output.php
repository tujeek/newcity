<?php require_once "engine/header.php";?>
            type="text/javascript"></script>

<script src="obj.js" type="text/javascript"></script>
ymaps.ready(init);

function init() {
    // Создание экземпляра карты.
    var map = new ymaps.Map('myMap', {
            center: [59.99098, 30.318752],
            zoom: 10,
        });	 
	myGeoObjects = []; 
	for (var i in groups) {
		var geop = [ groups[i].ll[1], groups[i].ll[0] ];
		myGeoObjects[i] = new ymaps.GeoObject({
			geometry: {type: "Point", coordinates: geop},
			properties: {
				iconContent: groups[i].name,
				clusterCaption: groups[i].name,
				balloonContentHeader: groups[i].name,
				//balloonContentBody: '<pre class="preElement">' + groups[i].description + '</pre>',
			},
		},{
		preset: "twirl#greyStretchyIcon",
        draggable: false	}
		);
    // Перебираем все группы.
	clusterer = new ymaps.Clusterer({clusterDisableClickZoom: true});
	clusterer.add(myGeoObjects);
	map.geoObjects.add(clusterer);
	map.controls
		.add('zoomControl', { left: 5, top: 5 })
		.add('typeSelector')
		.add('mapTools', { left: 35, top: 5 });
	map.behaviors.enable('scrollZoom');
};
	<div id="myMap" style="cursor: progress; width:800px; height:600px;"/>
</body>