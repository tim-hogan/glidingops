<!-- dev mode warning -->
<?php
  $env = getenv('APP_ENV');  
?>

<?php if($env == 'development') { ?>
  <div style='width: 100%;background-color: red;color: white;font-weight: bolder;font-size: 20px;text-align: center;'>
    DEVELOPMENT MODE - <a href="phpinfo.php">PHP Info</a>
  </div>
<?php } ?>