<?PHP
//15) Given a sorted array (Ex. {1, 3, 7, 8, 9}) that is shifted an unknown number of times
//    (Ex. {7, 8, 9, 1, 3}), write code to find the smallest number in the array.

$start = array(4, 5, 6, 7, 9, 55, 77, 99, 106, 199);
$shifted = shifterize( $start );

$k = array_keys($shifted);
$new_first = $shifted[$k[0]];

print_r($shifted);

$i = findFirstOccurence( $start, $new_first);

echo "it takes $i shifts to get from the first pos to the first element ($new_first) in shifted's.\n";
$shifted = array_reverse($shifted);
$answer = findNthValue( $i, $shifted );

echo "\nanswer = $answer\n";

function findNthValue( $i, $shifted) {
    foreach($shifted as $temp => $countdown) {
        $i--;
        if ($i == 0) {
            return $countdown;
        }

    }
    return $shifted[0];
}

function findFirstOccurence( $line, $new_first ) {
    $i = 0;
    foreach($line as $k => $v) {
        if ($v === $new_first) {
            return $i;
        }
        $i++;
    }
}

function shifterize( $start ) {
$rand = mt_rand(0, 7);
echo "Shifting $rand positions...\n";
    for(; $rand > 0; $rand--) {
        $k = array_keys($start);
        $start[] = $start[$k[0]];
        unset($start[$k[0]]);
    }
    return $start;
}