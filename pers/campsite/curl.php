<?PHP

while (TRUE) {

            $url = "http://www.reserveamerica.com/campsiteCalendar.do?page=matrix&calarvdate=05/30/2014&contractCode=CA&parkId=120063";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $head = curl_exec($ch);
            curl_close($ch);
	    if (strpos($head, "<td class='status r sat' >A") !== FALSE) {
		    file_put_contents('avail_sat__'.time().'.html', $head);
	    }
	    for($i = 0; $i <= 5; $i++) {
            	echo "$i\n";
		sleep(1);
	    }

}
