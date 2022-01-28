# The World from PRX's CMS

This repository contains the Wordpress codebase that serves as the backend for the the API and feeds for [theworld.org Next.js frontend](https://github.com/prx/theworld.org).

* `cms.theworld.org` is the CMS access point. Not intended for public consumption.

### Requirements
 - Lando (3.0+).
 - Terminus (1.6.1+).

### Setup
 - Install all requirements.
 - Clone this repository, and navigate to it's root directory.
 - Crank up Lando: `lando start`. This process will take a few minutes on the first run.
 - From the Pantheon dashboard, download a recent backup of the DB, or run this command: `terminus backup:get the-world-wp.dev --element=db --to=db.sql.gz`.
 - Import the database you just downloaded: `lando db-import db.sql.gz`.
