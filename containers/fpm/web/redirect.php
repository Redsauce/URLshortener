<?php

require_once "RSMDB.php";

if ($_REQUEST['short']){
    global $db;
    $db = new RSMDB();
    $db->SetClient("mongodb://mongo:27017");
    $db->SetDatabase("Shortener");
    $db->SetCollection("URLs");
    $id = $_REQUEST['short'];
    $res = $db->Query(["short" => $id]);
    $target = "";
    foreach ($res as $entry){
        $target = $entry['url'];
    }
    if ($target != "") {
        header('Location: '.$target);
    } else {
        echo "Y U WRONG URL";
    }
}
?>
