<?php

class CssToSassConverter {

	public function convert ($css) {
		$selectors = $this->getSelectors($css);
		$statementBlocks = $this->getStatementBlocks($css);

		$SASS = $this->buildSass($selectors, $statementBlocks);

		return $SASS;
	}

	private function getNtabs($n) {
		$tabs = '';
		for ($i = $n; $i > 0; $i--) {
			$tabs = $tabs . "   ";
		}
		return $tabs;
	}

	private function buildSass($selectors, $statementBlocks, $SASS = '', $originalSelectorString = '', $level = 0) {
		

		
		foreach($selectors as $selector => $children) {
			$selectorString = $originalSelectorString . ' ' . $selector;
			
			$SASS = $SASS . "\n\n" . $this->getNtabs($level) . $selector . " { \n";
			$css = $this->getCssForThisSelector($statementBlocks, $selectorString);
			$css = $this->getNtabs($level + 1) . str_replace("\n", "\n" . $this->getNtabs($level + 1), $css);
			$SASS = $SASS . $css;

			if (count($children) > 0) {
				$SASS = $this->buildSass($children, $statementBlocks, $SASS, $selectorString, ($level + 1));
				$SASS = $SASS . "\n" . $this->getNtabs($level) . "}";
			} else {
				$SASS = $SASS . "\n" . $this->getNtabs($level) . "}";
			}
		}

		return $SASS;
	}

	private function getCssForThisSelector($statementBlocks, $selectorString) {

		$selectorString = trim($selectorString);
		$css = "";

		foreach($statementBlocks as $block) {
			preg_match_all('/\s*' . $selectorString . '\s*{([^}]*)/im', $block, $tmp);

			echo "\n preg match all func: '/\s*" . $selectorString . "\s*{([^}]*)/im'";

			$css = $css . trim($tmp[1][0]);
		}

		return $css;
	}

	public function getStatementBlocks ($css) {
		preg_match_all('/([0-9a-zA-Z#: \.\-_]+{([^}]*))/im', $css, $statementBlocks);
		for ($i = 0; $i < count($statementBlocks[0]); $i++) {
			$statementBlocks[0][$i] = $statementBlocks[0][$i] . '}';
		}
		$statementBlocks = $statementBlocks[0];
		// trim each line
		for($i = 0; $i < count($statementBlocks); $i++) {
			$statementBlocks[$i] = implode("\n", array_map('trim', explode("\n", $statementBlocks[$i])));
		}
		return $statementBlocks;
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
			$tmpArray = &$nestedArray;

			foreach ($selectorChunks as $chunk) {
				if (!$tmpArray[$chunk]) {
					$tmpArray[$chunk] = array();
				}
				$tmpArray = &$tmpArray[$chunk];
			}
			unset($tmpArray);
		}

		return $nestedArray;
	}
}