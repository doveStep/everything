<?PHP
$searching = array_flip(array_keys(array_flip($argv)));
unset($searching['lca_2.php']);

if (count($searching) < 2) {
    echo "You must enter two args to find the common ancestor of.\n";
    return;
}

global $searching;
global $found;

init( $searching );

function init( $args ) {
    global $btree;
    //Binary tree: not guaranteed to be either sorted or balanced.
    global $searching;
    global $found;

    $btree = array('root' => array('100' => array('50' => array('18' => array('9', '27'), '36' => array('31', '41')), '75' => array('62' => array('56', '68'), '87' => array('81', '93'))), '150' => array('125' => array('112' => array('106', '118'), '137' => array('131', '143')), '175' => array('157' => array('151', '163'), '187' => array('181', '193')))));

    echo json_encode($btree) . "\n";
    $searching = $args;

    print_r($searching);
    echo "\n";

    //Find both values being searched for
    check_for_val($btree, NULL, $searching);

    compare_paths($found);
}

function check_for_val( $node, $path, $searching){
    global $found;

    echo "cur path = ".json_encode($path)."\n";
    $count = 0;
    foreach ($node as $val => $child) {
        if (is_scalar($child)) {
            echo "leaf found: = $child. address: ".json_encode($path)."\n";
        } else {
            $path[] = $val;
            if (check_for_val($child, $path, $searching) == TRUE) {
                return;
            }
            $child = $val;
        }
        if (array_key_exists($child, $searching)) {
            $found[$child] = $path;
            if (count($found) >= 2) {
                echo "node found: = $child. address: ".json_encode($path)."\n";
                echo "Second searcher discovered. Retrieving for Analysis.\n";
                return TRUE;
            }
        }
    }
    array_pop($path);
    return;
}

function compare_paths( $found ) {
    global $searching;
    global $btree;
    
    echo "comparing paths...\n";
    $keys = array_keys($found);
    
    print_r($found);
    
    $f1 = $found[$keys[0]];
    $f2 = $found[$keys[1]];
    $i = -1;
    
    if (!is_scalar($btree[
    
    foreach ($f1 as $k => $v) {
        $i++;
        echo "comparing ".$f1[$i]." & ".$f2[$i]."\n";
        if ($f1[$i] === $f2[$i]) {
            $last = $v;
            continue;
        }
        echo "The lowest common ancestor of ".implode(array_keys($searching), ' & ')." is: $last.\n";
        return;
    }
    echo "The lowest common ancestor of ".implode(array_keys($searching), ' & ')." is: $last.\n";
    return;
}