BestWishes
=============


[![Build Status](https://travis-ci.org/kissifrot/BestWishes.svg?branch=master)](https://travis-ci.org/travis-ci-examples/php)

BestWishes is a multilanguage wishlist application allowing users to manage their wishlist and indicate who have bought what to avoid receiving the same gift for Christmas (for example).

Users can also add surprise gifts and manage additions and purchase alerts.

Recurrent as well as "one-shot" events are also configurable.

# Installation

BestWishes uses [Composer](http://getcomposer.org/) to ease the creation of a new project:

```sh
$ composer create-project webdl/bestwishes path/to/install
```

Composer will create a new BestWishes project under the `path/to/install` directory.  
You will have to enter main parameters such as database info.

## Database setup

After having indicated the main parameters, run

```sh
$ php bin/console doctrine:schema:create
```
to create the database schema.

## Application setup

_TODO_


An installer is needed, didn't have the time to do it yet.


