WRArticleType extension for MediaWiki
=============================================

The WRArticleType extension allows users to set the type of an article through a parser hook named
"articletype". The extension insert said article type in a few places:

- the HTML body classes, which allows for unique styling per article type
- the page_props table, which allows querying through the API
- Javascript config variables, as wgArticleType
- As an additional text in the title>, if an appropriate message is available


## Usage
- Use `{{#articletype:the_actual_article_type}}` (see localizations below) in an article.
- To style the article, you can then use class `.article-type-[actual type]`, e.g. for type "portal"
  this would be `.article-type-portal`.
- You can read the article type from JS using `mw.config.get('wgArticleType')`.
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
