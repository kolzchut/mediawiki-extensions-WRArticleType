<?php

namespace MediaWiki\Extension\ArticleType;

use PageProps;
use Title;
use function class_alias;

class ArticleType {
	/**
	 * @const
	 */
	public const DATA_VAR = 'ArticleType';

	/**
	 * Get SELECT fields and joins for retrieving the article type
	 *
	 * @param null|string|array $articleType filter by article type
	 * @param string $pageIdFieldName if we want to compare to a differently named page_id field, such as log_page
	 *
	 * @return array[] With three keys:
	 *   - tables: (string[]) to include in the `$table` to `IDatabase->select()`
	 *   - fields: (string[]) to include in the `$vars` to `IDatabase->select()`
	 *   - join_conds: (array) to include in the `$join_conds` to `IDatabase->select()`
	 *  All tables, fields, and joins are aliased, so `+` is safe to use.
	 */
	public static function getJoin( $articleType = null, string $pageIdFieldName = 'page_id' ): array {
		$dbr = wfGetDB( DB_REPLICA );

		$joinType  = $articleType ? 'INNER JOIN' : 'LEFT OUTER JOIN';
		$joinConds = [
			$pageIdFieldName . ' = article_type_page_props.pp_page',
			"article_type_page_props.pp_propname = '" . self::DATA_VAR . "'"
		];
		if ( $articleType ) {
			$joinConds[] = 'article_type_page_props.pp_value IN (' . $dbr->makeList( (array)$articleType ) . ')';
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
		$propArray = $pageProps->getProperties( $title, self::DATA_VAR );

		return empty( $propArray ) ? null : array_values( $propArray )[0];
	}

	/**
	 * @param string|array $type
	 *
	 * @return bool
	 */
	public static function isValidArticleType( $type ): bool {
		$validValues = self::getValidArticleTypes();
		// None defined, so all are valid
		if ( empty( $validValues ) ) {
			return true;
		}

		if ( empty( $type ) ) {
			return false;
		}

		$diff = array_diff( (array)$type, self::getValidArticleTypes() );
		return count( $diff ) === 0;
	}

	/**
	 * @param string $code
	 * @param int $count
	 *
	 * @return string
	 */
	public static function getReadableArticleTypeFromCode( string $code, int $count = 1 ): string {
		if ( self::isValidArticleType( $code ) ) {
			$msgKey = "articletype-type-$code";
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

// b/c
class_alias( ArticleType::class, \WRArticleType::class, true );
