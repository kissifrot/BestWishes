BestWishes
=============

[![PHP Build status](https://github.com/kissifrot/BestWishes/actions/workflows/php.yml/badge.svg)](https://github.com/kissifrot/BestWishes/actions/workflows/php.yml)

BestWishes is a multilingual wishlist application allowing users to manage their wishlist and indicate who have[composer.json](..%2Fsf6.2%2Fcomposer.json) bought what to avoid receiving the same gift for Christmas (for example).

Users can also add surprise gifts and manage additions and purchase alerts.

Recurrent as well as "one-shot" events are also configurable.

# Symfony versions support

| Project version | Symfony version |
|-----------------|-----------------|
| 0.x             | 5.4             |
| 1.x             | 6.4             |
| (current)       | 7.4             |

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

A basic setup command is included, you can use it by running the command:
```sh
$ php bin/console bw:setup
```
This command will create an admin user and defaults events if needed.


