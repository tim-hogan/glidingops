# REST API
Document the REST API to the Gliding Ops System
## Basic API Access
`http://glidingops.com/api/v1/json/<key>/<command>/<options>`
## Basic parameters
### key
This is a 16 digit key, currently not implemented, you can put any 16 random digits in its place.  At a later dat, however this key could be implemenetd to restrict access to only issued and known keys.
## Returned JSON header information
```php
{
    "meta":   
    {  
        "status":"OK",  
        "request":"flyingnow",  
        "time":"2017-05-03T10:23:45",  
        "errorcode":null,  
        "errormsg":null
     },  
    "data":  
     {
        "dataproperty":"A Data Property"
     }  
}`
```
### Header field descriptions
Field | Description
----- | -----------
status | OK or ERROR
request | The request string for this result
time | The ISO Timestmap in UTC of the request
errorcode | Integer error code 
errormsg | Descriptive text of the error message
## COMMANDS
### flyingnow
Method | GET  
-- | --  
Options | Club Identifier  
#### Example
`http://glidingops.com/api/v1/json/0123456789012345/flyingnow/1`

The above example will return a JSON string with the format:
```php
{
    "meta":
    { ..... },
    "data":
    {
        "flying":
        [
            {
                "seq": 2,
                "glider": "GGR",
                "pic": "Joe Bloggs",
                "p2": "Fred Dagg",
                "flighttime": "01:34"
            },
            {
                "seq": 3,
                "glider": "GPJ",
                "pic": "David Glide",
                "p2": "",
                "flighttime": "00:49"
            },
        ],
        "completed":
        [
            {
                "seq": 1,
                "glider": "GTT",
                "pic": "Don Wings",
                "p2": "",
                "flighttime": "02:14"
            }
        }
    }
}    
```
