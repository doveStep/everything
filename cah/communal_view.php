<html>
    <head>
        <style>
            #wrapper {
                position:absolute;
                 background-image:url('wood.jpg');
                width:100%;
                height:100%
            }
            .playercards {
                border-radius: 15px;
                background:white;
                text-align:left;
                font-family:"Arial", Helvetica, sans-serif;
                font-size:30px;
                border:14px;
                text-color:black;
                top:10%;
                min-height:45%;
                margin:1px;
                padding:1px;
                border:1px;
                position:absolute;
                border:solid black 2px;
            }
            .playercards_text {
                margin:12px 3px 5px;
            }
            .playercards img {
                width:65%;
                height:auto;
                bottom:5px;
                left:15px;
                position:absolute;
            }
            #input_players {
                top: 5%;
                align:center;
                width:50%;
                position:absolute;
            }
            #turn_submit {
                text-color:black;
                width: 96%;
                left:1.75%;
                position:relative;
                height: 60px;
                font-size:30px;
                border-radius: 15px;
                background:white;
                border:solid black 2px;
            }
        </style>
    </head>
    <body>
        <form id='input_players' action="client_view.php" method="get">
            <input type="hidden" id="" name="game_state" value="new_game">
<?php       foreach (range(1, 7) as $p) {
                echo '<span class="inputs_left_col  player_inputs">Username: <input type="text" id="player_name_'.$p.'"  name="player_name_'.$p.'"  value=""></span>';
                echo '<span class="inputs_right_col player_inputs">Email:    <input type="text" id="player_email_'.$p.'" name="player_email_'.$p.'" value=""></span><br/>';
            }
?>          <input id='turn_submit' type="submit" value="submit">
        </form>
    </div>
    </body>
</html>
