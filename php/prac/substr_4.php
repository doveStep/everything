<?PHP
unset($argv[0]);
// print_r($argv);
$len1 = strlen($argv[1]);
$len2 = strlen($argv[2]);
if ($len1 > $len2) {
    echo "The first arg must be smaller or equal to the second arg.\n";
    return;
}
if ($argv[1] === $argv[2]) {
    echo "The two strings are identical and therefore strictly a subset.\n";
    return;
}

$sub = str_split($argv[1]);
$split = str_split($argv[2]);
$c_i = 0;
$start = 0;
for ($i = 0; $i < $len2; $i++) {
    // echo 'comparing '.$sub[$c_i].' & '.$split[$i]." ($i)\n";
    if ($sub[$c_i] !== $split[$i]) {
        $c_i = 0;
        unset($start);
        continue;
    }
    if (!isset($start)) {
        $start = $i;
    }
    $c_i++;
    if ($c_i >= $len1) {
        echo '"'.$argv[1].'" is a substring of "'.$argv[2].'", starting at index: '. $start."\n";
        return;
    }
}
echo __line__."\n";
echo '"'.$argv[1].'" is not a substring of "'.$argv[2]."\"\n";