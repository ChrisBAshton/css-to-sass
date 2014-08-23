<?php

include 'convert.php';
include 'test_data.php';
include 'expect.php';

try {

	$converter = new CssToSassConverter();
	$expect = new Expect();

	$expect->equal(
		$converter->getStatementBlocks($testData),
		array(
'#my_element h1 {
font-weight: bold;
}',

'#my_element ul.test {
list-style-type: none
}',

'#my_element ul.test {
color: white;
background: black;
}',

'#my_element ul.test .list-item {
text-decoration: none;
color: #737373;
}',

'#my_element ul.test .list-item:last-child {
font-weight: bold;
}'
		)
	);

	$expect->equal(
		$converter->getSelectors($testData),
		array(
			'#my_element' => array(
				'h1' => array(),
				'ul.test' => array(
					'.list-item' => array(),
					'.list-item:last-child' => array()
				)
			)
		)
	);

	$expect->equal(
		$converter->convert($testData),
		$expectedOutput
	);

	echo "\nAll tests passed. \n";

} catch (Exception $e) {
	echo $e->getMessage() . "\n\n";
}