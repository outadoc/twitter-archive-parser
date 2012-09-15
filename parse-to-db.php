<?php

//the @username of the user; this should be the one present in the name of the files, e.g: outadoc-tweets.txt
$username = 'outadoc';
//the timezone of the user, to make the timestamps correspond
date_default_timezone_set('Europe/Paris');

//connecting to the sqlite database
$db = new PDO('sqlite:twitter.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//we're deleting all the existent tables before parsing, be careful with your data
$db->exec("DROP TABLE IF EXISTS tweets");
$db->exec("DROP TABLE IF EXISTS user");
$db->exec("DROP TABLE IF EXISTS dms");
$db->exec("DROP TABLE IF EXISTS favorites");
$db->exec("DROP TABLE IF EXISTS followers");
$db->exec("DROP TABLE IF EXISTS following");

//create the tables for the data
$db->exec("CREATE TABLE tweets (id INTEGER PRIMARY KEY, status_id INTEGER, created_at INTEGER, created_via VARCHAR(20), text VARCHAR(140))");
$db->exec("CREATE TABLE user (id INTEGER PRIMARY KEY, user_id INTEGER, created_at INTEGER, updated_at INTEGER, email VARCHAR(80), created_via VARCHAR(20), screen_name VARCHAR(21), time_zone VARCHAR(20))");
$db->exec("CREATE TABLE dms (id INTEGER PRIMARY KEY, sender_id INTEGER, recipient_id INTEGER, text VARCHAR(140), created_at INTEGER)");
$db->exec("CREATE TABLE favorites (id INTEGER PRIMARY KEY, author_name VARCHAR(20), status_id INTEGER)");
$db->exec("CREATE TABLE followers (id INTEGER PRIMARY KEY, username VARCHAR(20))");
$db->exec("CREATE TABLE following (id INTEGER PRIMARY KEY, username VARCHAR(20))");

//function to get the content of a file
function get_file_content($file_suffix) {
	global $username;
	//e.g. archive/outadoc-tweets.txt
	$handle = fopen('archive/' . $username . '-' . $file_suffix . '.txt', 'r');
	
	while(($buffer = fgets($handle)) !== false) {
		//reading all the content, line by line
		$content .= $buffer;
	}
	
	return $content;
}

/* function to parse data from the files formatted this way:
 *
 * *******************
 * lorem: ipsum
 * foo: bar
 * mine: craft
 *
 */
function parse_from_structured_file($file_suffix, $fields_count, $callback) {
	$content = get_file_content($file_suffix);
	$matches = null;
	
	preg_match_all('#\*{20}(\n([^\n\r]*\n?){' . $fields_count . '})#', $content, $matches, PREG_PATTERN_ORDER);
	
	$i = 0;
	foreach($matches[1] as $match) {
		$fields = null;
		$data = null;
		
		preg_match_all('#([a-z_]+)\: ([^\n\r]*)#', $match, $fields);
	
		for($j = 0; $j < count($fields[1]); $j++) {
			$data[$fields[1][$j]] = $fields[2][$j];
		}
		
		$callback($data);
		$i++;
	}
	
	echo 'parsed ' . $file_suffix . PHP_EOL;
}

//function to parse data from the files containing a list of status URLs
function parse_from_listed_urls($file_suffix, $callback) {
	$content = get_file_content($file_suffix);
	$matches = null;
	
	preg_match_all('#https?://twitter\.com/([a-zA-Z0-9]*)/status/([0-9]*)#', $content, $matches);
	
	for($i=0; $i<count($matches[1]); $i++) {
		$callback(array(
			"author_name" => $matches[1][$i],
			"status_id" => $matches[2][$i],
		));
	}
	
	echo 'parsed ' . $file_suffix . PHP_EOL;
}

//function to parse data from the files containing a list of data (e.g. usernames)
function parse_from_nlsv($file_suffix, $callback) {
	$content = get_file_content($file_suffix);
	$data = explode("\n", $content);
	
	foreach($data as $value) {
		if(!empty($value)) {
			$callback($value);
		}
	}
	
	echo 'parsed ' . $file_suffix . PHP_EOL;
}

//starting the parsing of the tweets
parse_from_structured_file('tweets', 5, function($data) {
	global $db;
	$query = $db->prepare('INSERT INTO tweets (status_id, created_at, created_via, text) VALUES (?,?,?,?)');
	$query->execute(array(
		intval($data['status_id']),
		strtotime($data['created_at']),
		$data['created_via'],
		$data['text']
	));
});

//starting the parsing of the user info
parse_from_structured_file('user', 8, function($data) {
	global $db;
	$query = $db->prepare('INSERT INTO user (user_id, created_at, updated_at, email, created_via, screen_name, time_zone) VALUES (?,?,?,?,?,?,?)');
	$query->execute(array(
		intval($data['id']),
		strtotime($data['created_at']),
		strtotime($data['updated_at']),
		$data['email'],
		$data['created_via'],
		$data['screen_name'],
		$data['time_zone']
	));
});

//starting the parsing of the direct messages
parse_from_structured_file('dms', 4, function($data) {
	global $db;
	$query = $db->prepare('INSERT INTO dms (sender_id, recipient_id, text, created_at) VALUES (?,?,?,?)');
	$query->execute(array(
		intval($data['sender_id']),
		intval($data['recipient_id']),
		$data['text'],
		strtotime($data['created_at'])
	));
});

//starting the parsing of the favorite tweets
parse_from_listed_urls('favorites', function($data) {
	global $db;
	$query = $db->prepare('INSERT INTO favorites (author_name, status_id) VALUES (?,?)');
	$query->execute(array(
		$data['author_name'],
		$data['status_id']
	));
});

//starting the parsing of the followers list
parse_from_nlsv('followers', function($data) {
	global $db;
	$query = $db->prepare('INSERT INTO followers (username) VALUES (?)');
	$query->execute(array($data));
});

//starting the parsing of the following list
parse_from_nlsv('following', function($data) {
	global $db;
	$query = $db->prepare('INSERT INTO following (username) VALUES (?)');
	$query->execute(array($data));
});

?>