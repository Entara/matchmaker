<?php

header("Access-Control-Allow-Origin: *");

class MyDB extends SQLite3{
    function __construct(){
        $this->open("matchmaker.sqlite");
    }
}

function connect_to_db() {
    $db = new MyDB();
    if(!$db){
        echo $db->lastErrorMsg();
    }
    return $db;
}

function return_error() {
    header($_SERVER["SERVER_PROTOCOL"] . ' 500 Server Error');
    header('Status:  500 Server Error');
}

function return_ok() {
    header($_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
    header('Status:  200 OK');
}

function return_json($json_object) {
    print_r($json_object);
    header('Content-Type: application/json; charset=utf8');
    header($_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
    header('Status:  200 OK');
}

function addDecisionToDB($name, $decision, $timestamp) {

}

// add decision
if(isset($_POST["name"]) && isset($_POST["decision"])) {
    try {
        $db = new MyDB();
        // connect
        if(!$db){
            echo $db->lastErrorMsg();
        }

        $name = $_POST["name"];
        $timestamp = date('Y-m-d H:i:s');
        $decision = $_POST["decision"];

        // add to db
        $sql = "INSERT INTO data ('name', 'timestamp', 'decision') VALUES ('".$name."', '".$timestamp."', '".$decision."');";
        $ret = $db->exec($sql);
        if(!$ret){
            echo $db->lastErrorMsg();
        }

        $db->close();

        // send ok if successfull
        header($_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
        header('Status:  200 OK');

    } catch (Exception $e) {
        return_error();
    }

// check for match
} elseif(isset($_POST["check"])) {
    try {

        $db = new MyDB();
        // connect
        if(!$db){
            echo $db->lastErrorMsg();
        }

        // get results and check for timestamp
        $current_timestamp = date('Y-m-d H:i:s');
        //$valid_timestep = $current_timestamp - (15 * 60); // -15 min
        $valid_timestep = date('Y-m-d H:i:s', strtotime("-15 minutes")); // -15 min

        $results = array();
        // select unique names -> if we use IDs, multiple entries by same name would count multiple
        // currently only one name, thats why we have to select ID, change that later
        $sql = "SELECT COUNT(id) AS match_count from data WHERE timestamp >='".$valid_timestep."';";
        $query_result = $db->query($sql);
        while($row = $query_result->fetchArray(SQLITE3_ASSOC) ){
            $results['matches'] = $row['match_count'];
        }
        $db->close();

        // send ok and JSON
        $results['now'] = $current_timestamp;
        $results['valid'] = $valid_timestep;
        return_json(json_encode($results));

    } catch (Exception $e) {
        return_error();
    }

} else {
    return_error();
}

?>
