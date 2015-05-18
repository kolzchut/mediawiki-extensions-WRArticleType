Revision history for Extension:WRArticleType
====================================================

All notable changes to this project will be documented in this file.
This project adheres (or attempts to adhere) to [Semantic Versioning](http://semver.org/).

## [1.2.0] - 2015-05-18
### Added
- Save the article type into page_props as well, where it can be queried
  using the API.
- README, LICENSE & CHANGELOG files
- New configuration options, through $wgArticleTypeConfig:
    - $wgArticleTypeConfig['types'] - valid article types (previously hardcoded)
    - $wgArticleTypeConfig['noTitleText'] - article types that shouldn't have the type added
      to the title (previously hardcoded).

### Changed
- Removed legacy dependency on Extension:CustomData
