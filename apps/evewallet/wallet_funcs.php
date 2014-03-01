<?PHP
	function wallet($key, $vcode) {
		//$key = 3138604;
		//$vcode = 'w0AZRdhnHkoNpX3uhjR6bSxSKAgaaqqd2qTT4nYa71bcn2XRYoReXXzt87jIwQce';
		$wallet_url = "https://api.eveonline.com/char/WalletJournal.csv.aspx?";
		$account_url = "https://api.eveonline.com/account/Characters.xml.aspx?";

		$character_id = getCharacterId( $account_url, $key, $vcode );
		
		$params = http_build_query(array('keyID' => $key,
			'vCode' => $vcode,
			'characterID' => $character_id)
		);
		
		$head = apiCall( $wallet_url . $params );
		
		$head = urlencode($head);
		
		$content = strpos($head, '%0D%0A%0D%0');
		$body = urldecode(substr($head, $content + 12));
		$key_end = strpos($body, 'taxAmount') + 10;
		
		//$body = preg_replace('/\n/sim','<br/>', substr($body, $key_end) );
		$body = preg_split("/((\r(?!\n))|((?<!\r)\n)|(\r\n))/", $body); 
		
		$key = array('txn_date','ref_id','ref_type','owner_name_1','owner_name_2','arg_name_1','amount','balance','reason','tax_receiver_id','tax_amount', 'hash');
		
		foreach ($body as $trash => $b) {
			$row = explode(',', $b);
			$sha = sha1($b);
			if ($sha === 'bea16876e53c1aa55e4358f965a956588412bafb') {
				//this is the hash for the column keys...
				continue;
			}
			$row[] = $sha;
			if (count($key) != count($row)) {
				//error_log('Mismatched array values: $keys = '.json_encode($key).' ('.count($key).') & $vals = '.json_encode($row).' ('.count($row).')');
				continue;
			} else {
				writeHistory( $key, $row );
			}
		}	
		$details['verb'] = 'select';
		$details['actor'] = 'eve_wallet_history';
		$details['where_aa'] = array('1' => '1');
		$db = new Db();
		$result = $db->cmd( $details );
		
		return array($key, $result);
	}
	
	function getCharacterId( $account_url, $key, $vcode ) {
		$params = http_build_query(array('keyID' => $key,
			'vCode' => $vcode)
		);
		$result = apiCall( $account_url  . $params);
		$start = 13 + strpos($result, 'characterID="');
		$end = strpos($result, '"', $start);
		return substr($result, $start, $end - $start);
	}
	
	function writeHistory( $key, $row ) {
		$details['verb'] = 'insert';
		$details['actor'] = 'eve_wallet_history';
		$details['relevant'] = array_combine($key, $row);
		$db = new Db();
		$db->cmd( $details );
	}
	
	function apiCall( $url ) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$head = curl_exec($ch);
		curl_close($ch);
		
		return $head;
	}
?>