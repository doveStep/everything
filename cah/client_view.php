<html>
    <head>
        <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
        <script>
            $(document).ready(function(){
                //
                $('.playercards').click(function(){
                    $(this).parent().find('.playercards').css('top','10%');
                    $(this).css('top','1%');
                    
                    $('#chosen_card').val(
                        $(this).attr('name')
                    );
                    // var link_name = $(this).attr('name');
                    // alert(link_name);
                    
                    
                });
                //
                $("#turn_submit_form").submit(function(){
                    var isFormValid = true;
                    var mia = '';
                    $("#turn_submit_form input").each(function(){
                        if ($.trim($(this).val()).length == 0){
                            isFormValid = false;
                            
                            // if ($(mia.length > 1)){
                            //      mia = mia + ', ';
                            // }
                            // mia = mia + $(this).attr('id');
                        }
                    });
                    // if (!isFormValid) alert("Missing fields required: " + mia);
                    if (!isFormValid) alert("Please touch a card to play.");
                    return isFormValid;
                });
            });
        </script>
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
            #turn_submit_form {
                top: 60%;
                align:center;
                width:100%;
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
                -webkit-transition: -webkit-box-shadow 0.5s ease-out, opacity 0.5s ease-out;
                -moz-transition: -moz-box-shadow 0.5s ease-out, opacity 0.5s ease-out;
                transition: box-shadow 0.1s ease-out, opacity 0.1s ease-out;
            }
            #turn_submit:hover {
                -webkit-box-shadow: 2px 12px 10px rgba(0,0,0,0.3);
                -moz-box-shadow: 2px 12px 10px  rgba(0,0,0,0.3);
                box-shadow: 2px 12px 10px rgba(0,0,0,0.3);
                opacity: 1;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
<?php
    // $player_id = $this->get('player_id');
    // $game = $this->get('game_id');
    // $player_id = 'a8158bf8a';
    // $game_id = 1515;

    require_once 'cardsagainsthumanity.php';
    
    $hand = array(
        'An honest cop with nothing left to lose.',
        'Famine.',
        'Flesh-eating bacteria.',
        'Flying sex snakes.',
        'Not giving a shit about the Third World.',
        'The World of Warcraft.',
        'Swooping.',
    );
    $numcards = sizeof($hand);
    $percentage = ceil(100 / $numcards) - 2;

    $wide = 0;
    foreach (range(1, $numcards) as $traversed) {
        $layout[$traversed] = array(
            'iter' => $traversed,
            'wide' => $wide,
            'dec' => $wide,
            'hex' => '#'.dechex($wide).dechex($wide).dechex($wide),
            'text' => $hand[$traversed - 1],
            'percentage' => $percentage,
        );
        $wide += $percentage;
    }
?>
    <div id='wrapper'>
<?php   foreach($layout as $k => $va) { ?>
            <div class='playercards' id='card<?php echo $k; ?>' name='card<?php echo $k; ?>' style='opacity:<?php echo $va['dec']; ?>%;  left:<?php echo $va['wide'] + $va['iter']; ?>%; width:<?php echo $va['percentage']; ?>%; z-index:<?php echo $va['iter']; ?>;'>
                <div class='playercards_text'>
                    <?php echo $va['text']; ?>
                    <img src='logo.png' />
                </div>
            </div>
<?php    
        }
?>      
        <form id='turn_submit_form' action="client_view.php" method="get">
            <input type="hidden" id='chosen_card' name="chosen_card" value="">
            <input type="hidden" name="game_id" value="<?php echo $game_id;?>">
            <input type="hidden" name="player_id" value="<?php echo $player_id;?>">
            <input id='turn_submit' type="submit" value="submit">
        </form>
    </div>
    </body>
</html>
