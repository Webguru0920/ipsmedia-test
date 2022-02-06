# ipsmedia test

Customers access their purchased courses via our Course Portal.
As part of this experience users are able to unlock achievements:

## Requriements

-   PHP ^7.3|^8.0
-   composer 2.2+

## Installation

-   From the root directory run `composer install`
-   You must have a MySql database running locally
-   Update the database details in `.env` to match your local setup
-   Run `php artisan key:generate`
-   Run `php artisan migrate` to setup the database tables
-   Run `php artisan db:seed` to seed data

## Testing

-   The `.env.testing` file should be created for testing.
-   Create new database for testing and replace database name on `.env.testing` file and run `php artisan migrate --env=testing`.
-   Artisan commands should be run with the `--env=testing` option as well.
-   run `php artisan test` on the command line to run your tests.
