/*TO INVOKE, BOOKMARK:
	javascript:
	var s1=document.createElement('script');
	s1.setAttribute('src','http://sean.hexault.com/clrooms/cbu.js');
	document.getElementsByTagName('body')[0].appendChild(s1);  
*/
/*
This html file doesn't actually go anywhere; copy/paste the script into a bookmark URL, go to 
'http://sfbay.craigslist.org/search/apa/sfc?zoomToPosting=&catAbb=apa&query=&minAsk=&maxAsk=&bedrooms=3&housing_type=&hasPic=1&excats=' and click it.
*/
//Used everywhere
var s=document.createElement('script');
s.setAttribute('src','http://code.jquery.com/jquery.js');
document.getElementsByTagName('body')[0].appendChild(s);
//Used for determining commute time.
var s=document.createElement('script');
s.setAttribute('src','https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false');
document.getElementsByTagName('body')[0].appendChild(s);
var s=document.createElement('script');
s.setAttribute('src','http://sean.hexault.com/clrooms/distance.js');
document.getElementsByTagName('body')[0].appendChild(s);

var too_much = 800;
var perfect = 700;	
var house_addy = '';

$( ".row" ).each(function(index, element){	
	text = element.innerHTML;
	var begindollar = text.indexOf("$");
	var enddollar = text.indexOf("<", begindollar);
	var beginbr = text.indexOf(" / ", enddollar);
	var endbr = text.indexOf(" - ", beginbr);
	
	var price = text.substring(begindollar + 1, enddollar);
	var brs = text.substring(beginbr + 2, endbr - 2);		
	var avg_cost_per_room = Math.floor(price / brs);
	color = (avg_cost_per_room <= perfect) ? 'red' : 'green';
	
	if (avg_cost_per_room <= too_much) {
		$( '<span style="margin-top:-33px; float:right; color:' + color + ';">$' + avg_cost_per_room + '</span>' ).insertAfter( this );
	} else {
		$(this).css({'display':'none'});
	}
	
});
