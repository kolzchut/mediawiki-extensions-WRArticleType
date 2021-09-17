<?php

namespace MediaWiki\Extension\ArticleType;

use PageProps;
use Title;

class ArticleType {
	/**
	 * @const
	 */
	static string $DATA_VAR = 'ArticleType';

	/**
	 * Get SELECT fields and joins for retrieving the article type
	 *
	 * @param null $articleType: filter by article type
	 * @param string $fieldNameToCompare: if we want to compare to a differently name page_id field, such as log_page
	 *
	 * @return array[] With three keys:
	 *   - tables: (string[]) to include in the `$table` to `IDatabase->select()`
	 *   - fields: (string[]) to include in the `$vars` to `IDatabase->select()`
	 *   - join_conds: (array) to include in the `$join_conds` to `IDatabase->select()`
	 *  All tables, fields, and joins are aliased, so `+` is safe to use.
	 */
	public static function getJoin( $articleType = null, $pageIdFieldName = 'page_id' ): array {
		$dbr = wfGetDB( DB_REPLICA );

		$joinType  = $articleType ? 'INNER JOIN' : 'LEFT OUTER JOIN';
		$joinConds = [ $pageIdFieldName . ' = article_type_page_props.pp_page', "article_type_page_props.pp_propname = '" . self::$DATA_VAR . "'" ];
		if ( $articleType ) {
			$joinConds[] = 'article_type_page_props.pp_value = ' . $dbr->addQuotes( $articleType );
		}

		$tables['article_type_page_props'] = 'page_props';
		$joins['article_type_page_props'] = [ $joinType, $joinConds ];

		// Changing the field's alias MUST be marked as a breaking change
		$fields['article_type'] = 'article_type_page_props.pp_value';

		return [
			'tables' => $tables,
			'fields' => $fields,
			'join_conds' => $joins
		];
	}

	/**
	 * Get article type from the page_props table
	 *
	 * @param Title $title
	 *
	 * @return mixed|null
	 */
	public static function getArticleType( Title $title ) {
		$pageProps = PageProps::getInstance();
		$propArray = $pageProps->getProperties( $title, ArticleType::$DATA_VAR );

		return empty( $propArray ) ? null : array_values( $propArray )[0];

	}

	public static function isValidArticleType( $type ) {
		return in_array( $type, self::getValidArticleTypes() );
	}

	public static function getReadableArticleTypeFromCode( $code, $count = 1 ) {
		if ( self::isValidArticleType( $code ) ) {
			$msgKey = "articletype-type-{$code}";
			$typeMsg = wfMessage( $msgKey );
			if ( $typeMsg->exists() && !$typeMsg->isBlank() ) {
				return $typeMsg->numParams( $count )->text();
			}
		}

		return 'unknown';
	}

	/**
	 * @return array
	 */
	public static function getValidArticleTypes(): array {
		global $wgArticleTypeConfig;
		return $wgArticleTypeConfig['types'];
	}
}

\class_alias(ArticleType::class, \WRArticleType::class, true );

