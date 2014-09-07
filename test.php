<?php

include 'convert.php';
include 'test_data.php';
include 'expect.php';

/*echo 'Ashton debil';*/

	$converter = new CssToSassConverter();
    $converter->convert($testData);
?>