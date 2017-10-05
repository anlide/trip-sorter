# Trip sorter
Find best way between two location via 2 search algorithm.

# Source code base
https://github.com/anlide/trip-sorter

# Check alive
Please use for check how this code work online:<br/>
http://propertyfinder.allod.ws/

# Usage - get cities
http://propertyfinder.allod.ws/?api=city
* No params
* Response format:
{"cities":["Amsterdam","London"]}

# Usage - get transports
http://propertyfinder.allod.ws/?api=transport
* No params
* Response format:
{"transports":["train","bus"]}

# Usage - find path
http://propertyfinder.local/?api=findpath&departure=Amsterdam&arrival=London&algorithm=cheapest
* Param 1: departure - city name
* Param 2: arrival - city name
* Param 3: algorithm - cheapest or fastest
* Response format:
{"deals":[{"transport":"bus","departure":"Amsterdam","arrival":"Warsaw","duration":315,"cost":40,"discount":25,"reference":"BAW0515"},{"transport":"bus","departure":"Warsaw","arrival":"Stockholm","duration":315,"cost":40,"discount":50,"reference":"BWS0515"},{"transport":"car","departure":"Stockholm","arrival":"Moscow","duration":240,"cost":120,"discount":0,"reference":"CSM0400"}]}

# Notes
Usually for API we should use REST methods.
It mean that find-path should be via POST.
