var saved_bg;
var saved_ele;
var index_quants = new Array();

generateIndex();
generateGrid();
init();

function init() {
	var x = document.getElementsByClassName('square');
	for (var i=0;i<x.length;i++) {
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
				
				
				color = getRandomColor();
				saved_ele.innerHTML = color;
				saved_ele.style.backgroundColor = color;
				this.innerHTML = color_string;
				this.style.backgroundColor = color_string;
               
                //Write to history what happened.
                writeChildHistory(saved_bg, bg, color_string, color); //color _1_ was dragged onto color _2_, combined to form _3_, _4_ replaced the consumed one.
                
                //Check to see if child is within winning distance of index.

				saved_bg = null;
				saved_ele = null;
                
			} else {
				saved_ele = ele;
				saved_bg = bg;
			}
		}
	}
}

function getRandomColor(solid_chance = 15) {
	var color = '#';
    rand = random(solid_chance);
    switch (rand) {
        case '1':
            return '#000000';
        case '2':
            return '#ffffff';
        case '3':
            return '#0000ff';
        case '4':
            return '#00ff00';
        case '5':
            return '#ff0000';
        default:
            while (color.length != 7) {
                color = '#' + random(16777215).toString(16);
            }
            return color;
    }
}

function generateIndex() {
	var index = document.getElementById('index_square');
	index.style.backgroundColor = getRandomColor(40);
	index.setAttribute("class", "bar");
}

function generateGrid() {
console.log('init generateGrid');
	for (h = 0; h < 5; h++) {
		for (w = 0; w < 4; w++) {
			generateNewSquare(w, h);
		}
	}
}

function generateNewSquare(w, h) {
	var new_div= document.createElement("div");
	terminator_node = document.getElementById('terminator_node');
	document.body.insertBefore(new_div, terminator_node);
	new_div.setAttribute("class", "square");
	var color = getRandomColor();
	new_div.innerHTML = color;
	new_div.style.backgroundColor = color;

}

function random(max = 1) { 
    return Math.floor(max * Math.random());
}

//color _1_ was dragged onto color _2_, combined to form _3_, _4_ replaced the consumed one.
function writeChildHistory(saved_bg, bg, color_string, color) {
    document.getElementById('child_history').innerHTML = 'You combined '+saved_bg+' with '+bg+' to form '+color_string+'!<br/>';
    console.log('You combined '+saved_bg+' with '+bg+' to form '+color_string+'!<br/>');
    document.getElementById('new_history').innerHTML = color+' took '+saved_bg+"'s place.<br/>";
}

function rgbToHex(R,G,B) {
    return toHex(R) + toHex(G) + toHex(B);
}
function toHex(n) {
    n = parseInt(n,10);
    if (isNaN(n)) {
        return "00";
    }
    n = Math.max(0,Math.min(n,255));
    return "0123456789ABCDEF".charAt((n - n % 16) / 16) + "0123456789ABCDEF".charAt(n % 16);
}