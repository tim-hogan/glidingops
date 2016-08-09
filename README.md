# Title

A bunch of tools to assist gliding operations.

Original project author Tim Hogan.

## How to install

### Composer
This project uses [composer](https://getcomposer.org) to manage its dependencies. To install it, follow the steps outlined in [installing composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

After you've installed [composer](https://getcomposer.org), run the following command from inside the project's root folder:
```shell
composer install
```
This will download all the project's dependencies into the `vendor` folder under the project's root. All required dependencies are specified in the `composer.json` file.

## How to configure the database connections
Create a `config/database.php` file with the following structure  
```php
<?php
return [
    'gliding' => [
        'username' => 'admin',
        'password' => '****',
        'hostname' => 'localhost',
        'dbname' => 'gliding'
    ],
    'tracks' => [
        'username' => 'track',
        'password' => '****',
        'hostname' => 'localhost',
        'dbname' => 'tracks'
    ]
];
?>
```

## How to contribute
