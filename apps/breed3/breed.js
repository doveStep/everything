var saved_bg;
var saved_ele;
var index_bg = '';
var index_quants = new Array();
var color_rand = 16777215;
var win_similarity = 135;
var turns = 0;

generateIndex();
generateGrid();
init();

function init() {
	var x = document.getElementsByClassName('square');
	var index = document.getElementById('index_square');
	for (var i=0;i<x.length;i++) {
		if (x == index) {
			continue;
		}		
		x[i].onclick = function () {
			var bg = this.style.backgroundColor;
			var ele = this;
			
			bg = bg.replace('rgb(', '');
			bg = bg.replace(')', '');
			if (saved_bg) {
				var bgarr = bg.split(", ");
				var savedarr = saved_bg.split(", ");
				var avg = new Array(); 
				var color_string = '#';
								
				for (i = 0; i < 3; i++) {
					//Convert from strings to ints
					bgarr[i] = parseInt(bgarr[i]);
					savedarr[i] = parseInt(savedarr[i]);
					
					avg[i] = Math.floor((bgarr[i] + savedarr[i]) / 2);
					//We're now back to an [R,G,B] array, convert back.
					color_string += avg[i].toString(16);
				}
				//Take the color string and set both selected values to those
				
				
				color = getRandomColor(15, 'grid');
				saved_ele.innerHTML = color;
				toggleSelectedBorder(saved_ele, 'off');
				saved_ele.style.backgroundColor = color;
				this.innerHTML = color_string;
				this.style.backgroundColor = color_string;
				
				$( ".square" ).wrapInner( "<img class='cat_png' src='cat.png' />");
				turns++;
			   
                //Write to history what happened.
                writeChildHistory(saved_bg, bg, color_string, color); //color _1_ was dragged onto color _2_, combined to form _3_, _4_ replaced the consumed one.
                
                //Check to see if child is within winning distance of index.
				checkvictory_adjacency(color_string);
					
				saved_bg = null;
				saved_ele = null;
			} else {
				toggleSelectedBorder(ele, 'on');
				saved_ele = ele;
				saved_bg = bg;
			}
		}
	}
}

function getRandomColor(solid_chance, location) {
	if (isNaN(solid_chance)) {
		solid_chance = 15;
	}
	var color = '#';
	if (location == 'grid') {
		rand = random(solid_chance);
	} else {
		rand = 0;
	}
console.log('getRandomColor case: ' + rand);
    switch (rand) {
        case 1:
            color = '#000000';
			break;
        case 2:
            color = '#ffffff';
			break;
        case 3:
            color = '#0000ff';
			break;
        case 4:
            color = '#00ff00';
			break;
        case 5:
            color = '#ff0000';
			break;
        default:
            while (color.length != 7) {
                color = '#' + random(color_rand).toString(16);
            }
			break;
    }
console.log('color = ' + color);
	return color;
}

function generateIndex() {
	var index = document.getElementById('index_square');
	index_bg = getRandomColor(40, 'index');
console.log('index_bg = '+index_bg);
	index.style.backgroundColor = index_bg;
	$( "#index_square" ).wrapInner( "<img class='cat_png' src='cat.png' />");
}

function generateGrid() {
console.log('init generateGrid');
	for (h = 0; h < 6; h++) {
		for (w = 0; w < 4; w++) {
			// if (w == 1) {
				// var new_br = document.createElement("br");
				// document.body.insertBefore(new_br, last_square);
			// }
			var last_square = generateNewSquare(w, h);
		}
	}
	$( ".square" ).wrapInner( "<img class='cat_png' src='cat.png' /> <img class='cat_stripes' src='cat_stripes.png' />");
	// $( ".square" ).wrapInner( "<img class='cat_stripes' src='cat_stripes.png' />");
}

function generateNewSquare(w, h) {
	var new_div = document.createElement("div");
	var new_cat = document.createElement("img");
	
	terminator_node = document.getElementById('terminator_node');
	document.body.insertBefore(new_div, terminator_node);
	
	new_div.setAttribute("class", "square");
	var color = getRandomColor(15, 'grid');
	new_div.innerHTML = color;
	new_div.style.backgroundColor = color;

	new_cat.setAttribute("src", "cat.png");
	
	return new_div;
}

function random(max) {
	if (isNaN(max)) {
		max = 1;
	}
	var random = Math.floor(max * Math.random());
console.log('random = ' + random);
    return random;
}

//color _1_ was dragged onto color _2_, combined to form _3_, _4_ replaced the consumed one.
function writeChildHistory(saved_bg, bg, color_string, color) {
	saved_bg = rgbdecHex(saved_bg);
	bg = rgbdecHex(bg);
    //document.getElementById('child_history').innerHTML = 'You combined '+saved_bg+' with '+bg+' to form '+color_string+'!<br/>';
    //document.getElementById('new_history').innerHTML = color+' took '+saved_bg+"'s place.<br/>";
}

function rgbdecHex(rgb_str) {
	var color = '#';
	
	rgb_str = rgb_str.replace('rgb(', '');
	rgb_str = rgb_str.replace(')', '');
	var rgb_arr = rgb_str.split(", ");

	for (i = 0; i < 3; i++) {
		color += decHex(rgb_arr[i]);
	}
	return color;
}

function decHex(n) {
    n = parseInt(n,10);
    if (isNaN(n)) {
        return "00";
    }
    n = Math.max(0,Math.min(n,255));
    return "0123456789abcdef".charAt((n - n % 16) / 16) + "0123456789abcdef".charAt(n % 16);
}

function hexDec(n) {
	return parseInt(n, 16);
}

function checkvictory_adjacency(hex) {
	var rgbs = ['Red', 'Green', 'Blue'];
	var victory_adjacency = '';
	var sum_off = 0;
	for (i = 0; i < 3; i++) {
		var hex_diff = hexDec(hex.substring(1 + (2*i), 3 + (2*i)));
		var index_diff = hexDec(index_bg.substring(1 + (2*i), 3 + (2*i)));
		
		var off = (hex_diff - index_diff).toFixed(0);
		if (off == -255) {
console.log('off was -255, is now 0');
			off = 0;
		}
		victory_adjacency += '<span style="color:'+rgbs[i]+'">'+rgbs[i] + ' is off by ' + off + '.</span>&nbsp;';
		sum_off += Math.abs(parseInt(off));
	}
	sum_off -= win_similarity;
	
	victory_adjacency += '<br/>';
	if (sum_off <= 0) {
		victory_adjacency += 'AND YOU WIN! ( ' + turns + ' turns)';
	} else {
		victory_adjacency += ' (' + sum_off + ' away from victory)';
	}
	
	console.log('victory_adjacency = ' + victory_adjacency);
	document.getElementById('victory_adjacency').innerHTML = victory_adjacency;
}

function toggleSelectedBorder(element, on_or_off) {
	switch (on_or_off) {
		case 'on':
			element.style.border = '5px solid orange';
			break;
		case 'off':
			element.style.border = '5px solid #CCC';
			break;
	}
}