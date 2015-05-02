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
	private static $mValidArticleTypes = array(
	    'portal', 'right', 'service', 'term', 'proceeding', 'health',
	    'organization', 'government', 'event', 'ruling', 'law',
	    'letter', 'faq', 'newsletter', 'user', 'mainpage'
	);


	/* List of types that we don't add as text to the page title */
	private static $mNoTypeInTitle = array(
		'unknown',
		'portal',
		'user'
	);

	private static $DATA_VAR = 'ArticleType';

	/**
	 * @throws MWException
	 * @return CustomData
	 */
	public function getCustomData() {
	    global $wgCustomData;

	    if ( !$wgCustomData instanceof CustomData ) {
	        throw new MWException( 'CustomData extension is not properly installed.' );
	    }

	    return $wgCustomData;
	}

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
	    /* @todo: validate passed article type by using a wfMessage list? */
	    $articleType = trim( htmlspecialchars( $articleType ) );
		if ( !in_array( $articleType, self::$mValidArticleTypes ) ) {
			$articleType = 'unknown';
		}
	    $this->mArticleType = $articleType;
	    $this->getCustomData()->setParserData(
		    $parser->getOutput(), WRArticleType::$DATA_VAR,
		    array( $articleType )
	    );

	    return;
	}

	function onOutputPageParserOutput( OutputPage &$out, ParserOutput $parserOutput ) {
		$this->mArticleType = array_shift(
			$this->getCustomData()->getParserData( $parserOutput, WRArticleType::$DATA_VAR )
		);
		if ( !in_array( $this->mArticleType, self::$mNoTypeInTitle ) ) {
			$this->setPageTitle( $out, $parserOutput );
		}

		return true;
	}

	function getArticleType( OutputPage $out ) {
		return array_shift(
			WRArticleType::getCustomData()->getPageData( $out, WRArticleType::$DATA_VAR )
		);
	}



	/**
	 * @param OutputPage $out
	 * @param Skin $sk
	 * @param $bodyAttribs
	 * @return bool
	 * @throws MWException
	 */
	public function onOutputPageBodyAttributes( OutputPage $out, $sk, &$bodyAttribs ) {
		$this->mArticleType = $this->getArticleType( $out );
		$bodyAttribs['class'] .= " article-type-{$this->mArticleType}";

		return true;
	}

	/**
	 * ResourceLoaderGetConfigVars hook
	 * Make extension configuration variables available in javascript
	 *
	 * @param $vars
	 * @return true
	 */
	public static function onMakeGlobalVariablesScript( array &$vars, OutputPage $out ) {
		$vars['wgArticleType'] = WRArticleType::getArticleType( $out );

		return true;
	}


	/**
	 * @param OutputPage $out
	 * @param ParserOutput $parserOutput
	 *
	 * @internal param string $articleType
	 */
	protected function setPageTitle( OutputPage &$out, ParserOutput $parserOutput) {
		$msgKey = "articletype-type-{$this->mArticleType}";
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
