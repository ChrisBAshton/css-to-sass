<?php

class CssToSassConverter {

	public function convert ($css) {
		$statementBlocks = $this->getStatementBlocks($css);

		return false;
	}

	public function getStatementBlocks ($css) {
		preg_match_all('/([0-9a-zA-Z#: \.\-_]+{([^}]*))/im', $css, $statementBlocks);
		for ($i = 0; $i < count($statementBlocks[0]); $i++) {
			$statementBlocks[0][$i] = $statementBlocks[0][$i] . '}';
		}
		return $statementBlocks[0];
	}

	public function getSelectors ($css) {
		preg_match_all('/([0-9a-zA-Z#: \.\-_]+){[^}]*/im', $css, $selectors);
		$selectors = $selectors[1];

		// remove whitespace from the ends
		for($i = 0; $i < count($selectors); $i++) {
			$selectors[$i] = trim($selectors[$i]);
		}

		$selectorsToReturn = $this->convertSelectorsToNestedArray($selectors);

		return $selectorsToReturn;
	}

	/*
	Convert
		array(
			".foo .bar",
			".foo .bar p",
			".foo h1"
		)
	to
		array(
			"foo" => array(
				"bar" => array(
					"p" => array()
				),
				"h1" => array()
			)
		)
	*/
	private function convertSelectorsToNestedArray ($selectors) {

		$nestedArray = array();

		for ($i = 0; $i < count($selectors); $i++) {
			$selectorChunks = explode(" ", $selectors[$i]);
			$arr = &$nestedArray;

			foreach ($selectorChunks as $chunk) {
				if (!$arr[$chunk]) {
					$arr[$chunk] = array();
				}
				$arr = &$arr[$chunk];
			}
			unset($arr);
		}

		return $nestedArray;
	}
}