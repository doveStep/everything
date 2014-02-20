/*TO INVOKE, BOOKMARK:
	javascript:
	var s1=document.createElement('script');
	s1.setAttribute('src','http://sean.hexault.com/dlig/dl.js');
	document.getElementsByTagName('body')[0].appendChild(s1);
*/

// var s=document.createElement('script');
// s.setAttribute('src','http://code.jquery.com/jquery.js');
// document.getElementsByTagName('body')[0].appendChild(s);

function getUrl() {
	var element = $( "div.Image.iLoaded.iWithTransition.Frame")[0];

	var style = element.getAttribute('style');

	var begin_url = style.indexOf("background-image: url(");
	var end_url = style.indexOf(')', begin_url);

	var url = style.substring(begin_url + 22, end_url);
	
	return url;
}

function download(url) {
	$("body").append("<a download id='downloadInstagramPic' href='" + url + "'></a>");
	var button = $("#downloadInstagramPic")[0];
	button.click();
	$( "#downloadInstagramPic" ).remove();
}

var url = getUrl();
download(url);