<?PHP
	require_once '../../common/orm.php';
	require_once 'wallet_funcs.php';
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="wallet.css">
	</head>
	<body>
<?PHP
	$key_id = (isset($_GET['keyID'])) ? $_GET['keyID'] : NULL;
	$vcode = (isset($_GET['vCode'])) ? $_GET['vCode'] : NULL;
	$page = (isset($_GET['page'])) ? $_GET['page'] : NULL;
	$needCredentials = FALSE;
	
	if (empty($key_id) || empty($vcode)) {
		$needCredentials = TRUE;
	} else {
		list($keys, $results, $character_name) = wallet($key_id, $vcode, $page);
	}
	if ($needCredentials === TRUE) { 
?>
		<form id="needcredentials">
			keyID: <input type="text" name="keyID"><br/>
			vCode: <input type="text" name="vCode"><br/>
			<input type="hidden" name="page" value="1"><br/>
			<input type="submit" value="submit">
		</form>
<?PHP
	} else {
?>
		<table id='wallethistory'>
			<tr>
				<?PHP 
				array_unshift($keys, 'row_id');
				array_pop($keys);
				$keys[] = 'last_altered';
				foreach ($keys as $trash => $k) {
					if ($k === 'hash') {
						continue;
					}
					echo '<th>'.$k.'</th>';
				}
				?>
			</tr>
			<?PHP foreach ($results['data'] as $trash => $r) {
				echo '<tr>';
				unset($r['key_id']);
				unset($r['hash']);
				foreach ($r as $trash => $td) {
					if (is_numeric($td)) {
						$td = number_format($td);
					}
					if (strtolower(trim($td)) == strtolower(trim($character_name))) {
						$td = '<span class="redtext">'.ucfirst($td).'</span>';
					}
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
	