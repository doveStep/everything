 <!DOCTYPE html>
<?php
echo 'zort';
?>
<html>
    <span class='form_title'>
        Input player names:<br/>
    </span>
     <form name="input" action="cardsagainsthumanity.php" method="get">
        <? //Allow for 7 players.
        foreach (range(1, 7) as $k) { ?>
            <span class='playername'>Player Name:</span><input type="text" name="player<?=$k?>">     <span class='playeremail'>Email:</span><input type="text" name="email<?=$k?>"><br/>
        <? } ?>
        <input type="submit" value="Submit">
    </form>
</html>