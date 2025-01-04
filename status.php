<?php
//ini_set('display_errors', 1);

// A function to find the key of our entry in the array by its id value
function array_find_key_by_property($value, $property, $array) {
	foreach($array as $key => &$entry) {
		if(array_key_exists($property, $entry) && $entry[$property] === $value) {
			return $key;
		}
	}

	return null;
}

try {

// Load the library to query the servers
require_once('GameQ/Autoloader.php');

// Load the server list to query from a file and perform the queries
$GameQ = new \GameQ\GameQ();
$servers = json_decode(file_get_contents('servers.json'), true);
$GameQ->addServers($servers);
$results = $GameQ->process();

// Now sanitize the output to fill in just the data we need to send to the client
$games = [];
foreach($results as $key => $value) {
	// Create a list of players with their data sanitized
	$players = [];
	foreach($value['players'] as $player) {
		$players[] = [
			'name' => $player['gq_name'],
		];
	}
	
	// find the key of this server in the input array
	$serversKey = array_find_key_by_property($key, 'id', $servers);

	// Sanitize data about the game instance and add it to the array
	$games[] = [
		'id' => $key,
		'name' => $value['gq_name'],
		'online' => $value['gq_online'],
		'max_players' => is_numeric($value['gq_maxplayers']) ? intval($value['gq_maxplayers']) : $value['gq_maxplayers'],
		'num_players' => is_numeric($value['gq_numplayers']) ? intval($value['gq_numplayers']) : $value['gq_numplayers'],
		'players' => $players,
		'map' => $value['gq_mapname'],
		// hide empty join links GameQ might return
		'joinlink' => is_string($value['gq_joinlink']) && strlen($value['gq_joinlink']) > 0 ? $value['gq_joinlink'] : null,
		// take the address from the original input data
		'address' => $servers[$serversKey]['host'],
	];
}

// Now add additional information from another json file to the entries
$overrides = json_decode(file_get_contents('overrides.json'), true);
foreach($overrides as $override) {

	// First find the key of the game this override is for in the global games array
	// If it can't be found create a new entry for it (for appending games where querying isn't available)
	$gameKey = array_find_key_by_property($override['id'], 'id', $games);
	if($gameKey === null) {
		$games[] = [];
		$gameKey = array_key_last($games);
	}

	foreach(array_keys($override) as $key) {
		// Skip _resolveAddress to not add it to the output for the client
		if($key === '_resolveAddress') continue;
		
		$games[$gameKey][$key] = $override[$key];
	}
	
	// Check whether the option is set for this game that the hostname needs to be resolved to an ip address
	// This is relevant if the game does offer entering an ip address to connect to, but not hostnames
	if(array_key_exists('_resolveAddress', $override) && $override['_resolveAddress']) {
		$address = $games[$gameKey]['address'];

		// gethostbyname expects only a hostname and will fail if we have a port as well. 
		// Separate the address parts so we can properly resolve the hostname.
		$addressParts = explode(':', $address);
		$addressParts[0] = gethostbyname($addressParts[0]);
		
		// join them back together
		$games[$gameKey]['address'] = implode(':', $addressParts);
	}
}

// Now send the data to the client
header('Content-Type: application/json');
echo json_encode($games);

} catch (Exception $ex) {
	http_response_code(500);
	header('Content-Type: application/json');
	echo json_encode($ex->getMessage());
}
?>