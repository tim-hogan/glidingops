# Gliding Class
## Include File
GlidingClass.php
## Constructor
Gliding(*gliding ops host name*,*key = null*);
### Parameters
*gliding ops host name*  
The name of the gliding ops host.  Currenlty this is glidingops.com  
*key*  
Optional parameter of a 16 digit pre-defined key if required for use of the api. (Currently not implemented)
## Methods
### getFlyingToday($club)
#### Description
Returns a clubs flights for the current day, divided into aircraft that are currently flying and completed flights for the current day.  
#### Parameters
*$club* [Integer]  
The index of the club 
#### Result
A sucessful result is a php array of JSON elements with the following format.  
```php  
{  
    "meta":
    {
        .......
    }
    "data":
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
}
```
## Examples
```php
<?php
require 'GlidingClass.php'
$club = 1;
$myGlide = new Gliding('glidingops.com');
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
