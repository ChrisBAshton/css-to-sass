<?php

class CssToSassConverter {

    /* The main function to convert */
	public function convert ($css) {
		//array of selectors (representing levels?)
		$selectors = $this->getSelectors($css);
		//var_dump($selectors);
		$statementBlocks = $this->getStatementBlocks($css);
		//print_r($statementBlocks);
		$SASS = $this->buildSass($selectors, $statementBlocks);

		//return $SASS;
	}
	/* level 1 */
	private function buildSass($selectors, $statementBlocks, $SASS = '', $originalSelectorString = '', $level = 0) {
		
		foreach($selectors as $selector => $children) {
			$selectorString = $originalSelectorString . ' ' . $selector;
			
			$SASS = $SASS . "\n\n" . $this->getNtabs($level) . $selector . " {\n";
			$css = $this->getCssForThisSelector($statementBlocks, $selectorString);
			if (strlen($css) > 0) {
				$css = $this->getNtabs($level + 1) . str_replace("\n", "\n" . $this->getNtabs($level + 1), $css);
			}
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

	/* Level 2 functions */
	public function getSelectors ($css) {
		preg_match_all('/([0-9a-zA-Z#: \.\-_]+){[^}]*/im', $css, $selectors);
		$selectors = $selectors[1];

		// remove whitespace from the ends
		for($i = 0; $i < count($selectors); $i++) {
			$selectors[$i] = trim($selectors[$i]);
		}

		$selectorsToReturn = $this->convertSelectorsToNestedArray($selectors);

		//print_r($selectorsToReturn) and die();

		return $selectorsToReturn;
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

	/* Level 3 */
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

				// if chunk contains ':' character
					// if parent selector exists at this level
						// rename this to &:... and make it a child of that
					// else
						// assume we only ever want to target the : modifier, so just make it its own array

				// @TODO - we should reorder selectors before passing to this function, so that
				// top level selectors come first (i.e. h1, p, etc) and nested selectors come lower,
				// with modifier (':') selectors coming last.
				// otherwise the above pseudocode will not work - you could have a block
				// .something:last-child
				// followed by a block
				// .something
				// and it would be too late - the compiler will have assumed that :last-child was the only
				// modifier of .something we want to target.
				// would be pointless for the compiler to output:
				/*
					.something {
						
						&:last-child {
							// something
						}

					}
				*/

				if (!$tmpArray[$chunk]) {
					$tmpArray[$chunk] = array();
				}
				$tmpArray = &$tmpArray[$chunk];
			}
			unset($tmpArray);
		}

		return $nestedArray;
	}

	//HELPER
	private function getCssForThisSelector($statementBlocks, $selectorString) {

		$selectorString = trim($selectorString);
		$css = "";

		foreach($statementBlocks as $block) {
			preg_match_all('/\s*' . $selectorString . '\s*{([^}]*)/im', $block, $tmp);

			if (count($tmp[1]) > 0) {
				// mutliple blocks applying to the same selector, need to separate them
				if ($css !== '') {
					$css = $css . "\n";
				}
				$css = $css . trim($tmp[1][0]);	
			}
		}

		return $css;
	}
	
	//HELPER
	private function getNtabs($n) {
		$tabs = '';
		for ($i = $n; $i > 0; $i--) {
			$tabs = $tabs . "    ";
		}
		return $tabs;
	}
}