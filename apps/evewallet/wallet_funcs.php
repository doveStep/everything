<?PHP
	function wallet($key_id, $vcode, $page) {
		$page_size = 30;
	
		//$key_id = 3138604;
		//$vcode = 'w0AZRdhnHkoNpX3uhjR6bSxSKAgaaqqd2qTT4nYa71bcn2XRYoReXXzt87jIwQce';
		$wallet_url = "https://api.eveonline.com/char/WalletJournal.csv.aspx?";
		$account_url = "https://api.eveonline.com/account/Characters.xml.aspx?";

		list($character_id, $character_name) = getCharacterId( $account_url, $key_id, $vcode );
		
		$params = http_build_query(array('keyID' => $key_id,
			'vCode' => $vcode,
			'characterID' => $character_id)
		);
		
		$head = apiCall( $wallet_url . $params );
		$head = removeNewlines( $head );
		
		//$body = preg_replace('/\n/sim','<br/>', substr($body, $key_end) );
		$body = preg_split("/((\r(?!\n))|((?<!\r)\n)|(\r\n))/", $body); 
		
		$keys = array('txn_date','ref_id','ref_type','owner_name_1','owner_name_2','arg_name_1','amount','balance','reason','tax_receiver_id','tax_amount','hash','key_id');
		
		foreach ($body as $trash => $b) {
			$row = explode(',', $b);
			$sha = sha1($b);
			if ($sha === 'bea16876e53c1aa55e4358f965a956588412bafb') {
				//this is the hash for the column keys...
				continue;
			}
			$row[] = $sha;
			$row[] = $key_id;
			if (count($keys) != count($row)) {
				//error_log('Mismatched array values: $keys = '.json_encode($keys).' ('.count($keys).') & $vals = '.json_encode($row).' ('.count($row).')');
				continue;
			} else {
				writeHistory( $keys, $row );
			}
		}	
		$details['verb'] = 'select';
		$details['actor'] = 'eve_wallet_history';
		$details['where_aa'] = array('1' => '1');
		$details['limit'] = $page_size;
		$details['offset'] = $page_size * $page;
		$db = new Db();
		$result = $db->cmd( $details );
		
		return array($keys, $result, $character_name);
	}
	
	function removeNewlines( $head ) {
		$head = urlencode($head);
		$content = strpos($head, '%0D%0A%0D%0');
		$body = urldecode(substr($head, $content + 12));	
	}
	
	function getCharacterId( $account_url, $key, $vcode ) {
		//Build out the API query
		$params = http_build_query(array('keyID' => $key,
			'vCode' => $vcode)
		);
		//Make the API call, pinging api.eve-online
		$result = apiCall( $account_url  . $params);
		//Get the character name
		$start_cn = strpos($result, '<row name="') + 11;
		$end_cn = strpos($result, ' characterID=') - 1;
		$cn = substr($result, $start_cn, $end_cn - $start_cn);		
		//Get the next field, the character ID.
		$start_cid = 15 + $end_cn;
		$end_cid = strpos($result, '"', $start_cid);
		$cid = substr($result, $start_cid, $end_cid - $start_cid);		
		return array($cid, $cn);
	}
	
	//Make the DB call to write what we obtained; the db takes care of redundancies
	//TODO: keep a record of the last entry written, and continue from there (or 1 second previous or whatever)
	function writeHistory( $key, $row ) {
		$details['verb'] = 'insert';
		$details['actor'] = 'eve_wallet_history';
		$details['relevant'] = array_combine($key, $row);
		$db = new Db();
		$db->cmd( $details );
	}
	
	//Performs the actual curl.
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