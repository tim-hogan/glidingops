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

Make sure mysql *STRICT_MODE* is not enabled (*STRICT_TRANS_TABLES* nor *STRICT_ALL_TABLES*). Please see https://dev.mysql.com/doc/refman/5.7/en/sql-mode.html#sql-mode-strict

To list the currently enabled modes use
```sql
SELECT @@sql_mode;
```

## How to contribute
