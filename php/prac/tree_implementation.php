<?PHP
global $searching;
global $found;
global $btree;

unset($argv['lca_2.php']);

init();

// print_r($btree);
echo "arg1 = $argv[1]\n";
switch ($argv[1]) {
    default:
        echo "\nValid arg1s: lca <node1> <node2>; insert <new val>\n";
        // return;
    case 'search':
        do_search($argv[2]);
        break;
    case 'add':
        do_add($argv[2]);
        break;
}

function init() {
    global $btree;

    $btree = [200 => [100 => [50 => [25 => [12,37], 75 => [62,88]], 150 => [125 => [112,137], 175 => [162,188]]], 300 => [ 250 => [225 => [212,237], 275 => [262,288]], 350 => [325 => [312,337], 375 => [362,388]]]]];
    
}

function do_search( $search ) {
    global $btree;
    //Binary tree: not guaranteed to be either sorted or balanced.
    global $found;

    echo "\n";
    
    if (!isset($search)) {
        return "No search parameter provided.\n";
    }

    //Find both values being searched for
    search( $search, array(200), $btree[200], 1);

    print_r($found);
    return $found;
    // compare_paths($found);
}

function do_add( $addition ) {
    global $btree;

    $ak = array_keys($btree);
    add( $ak[0], $addition );
}

function add( $parent, $addition ) {
    global $btree;
    
    if ($addition == $parent) {
        echo "Value '$addition' already exists in structure.\n";
        exit;
    }
    if (is_scalar($parent)) {
        if ($addition < $parent) {}
    }
    
    print_r($addition);
    echo "\n";
    print_r($parent);
    echo "\n";
    
    $c = getChildren( $parent );
    // if 
    
    
    // print_r($c[0]);
    // print_r($c[1]);
}

function getChildren( $parent ) {
    global $btree;
    
    $ak = array_keys($btree);
    $ak2 = array_keys($btree[$ak[0]]);
    $node_children = $ak2;
    
    return $node_children;
}

function search( $search, $path, $current_node, $depth){
    global $found;

    // echo "-- cur path = ".json_encode($path)." == ".json_encode($current_node)."\n";
    foreach ($current_node as $val => $child) {
        
        echo "I am currently at ".json_encode($current_node).", path = ".json_encode($path).", depth = $depth\n";
    
        if (is_scalar($child)) {
            // echo "leaf found: = $child. address: ".json_encode($path)."\n";
// echo "at $child; looking for $search\n";
            if ($child == $search) {
                echo "$search found at ".json_encode($path)."\n";
                exit;
            }
        } else {
            $path[] = $val;
            if (search($search, $path, $child, $depth + 1) == TRUE) {
                array_pop($path);
                return;
            }
            $child = $val;
        }
        
        
        
        
        if ($child == $search) {
            $found[$child] = $path;
            return TRUE;
        }
    }
    array_pop($path);
    return;
}

function compare_paths( $found ) {
    global $search;
    global $btree;
    
    echo "comparing paths...\n";
    $keys = array_keys($found);
    
    print_r($found);
    
    $f1 = $found[$keys[0]];
    $f2 = $found[$keys[1]];
    $i = -1;
    
    // if (!is_scalar($btree[
    
    foreach ($f1 as $k => $v) {
        $i++;
        echo "comparing ".$f1[$i]." & ".$f2[$i]."\n";
        if ($f1[$i] === $f2[$i]) {
            $last = $v;
            continue;
        }
        echo "The lowest common ancestor of ".implode(array_keys($search), ' & ')." is: $last.\n";
        return;
    }
    echo "The lowest common ancestor of ".implode(array_keys($search), ' & ')." is: $last.\n";
    return;
}