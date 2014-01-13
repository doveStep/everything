<?PHP
//10) Sort the flight itinerary,  JFK - SFO, IAH - JFK, SFO- DLS, sort so all flights connect
$flights = array(array('JFK', 'SFO'), array('DLS', 'YYZ'), array('IAH', 'JFK'), array('SFO', 'DLS'));

$table = array();
foreach(range(1, 5) as $j) {
    foreach($flights as $i => $pairs) {
        // echo 'Do any flights in table '.json_encode($table).' end with '.$pairs[0]."?      >> for ".json_encode($pairs)."\n\n";
print_r($table);
        if ( ! array_key_exists($pairs[0], $table)) {
            $table[$pairs[1]] = $pairs;
            echo "nope! table is currently: ".json_encode($table)."\n\n";
            continue;
        }
        
        //Internal compare:
        foreach($table as $t => $itin) {
echo "is key ".$itin[0]." present in ".json_encode($table)."?\n";
            if (array_key_exists($itin[0], $table)) {
echo "___yes\n";
            }
        }
        
        
        
        // echo "yes! table is currently: ".json_encode($table)."\n\n";
        print_r($pairs[0]);
        echo "\n";
        $temp = $table[$pairs[0]];
    // echo __line__ . json_encode($temp)."\n";
        $temp[] = $pairs[1];
    // echo __line__ . json_encode($temp)."\n";
    // echo __line__ . json_encode($table)."\n";
        unset($table[$pairs[0]]);
    // echo __line__ . json_encode($table)."\n";
        $table[$pairs[1]] = $temp;
    // echo __line__ . json_encode($table)."\n";
    }
}
echo "\nend result:\n";
print_r($table);