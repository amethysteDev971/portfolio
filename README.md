# Portfolio Amethyste Design

root@dfcb4ae20363:/app# mkdir -p /app/var/tailwind/v3.4.17
root@dfcb4ae20363:/app# curl -L https://github.com/tailwindlabs/tailwindcss/releases/download/v3.4.17/tailwindcss-linux-arm64 -o /app/var/tailwind/v3.4.17/tailwindcss-linux-arm64
  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                 Dload  Upload   Total   Spent    Left  Speed
  0     0    0     0    0     0      0      0 --:--:--  0:00:01 --:--:--     0
100 38.6M  100 38.6M    0     0   619k      0  0:01:03  0:01:03 --:--:-- 1685k
root@dfcb4ae20363:/app# ls -l /app/var/tailwind/v3.4.17/tailwindcss-linux-arm64
-rw-r--r-- 1 root root 40525732 Dec 30 16:13 /app/var/tailwind/v3.4.17/tailwindcss-linux-arm64
root@dfcb4ae20363:/app# chmod +x /app/var/tailwind/v3.4.17/tailwindcss-linux-arm64
root@dfcb4ae20363:/app# ls -l /app/var/tailwind/v3.4.17/tailwindcss-linux-arm64
-rwxr-xr-x 1 root root 40525732 Dec 30 16:13 /app/var/tailwind/v3.4.17/tailwindcss-linux-arm64
root@dfcb4ae20363:/app# /app/var/tailwind/v3.4.17/tailwindcss-linux-arm64 --help


mv /app/var/tailwind/v3.4.17/tailwindcss-linux-arm64 tailwindcss
root@dfcb4ae20363:/app# php bin/console tailwind:build 

root@7963163b3dbc:/app# php bin/console asset-map:compile


# Symfony Docker

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework,
with [FrankenPHP](https://frankenphp.dev) and [Caddy](https://caddyserver.com/) inside!

![CI](https://github.com/dunglas/symfony-docker/workflows/CI/badge.svg)

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up --pull always -d --wait` to set up and start a fresh Symfony project
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Features

* Production, development and CI ready
* Just 1 service by default
* Blazing-fast performance thanks to [the worker mode of FrankenPHP](https://github.com/dunglas/frankenphp/blob/main/docs/worker.md) (automatically enabled in prod mode)
* [Installation of extra Docker Compose services](docs/extra-services.md) with Symfony Flex
* Automatic HTTPS (in dev and prod)
* HTTP/3 and [Early Hints](https://symfony.com/blog/new-in-symfony-6-3-early-hints) support
* Real-time messaging thanks to a built-in [Mercure hub](https://symfony.com/doc/current/mercure.html)
* [Vulcain](https://vulcain.rocks) support
* Native [XDebug](docs/xdebug.md) integration
* Super-readable configuration

**Enjoy!**

## Docs

1. [Options available](docs/options.md)
2. [Using Symfony Docker with an existing project](docs/existing-project.md)
3. [Support for extra services](docs/extra-services.md)
4. [Deploying in production](docs/production.md)
5. [Debugging with Xdebug](docs/xdebug.md)
6. [TLS Certificates](docs/tls.md)
7. [Using MySQL instead of PostgreSQL](docs/mysql.md)
8. [Using Alpine Linux instead of Debian](docs/alpine.md)
9. [Using a Makefile](docs/makefile.md)
10. [Updating the template](docs/updating.md)
11. [Troubleshooting](docs/troubleshooting.md)

## License

Symfony Docker is available under the MIT License.

## Credits

Created by [Kévin Dunglas](https://dunglas.dev), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).
