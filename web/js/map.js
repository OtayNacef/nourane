	var locations = [
	
[ "<div class='kode-event-list-2'><div class='kode-thumb'><a href='#'><img src='images/news-img1.png' alt=''></a></div><div class='kode-text'><h2>The anatomy of breast</h2><p class='title'>Wednesday Januaur 19th, 2015</p><a href='#' class='btn-filled'>Read more</a></div></div>" , 43.5132101,13.2302635],  
[ "<div class='kode-event-list-2'><div class='kode-thumb'><a href='#'><img src='images/news-img1.png' alt=''></a></div><div class='kode-text'><h2>The anatomy of breast</h2><p class='title'>Wednesday Januaur 19th, 2015</p><a href='#' class='btn-filled'>Read more</a></div></div>" , 42.5132101,100.2302635],  
[ "<div class='kode-event-list-2'><div class='kode-thumb'><a href='#'><img src='images/news-img1.png' alt=''></a></div><div class='kode-text'><h2>The anatomy of breast</h2><p class='title'>Wednesday Januaur 19th, 2015</p><a href='#' class='btn-filled'>Read more</a></div></div>" , 23.5132101,93.2302635],  
[ "<div class='kode-event-list-2'><div class='kode-thumb'><a href='#'><img src='images/news-img1.png' alt=''></a></div><div class='kode-text'><h2>The anatomy of breast</h2><p class='title'>Wednesday Januaur 19th, 2015</p><a href='#' class='btn-filled'>Read more</a></div></div>" , 15.5132101,35.2302635],
[ "<div class='kode-event-list-2'><div class='kode-thumb'><a href='#'><img src='images/news-img1.png' alt=''></a></div><div class='kode-text'><h2>The anatomy of breast</h2><p class='title'>Wednesday Januaur 19th, 2015</p><a href='#' class='btn-filled'>Read more</a></div></div>" , 13.5132101,123.2302635],
[ "<div class='kode-event-list-2'><div class='kode-thumb'><a href='#'><img src='images/news-img1.png' alt=''></a></div><div class='kode-text'><h2>The anatomy of breast</h2><p class='title'>Wednesday Januaur 19th, 2015</p><a href='#' class='btn-filled'>Read more</a></div></div>" , 12.5132101,77.2302635],
     	
	];
	// Setup the different icons and shadows
	var iconURLPrefix = 'images';
	var icons = [
				'images/pin1.png', 
				'images/pin1.png', 
				'images/pin1.png', 
				'images/map-icon-2.png', 
				'images/map-icon-2.png', 
				'images/map-icon-2.png', 
							
			];

	var icons_length = icons.length;
	var shadow = {
	  anchor: new google.maps.Point(16,16),
	  url: iconURLPrefix + 'msmarker.shadow.png'
	};

	var myOptions = {
	  center: new google.maps.LatLng(16,18),
	  mapTypeId: 'roadmap',
	  mapTypeControl: true,
	  streetViewControl: true,
	  panControl: true,
	  scrollwheel: false,
	  draggable: true,
	  
					  
	 styles: [{
			stylers: [{
				hue: '#000000'
			}, {
				saturation: -300                        }, {
				lightness: 1                        }]
		}],
		
					
	   zoom: 3,
	}
	
	var map = new google.maps.Map(document.getElementById("map_list"), myOptions);
	var infowindow = new google.maps.InfoWindow({
	  maxWidth: 350,
	});
	var marker;
	var markers = new Array();
	var iconCounter = 0;

	// Add the markers and infowindows to the map
	for (var i = 0; i < locations.length; i++) {  
	  marker = new google.maps.Marker({
		position: new google.maps.LatLng(locations[i][1], locations[i][2]),
		map: map,
		icon : icons[iconCounter],
		shadow: shadow
	  });

	  markers.push(marker);
	  google.maps.event.addListener(marker, 'click', (function(marker, i) {
		return function() {
		  infowindow.setContent(locations[i][0]);
		  infowindow.open(map, marker);
		}
	  })(marker, i));
	  
	  iconCounter++;
	  // We only have a limited number of possible icon colors, so we may have to restart the counter
	  if(iconCounter >= icons_length){
		iconCounter = 0;
	  }
	}