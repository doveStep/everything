<?PHP
	require_once '../../common/orm.php';
	require_once 'wallet_funcs.php';
?>
<html>
	<head>
		<style>
			table,
			td,
			th {
				border:1px solid green;
				text-align:center;
				padding:2px;
			}
			th {
				background-color:green;
				color:white;
			}
		</style>	
	</head>
	<body>
<?PHP
	$key = (isset($_GET['keyID'])) ? $_GET['keyID'] : NULL;
	$vcode = (isset($_GET['vCode'])) ? $_GET['vCode'] : NULL;
	$needCredentials = FALSE;
	
	if (empty($key) || empty($vcode)) {
		$needCredentials = TRUE;
	} else {
		list($key, $results) = wallet($key, $vcode);
	}
	
	if ($needCredentials === TRUE) { 
?>
		<form id="needcredentials">
			keyID: <input type="text" name="keyID"><br/>
			vCode: <input type="text" name="vCode"><br/>
			<input type="submit" value="submit">
		</form>
<?PHP
	} else {
?>
		<table id='wallethistory'>
			<tr>
				<?PHP 
				
				array_unshift($key, 'row_id');
				array_pop($key);
				$key[] = 'last_altered';
				$key[] = 'hash';
				foreach ($key as $trash => $k) {
					echo '<th>'.$k.'</th>';
				} 
				?>
			</tr>
			<?PHP foreach ($results['data'] as $trash => $r) {
				echo '<tr>';
				foreach ($r as $trash => $td) {
					echo '<td>'.$td.'</td>';
				}
				echo '</tr>';
			}
			?>
		</table>
<?PHP
	}
?>
	</body>
</html>
	