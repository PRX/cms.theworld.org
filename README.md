# WordPress

This is a WordPress repository configured to run on the [Pantheon platform](https://pantheon.io).

Pantheon is website platform optimized and configured to run high performance sites with an amazing developer workflow. There is built-in support for features such as Varnish, Redis, Apache Solr, New Relic, Nginx, PHP-FPM, MySQL, PhantomJS and more. 

# PRX - The World

Shaping the future of audio by building technology, training talented producers and connecting them with supportive listeners.:


## Development instance:

- Develop : dev-the-world-wp.pantheonsite.io
- Test : test-the-world-wp.pantheonsite.io
- Multidev : [git-branch]-the-world-wp.pantheonsite.io


## Production instance:

- live-the-world-wp.pantheonsite.io
- TBD Domain


## JIRA:

- https://fourkitchens.atlassian.net/jira/software/c/projects/PRIS/boards/236


## Dependencies

- Lando (https://docs.lando.dev/)


## Setup

This repository uses Lando for local development. Run the following commands:

1. Set up lando:
`lando start`

2. Retrieve and export of the database and place it in the `reference/` directory. If a database export does not exist within this directory the script will attempt to pull the database from Pantheon's dev environment. After a successful import it will find and replace all domain records in the database with your local domain. Finally, import and configuration bundles saved to `wp-content/config`
`lando refresh`


## Development

- TBD development steps go here


### Helper scripts

To use the helper script provided you will need to have `npm` installed. These commands are bash scripts located in the `./scripts` directory and defined in `package.json`.

`npm run local` - Imports configuration bundles and verifies all domain records in the database use your local domain.

`npm run refresh` - See the `lando refresh` description above.
