BestWishes
=============

[![PHP Build status](https://github.com/kissifrot/BestWishes/actions/workflows/php.yml/badge.svg)](https://github.com/kissifrot/BestWishes/actions/workflows/php.yml)

BestWishes is a multilingual wishlist application allowing users to manage their wishlist and indicate who have bought what to avoid receiving the same gift for Christmas (for example).

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

## PDF exports

BestWishes uses Gotenberg to render printable wishlists with Chromium. Gotenberg
runs in its own container. The PHP host only needs HTTP access to it.

When PHP runs on the Docker host, start the Chromium-only image and publish it
on localhost.

```sh
docker run -d --name bestwishes-gotenberg --restart unless-stopped \
  -p 127.0.0.1:3000:3000 \
  gotenberg/gotenberg:9.9.1-chromium gotenberg \
  --chromium-disable-javascript --chromium-deny-private-ips --chromium-deny-public-ips

curl --fail http://127.0.0.1:3000/health
```

The default `GOTENBERG_URL` is `http://127.0.0.1:3000`. Override it in
`.env.local` or in the environment if you use another address. If PHP also runs
in Docker, replace the `-p` option with `--network <private-network>`, using the
network that PHP is on, and set `GOTENBERG_URL=http://bestwishes-gotenberg:3000`.
Keep the API private because the conversion requests include wishlist content.

Build and deploy the Encore assets with the application. The PDF export sends the
compiled CSS and bundled Open Sans and Font Awesome fonts with the rendered HTML.

```sh
yarn install --frozen-lockfile
yarn build
```

When upgrading, rebuild the assets and remove the obsolete `WKHTMLTOPDF_PATH` and
`WKHTMLTOIMAGE_PATH` settings from any deployment overrides.

### Run Gotenberg with Supervisor

When Docker and PHP run on the same host, save this configuration as
`/etc/supervisor/conf.d/bestwishes-gotenberg.conf`. Keep the `command` directive
on a single line.

```ini
[program:bestwishes-gotenberg]
command=/usr/bin/docker run --rm --name bestwishes-gotenberg -p 127.0.0.1:3000:3000 gotenberg/gotenberg:9.9.1-chromium gotenberg --chromium-disable-javascript --chromium-deny-private-ips --chromium-deny-public-ips
autostart=true
autorestart=true
startsecs=3
startretries=5
stopsignal=TERM
stopwaitsecs=30
redirect_stderr=true
```

Supervisor runs the container in the foreground and handles restarts. If you
already launched the standalone container above, stop and remove it first to
free the container name and port. The Supervisor entry uses the default
`GOTENBERG_URL` of `http://127.0.0.1:3000`.

Pull the image, activate the entry, and check the process status and HTTP health
endpoint.

```sh
sudo docker pull gotenberg/gotenberg:9.9.1-chromium
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status bestwishes-gotenberg
curl --fail --retry 5 --retry-connrefused http://127.0.0.1:3000/health
```

