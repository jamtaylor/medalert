
<?php

  $val = $_REQUEST['val'];
  $uval = $_REQUEST['uval'];

  $dbhost = 'localhost';
  $dbuser = 'root';
  $dbpass = 'mFE8tfD1k0a2';
  $dbname = 'ERB';

  $db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);

  function validateButton($id, $uniqueid, $db) {
    $buttonCleared = False;

    $sql = 'SELECT id, uniqueid FROM Button';
    $stmt = $db->query($sql);

    foreach ($stmt as $row) {
      if (($id == $row['id']) && ($uniqueid == $row['uniqueid'])) {
        $buttonCleared = True;
      }
    }
    if (!$buttonCleared) {
      exit();
    }
  }

  validateButton($val, $uval, $db);

  $sql = 'SELECT id, config FROM ButtonConfigs';
  $config_text = 'no worked';

  $stmt = $db->query($sql);
  foreach ($stmt as $row) {
    if ($val == $row['id']) {
      $config_text = $row['config'];
    }
  }

  $config_json = json_decode($config_text);

  echo $config_text;

?>
