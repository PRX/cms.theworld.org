# WordPress

This is a WordPress repository configured to run on the [Pantheon platform](https://pantheon.io).

Pantheon is website platform optimized and configured to run high performance sites with an amazing developer workflow. There is built-in support for features such as Varnish, Redis, Apache Solr, New Relic, Nginx, PHP-FPM, MySQL, PhantomJS and more.

# PRX - The World

The World is a public radio program that crosses borders and time zones to bring home the stories that matter. From PRX.

PRX is Shaping the future of audio by building technology, training talented producers and connecting them with supportive listeners.

## Development instance:

- Develop : https://dev-the-world.pantheonsite.io
- Test : https://test-the-world.pantheonsite.io
- Multidev : https://[git-branch]-the-world.pantheonsite.io

## Production instance:

- https://live-the-world.pantheonsite.io/
- TBD Domain

## JIRA:

- https://fourkitchens.atlassian.net/jira/software/c/projects/PRIS/boards/236

## Dependencies

- Lando (https://docs.lando.dev/) - version `>=3.6`
- Node `^16.x`
- PHP_CodeSniffer (https://github.com/squizlabs/PHP_CodeSniffer) - `>=3.6`
  - WordPress-Coding-Standards (https://github.com/WordPress/WordPress-Coding-Standards) - `>=2.3`

## Setup

This repository uses Lando for local development. Run the following commands:

1. Set up lando:

`lando start`

2. Retrieve and export of the database and place it in the `reference/` directory. If a database export does not exist within this directory the script will attempt to pull the database from Pantheon's dev environment. After a successful import it will find and replace all domain records in the database with your local domain and import any configuration bundles saved to `wp-content/config`. Finally, it will generate a `local-admin` user for you.

`npm run refresh`

## Development

### Composer Dependencies

Run `composer install` to install dev dependencies. Install latest version of Composer if you don't already have it installed.

- PHPCS
- WordPress Coding Standards Sniffer

#### VS Code Extensions

Install the following extension:

- PHPCS (ikappas.phpcs)
- PHP Sniffer (wongjn.php-sniffer)
- EditorConfig for VS Code (EditorConfig.EditorConfig)

### Theme Development

- TBD

### Helper scripts

To use the helper script provided you will need to have `npm` installed. These commands are bash scripts located in the `./scripts` directory and defined in `package.json`.

Run `nvm use` prior to running the scripts below.

`npm run local` - Imports configuration bundles and verifies all domain records in the database use your local domain.

`npm run refresh` - See the `lando refresh` description above.

`npm run config-export` - Updates configuration bundles stored in `wp-content/config/` with any changes staged in the database.
