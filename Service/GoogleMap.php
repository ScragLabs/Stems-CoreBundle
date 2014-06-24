<?php

namespace Stems\CoreBundle\Service;

// Google map object that uses the geocode api to create a custom location map using a postcode and country. Example usage:
//
//
// public function mapAction($height, $width, $zoom, $postcode, $country, $lat=null, $lng=null)
// {
//      // create the map object
//      $map = $this->get(Stems.core.googlemap)->create($height, $width, $zoom, $postcode, $country, $lat, $lng);
//
//      // embed the map
//      return $this->render('StemsPageBundle:Widget:map.html.twig', array(
//           'map'               => $map,
//      ));
// }
//
// {% render 'StemsPageBundle:Widget:map' with { 'height' : 300, 'width' : 400, 'zoom' : 12, 'postcode' : 'S10 5BW', 'country' : 'UK' } %}
//
// Lat & long overide can be used by passing in the extra paramters.
//
// Don't forget to get to include the API script with your associated key <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDg8H17NVSYHXRIObVhOG83ZtxoV2Z2M5s&sensor=true"></script>
//
//  ...and the map handler JS:
// 
// if($('#map-canvas').length) {
//      function initialize() {
//           var mapOptions = {
//             center: new google.maps.LatLng($('#google-lat').val(), $('#google-lng').val()),
//             zoom: parseInt($('#google-zoom').val()),
//             mapTypeId: google.maps.MapTypeId.ROADMAP,
//           };
//           var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

//           // add the marker
//           var location = new google.maps.LatLng($('#google-lat').val(), $('#google-lng').val());
//           var marker = new google.maps.Marker({
//              position: location,
//                   map: map,
//                   icon: $('#google-pin').val()
//          });

//           // marker click event
//           var url = 'https://maps.google.co.uk/maps?q='+$('#google-postcode').val();
//           google.maps.event.addListener(marker, 'click', function() {
//               window.open(url);
//           });

//          // add the size css
//          $('#map-canvas').css('height', $('#google-height').val()+'px');
//          $('#map-canvas').css('width', $('#google-width').val()+'px');
//     }
//      google.maps.event.addDomListener(window, 'load', initialize);
// }

class GoogleMap
{
	// The render height of the map
	protected $height = 400;

	// The render width of the map
	protected $width = 400;

	// The starting zoom level of the map
	protected $zoom = 10;

	// filepath of the map pin
	protected $pin = '/images/layout/icon-map-pin.png';

	// The location postcode
	protected $postcode;
    
	// The location country, defaulting to the united kingdom
	protected $country = 'UK';

	// Latitude override
	protected $lat;

	// Longditude override
	protected $lng;

	public function create($height, $width, $zoom, $postcode, $country, $lat=null, $lng=null)
	{
		// set the height, width and zoom level of the rendered map
		$this->height = $height;
		$this->width = $width;
		$this->zoom = $zoom;

		// convert the postcode and add country to force the right location
	    $this->postcode = str_replace(' ', '+', $postcode);
	    $this->postcode .= '+'.$country;
	   
	    // get the latlong for the address if no overrides are specified
	    if(!$lat && !$lng) {
		    $url = 'https://maps.googleapis.com/maps/api/geocode/xml?address='.$postcode.'&sensor=false&components=country:'.$country;
			$geocode = simplexml_load_file($url);
			$this->lat = $geocode->result->geometry->location->lat;
			$this->lng = $geocode->result->geometry->location->lng;
	    } else {
		    $this->lat = $lat;
			$this->lng = $lng;
	    }
	}

	public function getHeight()
	{
		return $this->height;
	}

	public function setHeight($height)
	{
		$this->height = $height;
	}

	public function getWidth()
	{
		return $this->width;
	}

	public function setWidth($width)
	{
		$this->width = $width;
	}

	public function getLat()
	{
		return $this->lat;
	}

	public function setLat($lat)
	{
		$this->lat = $lat;
	}

	public function getLng()
	{
		return $this->lng;
	}

	public function setLng($lng)
	{
		$this->lng = $lng;
	}

	public function getPin()
	{
		return $this->pin;
	}

	public function setPin($pin)
	{
		$this->pin = $pin;
	}

	public function getZoom()
	{
		return $this->zoom;
	}

	public function setZoom($zoom)
	{
		$this->zoom = $zoom;
	}

	public function getPostcode()
	{
		return $this->postcode;
	}

	public function setPostcode($postcode)
	{
		$this->postcode = $postcode;
	}

	public function getCountry()
	{
		return $this->country;
	}

	public function setCountry($country)
	{
		$this->country = $country;
	}
}

