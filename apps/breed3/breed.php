<?PHP
include 'breed_iface.php';

class Squares extends Spawn {
    private $p1 = array();
    private $p2 = array();
    private $dominance_threshold = 85;
    private $traits = array(
        'color' => array('dominant' => '', 'recessive' => '', 'type' => 'color'),
    );
	public $color;

    function __construct($parent1 = NULL, $parent2 = NULL) {
		try {
			//Check to see if Parents exist
			if ($parent1 != NULL || $parent2 != NULL) {
				$parents = TRUE;
			
				//Verify that parents are also Spawn types.
				$class = __class__;
				if (! $parent1 instanceof $class || ! $parent2 instanceof $class) {
					throw new Exception('Parent exists but is not an instance of Spawn');
				}
			}
			//Set parents
			$this->setP1($parent1);
			$this->setP2($parent2);
			
			foreach ($this->traits as $feature => &$val) {
				$p1_val = $this->getAttr($feature, $parent1);
				$p2_val = $this->getAttr($feature, $parent2);
				$val = $this->assignAttributes($val, $p1_val, $p2_val);
			}
			$this->color = $this->traits['color']['dominant'];
		} catch (Exception $e) {
			echo "<br/>EXCEPTION:".json_encode($e->getMessage().'<br/><br/><br/>');
		}
    }
    
    function getRandomColor() {
		if (mt_rand(1, 25) == 1) {
			switch (mt_rand(1, 5)) {
				case 1:
					return '#ff0000';
				case 2:
					return '#00ffoo';
				case 3:				
					return '#0000ff';
				case 3:				
					return '#000000';
				case 3:				
					return '#ffffff';
			}
		}
        $color = '#';		
        $color .= dechex(mt_rand(0, 16777215));
        return $color;
    }
    
    //Gets a parents' dominant/recessive values, and returns the appropriate one based on the dominance threshold.
    //If no parent exists, return a random value.
    function getAttr($attr, $obj) {
        if ($obj == NULL) {
            return $this->handleNullParents($attr);
        }
        if (!isset($obj->traits[$attr]['recessive']) || !isset($obj->traits[$attr]['dominant'])) {
            throw new Exception('Obj is set but does not have appropriate traits set: '.json_encode($obj));
        }
        if (mt_rand(1, 100) >= $this->dominance_threshold) {
            return $obj->$traits[$attr]['recessive'];
        }
        return $obj->$traits[$attr]['dominant'];
    }
    
    //Input: $attribute_set = array('attribute', 'dominant', 'recessive')
    //Output: return an array with those elements filled in.
    function assignAttributes($attribute_set, $p1_val, $p2_val) {
        if (mt_rand(0, 1) == 1) {
            $attribute_set['dominant'] = $p2_val;
            $attribute_set['recessive'] = $p1_val;
        } else {
            $attribute_set['dominant'] = $p1_val;
            $attribute_set['recessive'] = $p2_val;        
        }
        return $attribute_set;
    }
    
    function handleNullParents( $attr ) {
        //Handle null parents
        switch ($attr) {
            default:           
                throw new Exception('No valid type set in attribute_set: '.json_encode($attr));
            case 'color':
                return $this->getRandomColor();
        }
    }
    
    /**************************GETTERS AND SETTERS*********************************************/
    
    function setP1($parent) {
        $this->p1 = $parent;
    }
    function setP2($parent) {
        $this->p2 = $parent;
    }
    
    
    /**************************ATTRIBUTE INSTANTIATORS*****************************************/
}