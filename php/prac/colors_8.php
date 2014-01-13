<?PHP
for($i = 0; $i < 20; $i++) {
    $obj[$i] = new Obj();
}
print_r($obj);
foreach (array("\e[0;31mred\e[0m", "\e[0;36mblue\e[0m", "\e[0;32mgreen\e[0m") as $color) {
    foreach($obj as $i => $o) {
        if ($o->color == $color) {
            unset($obj[$i]);
            $obj[] = new Obj($color);
        }
    }
}
print_r($obj);

class Obj {
    public $color = '';
    function __construct ( $color = NULL ) {
        $this->color = ($color == NULL) ? array_rand(array_flip(array("\e[0;31mred\e[0m", "\e[0;36mblue\e[0m", "\e[0;32mgreen\e[0m"))) : $color;
    }
}