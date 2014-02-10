<?PHP
require_once '../../common/orm.php';

class Backend {
    public $db = NULL;
    
	function __construct( $session ) {
print(__file__.', '.__class__.'::'.__function__.'( '.__line__.' )'."<br/>");
        $this->db = new Db();
	}

	function getPossibilitiesForPosition($type, $position) {
print(__file__.', '.__class__.'::'.__function__.'( '.__line__.' )'."<br/>");
		$details['verb'] = 'select';
		$details['actor'] = 'breed_attributes_by_type';
		$details['where_aa'] = array('type' => $type, 'position' => $position);
		$details['limit'] = 1;
print(__class__.'::'.__function__.': details = '.json_encode($details)."<br/>");
		$results = $this->db->cmd( $details );
print(__class__.'::'.__function__.': results = '.json_encode($results)."<br/>");

		return $results;
	}
}