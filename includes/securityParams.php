<?php
define('SECURITY_NONE','0');				 //0000 0000 0000   Dec 1  = (2^x) -1
define('SECURITY_MEMBER','1');				 //0000 0000 0001   Dec 1  = (2^x) -1
define('SECURITY_BOOKING_ADMIN','2');        //0000 0000 0010   Dec 1  = (2^x) -1
define('SECURITY_DAILY_OPS','4');            //0000 0000 0100   Dec 1  = (2^x) -1
define('SECUIRTY_CFO','8');					 //0000 0000 1000   Dec 1  = (2^x) -1
define('SECURITY_CFI','16');				 //0000 0001 0000   Dec 1  = (2^x) -1
define('SECURITY_ADMIN','64');				 //0000 0010 0000   Dec 1  = (2^x) -1
define('SECURITY_GOD','128');				 //0000 0100 0000   Dec 1  = (2^x) -1

define('MAX_USERNAME_ATTEMPS','10');
?>