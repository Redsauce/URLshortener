<?php

require_once "RSMDB.php";

if ($_REQUEST['url']) {
    global $db;
    $db = new RSMDB();
    $db->SetClient("mongodb://mongo:27017");
    $db->SetDatabase("Shortener");
    $db->SetCollection("URLs");
    $id = get_unique_short_id(10);
    $db->Insert([["short" => $id, "url" => $_REQUEST['url']]]);
    echo $id;
} else {
    echo "Y U NO URL";
}


function get_unique_short_id($size){
    $valid_bytes = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $found = false;
    while ($found == false) {
        $res = "";
        for($i = 0; $i < $size; $i++){
            $res .= $valid_bytes[random_int(0,strlen($valid_bytes))];
        }
        if (!entry_exists($res)) {
            $found = true;
        }
    }
    return $res;
}

function entry_exists($id){
    global $db;
    $res = $db->Query(["short" => $id]);
    $count = 0;
    foreach ($res as $entry){
        $count++;
    }
    return $count > 0;
}


?>
