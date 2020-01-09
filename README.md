# Symfony Forms Playground: Splitting send and receive into two actions

This is a playground project where I aim to split sending and receiving
a form into separate GET and POST actions.

For setup, just clone and run `composer install`. The questions about
`parameters.yml` values can all be answered with their defaults. Then

``` bash
php bin/console doctrine:database:create
php bin/console doctrine:schema:create
```

If you don't want to setup a server to test this, `bin/console server:run` will
do just fine.

You need to have `php-sqlite` or the equivalent package for the sqlite
extension on your system.
