# Gliding Class
## Include File
GlidingClass.php
## Constructor
Gliding(*gliding ops host name* , *gnz host name = null*);
### Parameters
*gliding ops host name*  
The name of the gliding ops host.  Currenlty this is glidngops.com  
*gnz host name* (Optional)  
Optional host name of the Gliding New Zealand 
## Methods
### getFlyingToday($club)
#### Parameters
*$club* [Integer]  
The index of the club 
#### Result
A sucessful result is a php array of JSON elements with the following format.  
```php  
{
"flying":
    [
        {
            "seq":10,
            "glider":"GGG",
            "pic":"Name 1",
            "p2":"Name 2",
            "flighttime":"01:23"
        },
        {
            "seq":12,
            "glider":"GGH",
            "pic":"Name 3",
            "p2":"",
            "flighttime":"00:47"
         }
    ],
"completed":
    [
        {
            "seq":3,
            "glider":"GGI",
            "pic":"Name 1",
            "p2":"Name 2",
            "flighttime":"02:12"
        }
    ]
}
```
## Examples
```php
<?php
require 'GlidingClass.php'
$club = 1;
$myGlide = new Gliding('glidingops.com','www.gliding.net.nz');
$result = $myGlide->getFlyingToday($club);
if (!in_array('error',result))
{
    $flying_flights = result['flying'];
    $completed_flights = result['completed'];
    
    foreach ($flying_flights as $flight) {
        echo $flight['seq'] ." ," . $flight['pic'] ." ," . $flight['p2']  ." ," . $flight['flighttime'];
    }
    ........
    
}
?>
```
