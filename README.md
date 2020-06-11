# Server Status Page
This repository holds a simple web page that shows the status of a couple game servers I maintain. If the game supports it the page outputs the following information:
* Online status of the server
* Current and maximum player count
* Current map
* Address of the server to connect to
* A link to join the game
* A link with more details about the game

The page consists of a frontend using [Angular.js](https://github.com/angular/angular.js) and [Bootstrap](https://github.com/twbs/bootstrap).  
It uses a [PHP](https://github.com/php/php-src) and [GameQ](https://github.com/Austinb/GameQ/wiki/Configuration-v3) backed JSON API to query status information.

A slideshow with screenshots of the games is played in the background of the page.

## Configuration
### servers.json
This file is the configuration file as accepted by GameQ's function addServersFromFiles(). See [GameQ's documentation](https://github.com/Austinb/GameQ/wiki/Configuration-v3) for details.
### overrides.json
This file holds overrides allowing to customize the JSON data the PHP script will send to the client, i.e. the name of a game or a URL for more information. If a game isn't supported by GameQ it can be added to this file to show basic information about it. 

These are the fields that the JSON API outputs and can be overriden:
* `id`: ID of the entry these overrides should apply to
* `name`: Name of the game to display
* `detailslink`: Link to put on the game's name in the header
* `online`: Whether or not the server is online
* `num_players`: The current amount of players connected to the server
* `max_players`: The maximum amount of players that connect to the server
* `map`: Current map running on the server
* `address`: Address the player has to enter to connect to the server
* `_resolveAddress`: If the game doesn't support entering hostnames, set this to `true` to have the PHP script resolve the hostname to an IP address. This property will be removed from the output.
* `preferredOrder`: Number to sort the entry in the list by
* `joinlink`: Link to put on the join button. Set to `null` if GameQ outputs a link to remove it.

### Notes
For the background slideshow a list of images is currently hardcoded in `app.js`.