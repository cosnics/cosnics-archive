var geocoder;
var map;

function initialize() 
{
	geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var myOptions = 
    {
      zoom: 12,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
}

function codeAddress(address) 
{
	geocoder.geocode( { address: address},
		function(results, status) 
		{
			if (status == google.maps.GeocoderStatus.OK && results.length) 
			{
				// You should always check that a result was returned, as it is
				// possible to return an empty results object.
				if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
					map.set_center(results[0].geometry.location);
					var marker = new google.maps.Marker({
						position: results[0].geometry.location,
						map: map
					});
				}
			} else 
			{
				alert("Geocode was unsuccessful due to: " + status);
			}
		}
	);
}

