lrvFolder = File.expand_path("../", File.dirname(__FILE__))
dotEnvFile = File.expand_path(".env", lrvFolder)
File.open(dotEnvFile, 'w') do |f|
    f.write(
<<-ENV
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
ENV
        )
end

legacyDatabaseCfgFile = File.expand_path('../config/database.php', lrvFolder)
File.open(legacyDatabaseCfgFile, 'w') do |f|
    f.write(
<<-DATABASE_PHP
<?php
return [
    'gliding' => [
        'username' => 'homestead',
        'password' => 'secret',
        'hostname' => 'localhost',
        'dbname' => 'gliding'
    ],
    'tracks' => [
        'username' => 'homestead',
        'password' => 'secret',
        'hostname' => 'localhost',
        'dbname' => 'tracks'
    ]
];
?>
DATABASE_PHP
        )
end