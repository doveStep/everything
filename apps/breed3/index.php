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
			#adjacency {
				background:pink;
				height:200px;
				width:200px;
				font-size:400px;
			}
		</style>		
        <!--link rel="stylesheet" type="text/css" href="breed.css"-->
    </head>
    <body>
        <div id='hud'>
			<div id='index_square' class='index_square'></div>
		</div>
		<br/>
        <div id='selection_space'></div>
		<div id='terminator_node'></div>
		<div id='child_history'>
            You have not made any moves yet.
        </div>
		<div id='new_history'>
        </div>
		<div id='adjacency'>
			adjacency!
        </div>
    </body>
	<script src="breed.js"></script>
</html>
