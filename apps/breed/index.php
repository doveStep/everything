hi!
<?PHP
include 'breed.php';

session_start();
echo 'hello1'."<br/>";
$spawn = new Spawn();
echo 'hello2'."<br/>";
print_r($spawn);
?>
<html>
    <head>
        <!--link rel="stylesheet" type="text/css" href="breed.css"-->
        <!--script src="breed.js"></script-->
    </head>
    <body>
        <div id='hud'>beep</div>
        <div id='selection_space'>boop</div>
    </body>
</html>
