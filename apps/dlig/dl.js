/*TO INVOKE, BOOKMARK:
	javascript:
	var s1=document.createElement('script');
	s1.setAttribute('src','http://sean.hexault.com/dlig/dl.js');
	document.getElementsByTagName('body')[0].appendChild(s1);
*/
var credit = '';
var site = '';

//Flickr doesn't use jquery.
function loadJquery() {
	var s=document.createElement('script');
	s.setAttribute('src','http://code.jquery.com/jquery.js');
	document.getElementsByTagName('body')[0].appendChild(s);
}

//Different photo sites structure their download-blocking differently, so account for that on a per-site basis.
function getUrl() {
	switch (document.domain) {
		default:
			exception('Downloads not supported for this domain: ' + document.domain);
		case 'instagram.com':
			site = 'instagram';
			credit = $('.ibContent a')[0].href.substring(22);
			
			var style = $( "div.Image.iLoaded.iWithTransition.Frame")[0].getAttribute('style');
			var begin_url = style.indexOf("background-image: url(");
			var end_url = style.indexOf(')', begin_url);
			return style.substring(begin_url + 22, end_url);
		case 'www.flickr.com':
			site = 'flickr';
			loadJquery();
			credit = $('#all-sizes-header dl dd a')[0].href.substring(29).slice(0, -1);

			if ( $( "#lowres-photo" )
			&& $( "#lowres-photo" )[0]
			&& $( "#lowres-photo" )[0].src ) {
				return $( "#lowres-photo" )[0].src;
			} else if ( $( "#allsizes-photo img" ) 
			&& $( "#allsizes-photo img" )[0]
			&& $( "#allsizes-photo img" )[0].src) {
				return $( "#allsizes-photo img" )[0].src;
			} else {
				exception('No recognized photo available for Flickr!');
			}
		//By request, for a particularly lazy individual...
		case 'www.modelmayhem.com':
			site = 'modelmayhem';
			credit = $( '#album_name' )[0].innerHTML;
			return $('#viewpic img')[0].src;
		case 'facebook.com':
			site = 'facebook';
			if ( $("#fbPhotoImage")) {
				console.log( $("#fbPhotoImage" )[0].src);
			} else {
				//console.log( $(".stage.spotlight" )[0].src);
			}
	}
}

function exception( msg ) {
	window.alert( msg );
	throw '';
}

//This is where the (easy, trivial, dumb) magic happens; an <a> element with a 'download' attribute can pop a download prompt and then jquery can click it.
function download(url) {
	var orig_fn = url.substring(url.lastIndexOf('/') + 1);
	var fn = site + '_' + credit + '_' + orig_fn;	
	$("body").append("<a download="+ fn.replace(" ", "_") +" id='downloadInstagramPic' href='" + url + "'></a>");
	
	var button = $("#downloadInstagramPic")[0];
	button.click();
	$( "#downloadInstagramPic" ).remove();
}

var url = getUrl();
download(url);