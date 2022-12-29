BestWishes
=============


[![Build Status](https://travis-ci.org/kissifrot/BestWishes.svg?branch=master)](https://travis-ci.org/kissifrot/BestWishes)

BestWishes is a multilingual wishlist application allowing users to manage their wishlist and indicate who have[composer.json](..%2Fsf6.2%2Fcomposer.json) bought what to avoid receiving the same gift for Christmas (for example).

Users can also add surprise gifts and manage additions and purchase alerts.

Recurrent as well as "one-shot" events are also configurable.

The current branch is based on symfony 6.x. For symfony 5.4 version see `1.x` branch.

# Installation

BestWishes uses [Composer](http://getcomposer.org/) to ease the creation of a new project:

```sh
$ composer create-project webdl/bestwishes path/to/install
```

Composer will create a new BestWishes project under the `path/to/install` directory.  
You will have to enter main parameters such as database info and others in the `.env` file.

## Database setup

After having indicated the main parameters, run

```sh
$ php bin/console doctrine:database:create
```
to create the database, and

```sh
$ php bin/console doctrine:migrations:migrate
```
to populate schema.

## Application setup

_TODO_


An installer is needed, didn't have the time to do it yet.


