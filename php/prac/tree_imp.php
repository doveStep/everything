<?PHP
global $root;
global $opts;
global $flattened;

$opts['bst'] = TRUE;

$root = new Node(200, NULL);

#add block
$additions = array(250, 150, 175, 10, 1, 400, 201, 401, 601, 12, 8, 7);
foreach ($additions as $k => $v) {
    addNode($v);
}
#end add block

#traverse block
$node = $root;
$leftmost = NULL;
while ($leftmost == NULL) {
    if ($node->left != NULL) {
        $node = $node->left;
    } else {
        $leftmost = $node;
    }
}
puts("leftmost = ".$leftmost->value);

flatten($leftmost->value);
#end traverse block

    
print_r($root);
// print_r($argv);
/* try {
    if (!isset($argv[1])) {
        puts('No argument provided.');
        return;
    }
    switch($argv[1]) {
        default:
            puts('No argument provided.');
            break;
        case 'add':
            $parent = $argv[2];
            $new_value = $argv[3];
            $path = addNode( $parent, $new_value );
            puts("Node added to $path: $new_value.");
            break;
    }
} catch (Exception $e) {
    puts($e->getMessage);
 } */

function flatten($node) {
    global $flattened;
    global $root;
    
    puts('sizeof = '.sizeof($root, COUNT_RECURSIVE));
}
 
function addNode( $new_value ) {
    global $root;
    global $opts;
    
    $insert = new Node( $new_value );
    
    findLowestFree( $root, $insert );
}

function findLowestFree( $current, $insert ) {
    global $root;

    if ( $current->value == $insert->value ) {
        throw new Exception("A node with that value already exists in the tree with root ".$root->value);
    } elseif ( $insert->value < $current->value ) {
        if (empty($current->left)) {
            $current->setLeft( $insert );
            $insert->parent_node = $current->value;
        } else {
            findLowestFree( $current->left, $insert);
        }
    } else {
        if (empty($current->right)) {
            $current->setRight( $insert );
            $insert->parent_node = $current->value;
        } else {
            findLowestFree( $current->right, $insert);
        }
    }
}

class Node {
    public $value;
    public $parent_node;
    public $left;
    public $mark;
    public $right;

    function __construct($val, $par = NULL) {
        if (isset($val)) {
            $this->setValue( $val );
        }
    }
    
    function setValue( $value ) {   $this->value = $value;  }
    function setParentNode( $parent_node ) {   $this->parent_node = $parent_node;  }
    function setLeft( $left ) {   $this->left = $left;  }
    function setRight( $right ) {   $this->right = $right;  }
    function setMark( $node ) {   $this->mark++;  }
}

function puts( $msg ) {
    echo $msg."\n";
}

function getArgs($argv, $count) {
    $output = array();

    for ($i = 2; $i < $count; $i++) {
        if (!isset($argv[$i])) {
            throw new Exception('Insufficient arguments provided.');
        }
        $output[] = $argv[$i];
    }
    return $output;
}