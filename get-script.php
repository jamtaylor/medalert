
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

  $sql = 'SELECT id, needScriptUpdate FROM Button';
  $stmt = $db->query($sql);

  $needScriptUpdate = False;
  foreach ($stmt as $row) {
    if (($row['id'] == $val) && ($row['needScriptUpdate'] == 'y')) {
      $needScriptUpdate = True;
      $sql = "UPDATE Button SET needScriptUpdate = 'n' WHERE id = ?";
      $db->prepare($sql)->execute([$val]);
      break;
    }
  }

  if ($needScriptUpdate) {
    echo readfile("dev-control-panel/mainprogram.py");
  } else {
    echo 'false';
  }
?>
