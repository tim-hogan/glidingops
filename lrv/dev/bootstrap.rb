lrvFolder = File.expand_path("../", File.dirname(__FILE__))
dotEnvFile = File.expand_path(".env", lrvFolder)
File.open(dotEnvFile, 'w') do |f|
    f.write(
<<-ENV
APP_ENV=development

DATABASE_HOST=localhost
DATABASE_PORT=3306

DATABASE_NAME=gliding
DATABASE_USER=homestead
DATABASE_PW=secret

TRACKS_DATABASE_NAME=tracks
TRACKS_DATABASE_USER=homestead
TRACKS_DATABASE_PW=secret

APP_KEY=base64:m8XNVK0wYvRJoDLfIcBYuK+/vZdmTP2+g8A1dPOOEUc=
APP_DEBUG=true
ENV
)
end
		
rootFolder = File.expand_path("../../", File.dirname(__FILE__))
phpInfoFile = File.expand_path("phpinfo.php", rootFolder)
File.open(phpInfoFile, 'w') do |f|
    f.write(
<<-PHPNFO
<?php
  require_once "./includes/moduleEnvironment.php";
  $env = $devt_environment->getkey('APP_ENV');  
  if($env == 'development') { 
	phpinfo(INFO_ALL);
  }
?>
PHPNFO
)
end