function getCommuteTime(form) {
	console.log('begin:getCommuteTime');
	var work_addy = form.commute_address.value;
	house_addy = $('#map')[0].getAttribute('data-latitude') + ',' + $('#map')[0].getAttribute('data-longitude');
	calculateDistances(work_addy, house_addy);
	console.log('end:getCommuteTime');
}

function calculateDistances(work_addy, house_addy) {
var load = google.maps.Load;
console.log(load);
console.log(google);
  var service = new google.maps.DistanceMatrixService();
console.log('calculateDistances4');
  service.getDistanceMatrix(
    {
      origins: [house_addy],
      destinations: [work_addy],
      travelMode: google.maps.TravelMode.DRIVING,
      unitSystem: google.maps.UnitSystem.IMPERIAL,
      avoidHighways: false,
      avoidTolls: false
    }, googDistMatrixCallback);
console.log('calculateDistances5');
}

function googDistMatrixCallback(response, status) {
   var obj = response.rows[0].elements[0];
   console.log(obj.distance.text);
   console.log(obj.duration.text);

  if (status != google.maps.DistanceMatrixStatus.OK) {
    alert('Error was: ' + status);
  } else {
   console.log(obj.distance.text);
   console.log(obj.duration.text);
   console.log(response.destinationAddresses);
  }
  
  //var rackspace = "https://www.google.com/maps/dir/'"+ coords +"'/Rackspace,+620+Folsom+St+%23100,+San+Francisco,+CA+94107/";
  //$( '<a target="_blank" href="' + rackspace + '">(Rackspace)</a>' ).insertAfter( 'div.mapaddress' );  
}
	$( '<form id="commuteToWorkEstim" action="" method="get"><input type="text" name="commute_address" value=""/><input type="button" name="button" value="click" onclick="getCommuteTime(this.form)"></form>').insertAfter( 'div.mapaddress' );
