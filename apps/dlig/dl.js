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

function downloadButton(url) {
	//Create a download button
	var dl = document.createElement('a');
	dl.setAttribute('id', 'downloadInstagramPic');
	dl.setAttribute('download', '');
	dl.setAttribute('href', url);
	dl.innerHTML = 'download this picture!';

	//Hide all of the default page stuff so they can download.
	$( ".root" ).css({'display':'none'});
	$( ".igDialogLayer" ).css({'display':'none'});
			
	document.getElementsByTagName('body')[0].appendChild( dl );
}

function backToContext() {
	$( ".root" ).css({'display':'inline'});
	$( ".igDialogLayer" ).css({'display':'inline'});
}

var url = getUrl();
downloadButton(url);

$( "#downloadInstagramPic" ).click(function() {
	backToContext();
});