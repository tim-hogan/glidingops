# Title

A bunch of tools to assist gliding operations.

Based on original by Tim Hogan.

## How to install

## How to configure the database connections for production deployment
Due to the mixed `vanilla php`/`laravel` nature of the project, we need to configure the database parameters in two places. 
One is the conventional `laravel` `.env` file that has to be located at `glidingops/lrv/.env`.
```bash
APP_ENV=development

DB_HOST=localhost
DB_PORT=3306

DB_DATABASE=gliding
DB_USERNAME=homestead
DB_PASSWORD=secret

DB_TRACKS_DATABASE=tracks
DB_TRACKS_USERNAME=homestead
DB_TRACKS_PASSWORD=secret

APP_KEY=base64:m8XNVK0wYvRJoDLfIcBYuK+/vZdmTP2+g8A1dPOOEUc=
APP_DEBUG=true
```

The second one is `glidingops`' custom database configuration file that needs to be located at `glidingops/config/database.php`
```bash
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
Please note that `glidingops/lrv/.env` and `glidingops/config/database.php` are not tracked in git because they contain real credentials for the database connection.

Make sure mysql *STRICT_MODE* is not enabled (*STRICT_TRANS_TABLES* nor *STRICT_ALL_TABLES*). Please see https://dev.mysql.com/doc/refman/5.7/en/sql-mode.html#sql-mode-strict

To list the currently enabled modes use
```sql
SELECT @@sql_mode;
```

## How to contribute

## How to configure your development environment
First you'll have to checkout the project from github. It comes configured with [Laravel Homestead](https://laravel.com/docs/5.8/homestead) for ease of spining up a confined development environment.

[Laravel Homestead](https://laravel.com/docs/5.8/homestead) uses [Vagrant](https://www.vagrantup.com/) to build and provision a virtual machine with all the packages necessary for running the project on your local machine (php5.6, mysql, apache etc.).

Before you move on, you'll have to make sure you have [VirtualBox](https://www.virtualbox.org/wiki/Downloads) installed on your development machine. VirtualBox is our default choice for running the aformentioned virtual machine. If you do not fency VirtualBox, you can use any of the Vagrant's [supported providers](https://www.vagrantup.com/docs/providers/). Just edit `glidingops/lrv/Homestead.yaml` and specify your provider of choice.

After you've checked out the project, change current folder to `lrv`. All commands for setting up the environment will be run from this folder.
```bash
cd glidingops/lrv
```

Build up and provision the virtual box.
```bash
vagrant up
```

This will configure and build a VM image and at the end of the process will crate and seed a test database to be used during development.

:warning: This process will overwrite `lrv/.env` and `config/database.php`. If already present make sure you have a backup copy.

You might see a couple of errors related to php7.1 failing to be properly configured. You can safely ignore them as the project is using the older php5.6.

The project's root `glidingops` from your host machine is automatically mapped under the `code` folder on the virtual machine. You can use your editor of choice on your host machine to edit any of the source files and as soon as you save, the changes will be synced with the virtual macine. 

From now on, all `laravel` and `php` commands should be run from inside the virtual machine. You can ssh into it using vagrant.
```bash
vagrant ssh
```

You shold be able to visit https://glidingops.test and login using Username: **fgordon** and Password: **fgordon**. Because in development mode we are using a self signed certificate, your server will give you a warning. You can safely add an exception for this certificate and instruct your browser to trust it.

If your browser complains that it can not find https://glidingops.test then check your `hosts` file and make sure you have an entry that reads
```bash
192.168.10.10   glidingops.test
```