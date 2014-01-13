<?PHP

$string = 'abcdefg';
echo $string."\n";

$len = strlen($string);
echo "string is $len chars long.\n";
$newstr = '';
foreach (range(0, $len) as $c) {
	$newstr .= substr($string, $len - $c, 1);
}
echo "newstr is $newstr.\n";
