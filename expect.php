<?php

class Expect {

	public function equal ($actual, $expected) {
		if ($actual !== $expected) {
			echo "\n\t Actual \n\n";
			var_dump($actual);
			echo "\n\t Expected \n\n";
			var_dump($expected);
			throw new Exception("Expected these to be equal.");
		}
		else {
			echo ".";
		}
	}

}