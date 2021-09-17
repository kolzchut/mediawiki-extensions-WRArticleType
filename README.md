ArticleType extension for MediaWiki
=============================================

The ArticleType extension allows users to set the type of article through a parser hook named
"articletype". The extension insert said article type in a few places:

- the HTML body classes, which allows for unique styling per article type
- the page_props table, which allows querying through the API
- Javascript config variables, as wgArticleType
- As an additional text in the title>, if an appropriate message is available


## Usage
- Use `{{#articletype:the_actual_article_type}}` (see localizations below) in an article.
- To style the article, you can then use class `.article-type-[actual type]`, e.g., for type "portal"
  this would be `.article-type-portal`.
- You can read the article type from JS using `mw.config.get('wgArticleType')`.
- You can read the article type in PHP this way:
  ```php
    ArticleType::getArticleType( $this->getTitle() );
  ```
- You can read the article type using the API this way:

        api.php?action=query&titles=__article_title__&prop=pageprops&ppprop=ArticleType
- To add per-type text to the page title, edit the appropriate system message; names are in the format
  `articletype-type-[actual type]` (e.g., `[[MediaWiki:article-type-portal]]`)

### Current localizations
In Hebrew, you can use `{{#סוגערך:}}` or `{{#סוג ערך:}}`.

## Configuration
All configuration is placed under `$wgArticleTypeConfig`, which has the following options:

- `$wgArticleTypeConfig['types']`: an array of valid article types, to prevent malicious use.

- `$wgArticleTypeConfig['noTitleText']`: an array of article types where we do not want any text added
  to the page title. You have the following options:
    - add any type to this array
    - or blank the appropriate system message (see their format above)
    - to prevent text in all titles, you can set `$wgArticleTypeConfig['noTitleText'] = $wgArticleTypeConfig['types']`.

## Changelog

### [2.0.0] - 2021-09-17
- Breaking change: only supports MediaWiki 1.35 and higher
- Split to main class and Hooks
- Class name is now ArticleType; class alias 'WRArticleType' exists for b/c.
- Added convenience functions such as:
	- `getArticleType( $title )` for retrieving the property for a single title
	- `getJoin( $articleType )` for retrieving the property for multiple pages from the DB

### [1.3.0] - 2018-10-23
Converted to MW's "new" extension registration mechanism.

### [1.2.0] - 2015-05-18
#### Added
- Save the article type into page_props as well, where it can be queried
  using the API.
- README, LICENSE & CHANGELOG files
- New configuration options, through $wgArticleTypeConfig:
	- $wgArticleTypeConfig['types'] - valid article types (previously hardcoded)
	- $wgArticleTypeConfig['noTitleText'] - article types that shouldn't have the type added
	  to the title (previously hardcoded).

#### Changed
- Removed legacy dependency on Extension:CustomData
