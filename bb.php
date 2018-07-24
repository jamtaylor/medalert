
<?php

require __DIR__ . '/twilio-php-master/Twilio/autoload.php';
use Twilio\Rest\Client;

$ip = (string)$_SERVER['REMOTE_ADDR'];
$cmd = $_REQUEST['cmd'];
$val = $_REQUEST['val'];

date_default_timezone_set('America/New_York');
$date = date("Y-m-d H:i:s");

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'mFE8tfD1k0a2';
$dbname = 'ERB';

function updateIPAddress($ip, $val, $db) {
	$sql = "UPDATE Button SET currentIPAddress = ? WHERE id = ?";

 	$db->prepare($sql)->execute([$ip, $val]);
}





//MAKE IT ONLY SEND TO ADMINISTRATORS
function sendTextMessage($loc, $db) {

	$sql = 'SELECT ResponderID, FirstName, LastName, PhoneNumber, Admin FROM Responders';
	$stmt = $db->query($sql);

	$people = array();

	foreach($stmt as $row) {
		if ($row['Admin'] == "1") {
			$people[$row['PhoneNumber']] = $row['FirstName'] . $row['LastName'];
		}
	}

	$AccountSid = "AC60a5db0940afdf0eaf0cc23f146b2d1f";
	$AuthToken  = "7fcb1889fe647ad3ee31c16fbd1f0e1c";

	$client = new Client($AccountSid, $AuthToken);

	foreach ($people as $number => $name) {

		$sms = $client->account->messages->create(

				$number,

				array(
					'from' => "19193240947",
					'body' => "Emergency at " . $loc . "!"
				)

		);

	}

}





function sendNotification($loc){
		//This will likely change
		$content = array(
			"en" => 'Emergency at ' . $loc . '! Please get there as soon as possible. Click notification for the map.'
			);

		$fields = array(
			'app_id' => "0f728ea6-249c-4f7b-ad9a-028dd151f176",
			'included_segments' => array("onCampusAndAvailable"),
			'contents' => $content,
			'url' => "https://www.caryacademy.org/uploaded/CA_Images/campus_map.jpg", //This really needs fixing. LOLS Maybe switch it to data from onesignal
			'priority' => 10
		);

		$fields = json_encode($fields);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic MjcwOTgzZDYtNzI1Zi00MDg1LTgwMmEtYzA3ZjQwMGVjOGFi'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}






function takeAction($dataTable, $ip, $val, $dbhost, $dbname, $dbuser, $dbpass, $date) {

	echo "Got $dataTable command from $val";

	$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);

  $sql = "INSERT INTO $dataTable (id, time) VALUES (?, ?)";
  $db->prepare($sql)->execute([$val, $date]);

  updateIPAddress($ip, $val, $db);

  return $db;

}

if($cmd == '0') {

 	$db = takeAction("KeepPresses",$ip, $val, $dbhost, $dbname, $dbuser, $dbpass, $date, $date);

	$button_locations = array(
		"Berger Hall",
		"Dining Hall",
		"Fitness Center",
		"Library and Administration Building",
		"Upper School",
		"Middle School",
		"Baseball Field",
		"Field-house by Track and Tennis Courts",
		"Lower Fields",
		"Sports Education Annex (SEA)",
		"Upper School Field"
	);

	$active_button_location = $button_locations[((int)$val - 1)];
	sendTextMessage($active_button_location, $db);
	$response = sendNotification($active_button_location);


} elseif($cmd == '1') {

  $db = takeAction("KeepAlives", $ip, $val, $dbhost, $dbname, $dbuser, $dbpass, $date, $date);

} else {

 	echo "Got unknown command " . $cmd;

}
