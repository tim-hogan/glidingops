# REST API
Document the REST API to the Gliding Ops System
## Basic API Access
`http://glidingops.com/API/V1/<key>/<command>/<options>`
## Basic parameters
### key
This is a 16 digit key, currently not implemented, you can put any 16 random digits in its place.  At a later dat, however this key could be implemenetd to restrict access to only issued and known keys.
## Returned JSON basic format
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
## COMMANDS
### flyingnow
Method | GET  
-- | --  
Options | Club Identifier  
#### Example
`http://glidingops.com/API/V1/0123456789012345/flyingnow/1`

The above example will return a JSON string with the format:
```php
{
    "meta":
    { ..... },
    "data":
    {
        "flying":
        {
        },
        "completed":
        {
        }
    }
}    
```
