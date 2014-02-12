<html>
    <head>
		<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
		<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

		<style>
			.cat_png {
				height:100%;
				width:100%;
			}
			.square {
				position:relative;
				height:100px;
				width:100px;
				float:left;
				border:5px solid #CCC;
				margin:5px;
			}
		</style>
        <!--link rel="stylesheet" type="text/css" href="breed.css"-->
    </head>
    <body>
        <!--div id='container'-->
            <div id='hud'>
                <div id='index_square' class='index_square'></div>
            </div>
            <br/>
            <div id='selection_space'></div>
            <div id='terminator_node'></div>
            <div id='child_history'></div>
            <div id='new_history'></div>
            <div id='victory_adjacency'>Breed two cats together by clicking one, and then clicking another!</div>
        <!--/div-->
    </body>
	<script src="breed.js"></script>
</html>
