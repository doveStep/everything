<?PHP
global $end;

class Node {
	public $val = 0;
	public $next = NULL;
	
	function __construct( $val ) {
		$this->val = $val;
	}
		
	function attach ($node_val) {
		global $end;
		$this->next = new Node( $node_val );
		$end = $this->next;
		
		return $end;
	}
}

$head = new Node(7);

$end = $head->attach(9);
$end = $end->attach(12);
$end = $end->attach(79);

print_r($head);

reverse_ll($head);

function reverse_ll( $head ) {
	$prev = NULL;
	$temp = NULL;
	for ($current = $head; $current != NULL; $current = $temp->next) {
		$temp = $current->next;
		$current->next = $prev;
		print_r($current);
	}
}