<?php

class WRArticleType {
	/**
	 * Internal store for Article Type
	 *
	 * @private
	 */
	protected $mArticleType;
	/**
	 * const
	 * @private
	 */

	private static $DATA_VAR = 'ArticleType';

	public function onParserFirstCallInit( Parser &$parser ) {
		$parser->setFunctionHook( 'articletype', array( $this, 'setArticleType' ) );
		return true;
	}

	/**
	 * Parser hook handler for {{#articletype}}
	 *
	 * @param Parser $parser : Parser instance available to render
	 *  wikitext into html, or parser methods.
	 * @param string $articleType : the article type to set
	 *
	 * @return string: HTML to insert in the page.
	 */
	public function setArticleType( Parser &$parser, $articleType ) {
		global $wgArticleTypeConfig;
	    $articleType = trim( htmlspecialchars( $articleType ) );
		if ( !in_array( $articleType, $wgArticleTypeConfig['types'] ) ) {
			$articleType = 'unknown';
		}
	    $this->mArticleType = $articleType;
	    $parser->getOutput()->setExtensionData( WRArticleType::$DATA_VAR, $articleType );
		$parser->getOutput()->setProperty( WRArticleType::$DATA_VAR, $articleType );

	    return;
	}

	function onOutputPageParserOutput( OutputPage &$out, ParserOutput $parserOutput ) {
		global $wgArticleTypeConfig;
		if ( !in_array( $this->getArticleType(), $wgArticleTypeConfig['noTitleText'] ) ) {
			$this->setPageTitle( $out, $parserOutput );
		}

		return true;
	}

	function getArticleType() {
		if ( $this->mArticleType ) {
			return $this->mArticleType;
		}

		return 'unknown';
	}

	/**
	 * @param OutputPage $out
	 * @param Skin $sk
	 * @param $bodyAttribs
	 * @return bool
	 * @throws MWException
	 */
	public function onOutputPageBodyAttributes( OutputPage $out, $sk, &$bodyAttribs ) {
		$bodyAttribs['class'] .= " article-type-{$this->getArticleType()}";

		return true;
	}

	/**
	 * ResourceLoaderGetConfigVars hook
	 * Make extension configuration variables available in javascript
	 *
	 * @param $vars
	 * @return true
	 */
	public function onMakeGlobalVariablesScript( array &$vars, OutputPage $out ) {
		$vars['wgArticleType'] = $this->getArticleType();

		return true;
	}


	/**
	 * @param OutputPage $out
	 * @param ParserOutput $parserOutput
	 *
	 * @internal param string $articleType
	 */
	protected function setPageTitle( OutputPage &$out, ParserOutput $parserOutput) {
		$msgKey = "articletype-type-{$this->getArticleType()}";
		$typeMsg = wfMessage( $msgKey );
		if ( $typeMsg->exists() && !$typeMsg->isBlank() ) {
			$currentPageTitle = $parserOutput->getDisplayTitle();
			$additionaltext = '<span class="article-type"> (' . $typeMsg->plain() . ')</span>';
			$newPageTitle = $currentPageTitle . $additionaltext;

			/* Set display both on ParserOutput and OutputPage, to be on the safe side */
			$parserOutput->setDisplayTitle( $newPageTitle );
			$out->setPageTitle( $newPageTitle );
		}
	}



}
