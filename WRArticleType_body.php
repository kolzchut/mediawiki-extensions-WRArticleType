<?php

class WRArticleType {
    /**
     * Internal store for Article Type
     *
     * @private
     */
	var $mArticleType ;
    /**
     * const
     * @private
     */
    private static $mValidArticleTypes = array(
        'portal', 'right', 'service', 'term', 'proceeding', 'health',
        'organization', 'government', 'event', 'ruling', 'law',
        'letter', 'faq', 'newsletter', 'user', 'mainpage'
    );


	private static $mNoTypeInTitle = array(
		'unknown',
		'portal',
		'user'
	);

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
		if( !in_array( $articleType, self::$mValidArticleTypes ) ) {
			$articleType = 'unknown';
		}
        $this->mArticleType = $articleType;
        $this->getCustomData()->setParserData( $parser->getOutput(), 'ArticleType', array( $articleType ) );

        return;
    }

	function onOutputPageParserOutput( OutputPage &$out, ParserOutput $parserOutput ) {
		$articleType = array_shift( $this->getCustomData()->getParserData( $parserOutput, 'ArticleType' ) );
		if( !in_array( $articleType, self::$mNoTypeInTitle ) ) {
			$this->setPageTitle( $out, $parserOutput, $articleType );
		}

		return true;
	}

	/**
	 * @param OutputPage $out
	 * @param Skin $sk
	 * @param $bodyAttribs
	 * @return bool
	 * @throws MWException
	 */
	public function onOutputPageBodyAttributes( OutputPage $out, $sk, &$bodyAttribs ) {
        $this->mArticleType = array_shift( $this->getCustomData()->getPageData( $out, 'ArticleType' ) );
        $bodyAttribs['class'] .= " article-type-{$this->mArticleType}";

		return true;
    }

	/**
	 * @param OutputPage $out
	 * @param string $articleType
	 */
	protected function setPageTitle( OutputPage &$out, ParserOutput $parserOutput, $articleType) {
		$msgKey = "articletype-type-$articleType";
		$typeMsg = wfMessage( $msgKey );
		if( $typeMsg->exists() && !$typeMsg->isBlank() ) {
			$currentPageTitle = $parserOutput->getDisplayTitle();
			$additionaltext = '<span class="article-type"> (' . $typeMsg->plain() . ')</span>';
			$newPageTitle = $currentPageTitle . $additionaltext;

			/* Set display both on ParserOutput and OutputPage, to be on the safe side */
			$parserOutput->setDisplayTitle( $newPageTitle );
			$out->setPageTitle( $newPageTitle );
		}
	}



}
