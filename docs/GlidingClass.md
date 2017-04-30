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
'''php  
{"flying":
    {
    }
}
'''
## Examples
