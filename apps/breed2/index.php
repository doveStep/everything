hi!
<?PHP
include 'breed.php';
session_start();
$spawn = new Squares();
$height = 5;
$width = 4;

function squareHTML($w, $h, $color, $index = FALSE) {
	$s = "<div id='square_".$w."_".$h."' class='index_".$index." square w_".$w." h_".$h."' style='background-color:".$color.";'>";
	// $s = "<div id='square_".$w."_".$h."' class='square w_".$w." h_".$h."' style='background-color:".$color.";'>boop</div>";
	return $s;
}
?>
<html>
    <head>
		<style>
			.square {
				position:relative;
				height:100px;
				width:100px;
				float:left;
				border:5px solid #ccc;
				margin:5px;
			}
		</style>		
        <!--link rel="stylesheet" type="text/css" href="breed.css"-->
        <!--script src="breed.js"></script-->
    </head>
    <body>
        <div id='hud'>
		<?PHP
			$square = new Squares();
			echo squareHTML('index', 'index', $square->color, TRUE);
			echo 'INDEX';
			echo '</div>';
		?>
		</div>
        <div id='selection_space'>
			<?PHP //Instantiate a new Square on every element of the grid.
			for ($h = 0; $h < $height; $h++) {
				for ($w = 0; $w < $width; $w++) {
					$square = new Squares();
					echo squareHTML($w, $h, $square->color);
					echo 'boop';
					echo '</div>';
				}
			}
			?>
		</div>
    </body>
		<script type = "text/javascript">
			var saved_bg;
			var saved_ele;

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
						
						bgarr = bgarr;
						savedarr = savedarr;
						
						console.log('this one is:');
						console.log(bgarr);
						console.log('last one is:');
						console.log(savedarr);
						for (i = 0; i < 3; i++) {
							//Convert from strings to ints
							bgarr[i] = parseInt(bgarr[i]);
							savedarr[i] = parseInt(savedarr[i]);
							
							console.log('constituents: '+bgarr[i]+', '+savedarr[i]);
							avg[i] = Math.floor((bgarr[i] + savedarr[i]) / 2);
							console.log('avg = ');
							console.log(avg[i]);
							//We're now back to an [R,G,B] array, convert back.
							color_string += avg[i].toString(16);
						}
						//Take the color string and set both selected values to those
						
						
						saved_ele.style.backgroundColor = color_string;
						this.style.backgroundColor = color_string;
						saved_bg = null;
						saved_ele = null;
					} else {
						saved_ele = ele;
						saved_bg = bg;
						console.log('just saved:');
						console.log(saved_ele);
						console.log(saved_bg);
					}
				}
			}
			

		</script>
</html>
