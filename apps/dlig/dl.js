/*TO INVOKE, BOOKMARK:
	javascript:
	var s1=document.createElement('script');
	s1.setAttribute('src','http://sean.hexault.com/dlig/dl.js');
	document.getElementsByTagName('body')[0].appendChild(s1);
*/

var domain_info = {"base_element":"", "begin_index":"", "end_index":""};

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
			window.alert('Downloads not supported for this domain: ' + document.domain);
			//Die!
			throw '';
		case 'instagram.com':
			var style = $( "div.Image.iLoaded.iWithTransition.Frame")[0].getAttribute('style');
			var begin_url = style.indexOf("background-image: url(");
			var end_url = style.indexOf(')', begin_url);
			return style.substring(begin_url + 22, end_url);
		case 'www.flickr.com':
			loadJquery();
			return $( "#lowres-photo" )[0].src;
	}
}

//This is where the (easy, trivial, dumb) magic happens; an <a> element with a 'download' attribute can pop a download prompt and then jquery can click it.
function download(url) {
	$("body").append("<a download id='downloadInstagramPic' href='" + url + "'></a>");
	var button = $("#downloadInstagramPic")[0];
	button.click();
	$( "#downloadInstagramPic" ).remove();
}

var url = getUrl();
download(url);