<?PHP
include 'backend.php';

class Spawn {
    private $type = '';
    private $coat_color_prime;
    private $coat_color2;
    private $alt_color_prime;
    private $alt_color2;
    private $facial_feature_prime;
    private $facial_feature2;
    private $eye_type_prime;
    private $eye_type2;
    private $mouth_type_prime;
    private $mouth_type2;
    private $be = NULL;

    function __construct($type = 1 /*cat*/, $parent1 = NULL, $parent2 = NULL) {
print(__file__.', '.__class__.'::'.__function__.'( '.__line__.' )'."<br/>");
        $parents = FALSE;
        //TODO: Conver this to a row read from breed_spawn_types:attributes
        $feature_list = array('coat_color', 'alt_color', 'facial_feature', 'eye_type', 'mouth_type');
        
        //Check to see if Parents exist
        if ($parent1 != NULL || $parent2 != NULL) {
            $parents = TRUE;
        
            //Verify that parents are also Spawn types.
            if (! $parent1 instanceof Spawn || ! $parent2 instanceof Spawn) {
                throw new Exception('Parent exists but is not an instance of Spawn');
            }
            
            //Verify that the child is the same as parents, and parents are the same
            if ($type != $parent1->getType() || $type != $parent2->getType()) {
                throw new Exception('Parent types do not match each other or child.');
            }
        }
        
        $this->type = $type;
        $this->be = new Backend();
        
        foreach ($feature_list as $temp => $feature) {
            if ($parent1 == NULL || mt_rand(1, 10) == 1) {
                $this->assignAttributeAtRandom($feature);
            } else {
                $this->assignAttributeFromParent($feature, $parent1, $parent2);
            }
        }
    }
    
    function assignAttributeAtRandom($feature) {
print(__file__.', '.__class__.'::'.__function__.'( '.__line__.' )'."<br/>");
        $type = $this->type;
        $possible = $this->be->getPossibilitiesForPosition($type, $feature);
print(__file__.', '.__class__.'::'.__function__.'( '.__line__.' ): '.json_encode($possible)."<br/>");
        $possible = explode(',', $possible);
        if (is_array($possible)) {
            $selected = array_rand($possible);
            $off = array_rand($possible);
        } else {
            $selected = $off = $possible;
        }
print(__file__.', '.__class__.'::'.__function__.'( '.__line__.' ): '.$selected."<br/>");
        $prime = $feature . '_prime';
        $off = $feature . '2';
        $this->{$prime} = $prime;
        $this->{$off} = $off;
    }
    
    function assignAttributeFromParent($feature, $p1, $p2) {
print(__file__.', '.__class__.'::'.__function__.'( '.__line__.' )'."<br/>");
        $type = $this->type;
        $prime = $feature . '_prime';
        $off = $feature . '2';

        //Have a 15% chance to choose the parent's off-gene instead of their primary.
        //Randomly choose from the parents equally.
        if (mt_rand(0, 1) == 1) {
            $this->{$prime} = (mt_rand(0, 7) == 1) ? $p1[$prime] : $p1[$off];
            $this->{$off} = (mt_rand(0, 7) == 1) ? $p2[$prime] : $p2[$off];
        } else {
            $this->{$prime} = (mt_rand(0, 7) == 1) ? $p2[$prime] : $p2[$off];
            $this->{$off} = (mt_rand(0, 7) == 1) ? $p1[$prime] : $p1[$off];
        }
    }
    
    /**************************GETTERS AND SETTERS*********************************************/
    
    function getType() {
        return $this->type;
    }
    
    /**************************ATTRIBUTE INSTANTIATORS*****************************************/
}