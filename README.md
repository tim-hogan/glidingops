# Title

A bunch of tools to assist gliding operations.

Based on original by Tim Hogan.

## How to install

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
