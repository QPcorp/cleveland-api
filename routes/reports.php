<?php

/*
 *
 * Reports Route Setup
 * Route: /reports
 * Level: 1 + 
 *
*/

$db = connect_db();

//Get a list of customers
$app->get('/reports/location/:id/:start_time/:end_time', function ($id, $start_time, $end_time) use ($db, $app) {
    

    $start_time = DATE("Y-m-d H:i:s",$start_time);
    $end_time = DATE("Y-m-d H:i:s",$end_time);

    $sth = $db->prepare('SELECT * FROM sessions WHERE location_id = '.$id.' and session_date > "'.$start_time.'" and session_date < "'.$end_time.'";');
    $sth->execute();
    //echo '{"test":"hello"}';
    echo json_encode(array("start_date"=>$start_time, "end_date"=>$end_time, "value"=>$sth->fetchAll(PDO::FETCH_CLASS)));
});


//Get data about 
$app->post('/report/customer/recent/:count', function ($count) use ($db, $app) {
    
    if(!$count){
        $count = 999999;
    }

    //Get Request
    $body = $app->request->getBody();
    $data = json_decode($body);

    //Values Setup
    $email = $data->email;
    $location = $data->location_id;

    $user_info = $db->query('SELECT * FROM users WHERE email = "'. $email .'" LIMIT 1;');
    $user = $user_info->fetchAll(PDO::FETCH_CLASS);

    if(count($user) < 1){
        echo '{"error":true, "msg":"No User with this email exists"}';
    } else {
        $user = array_shift($user);
        $user_id = $user->id;

        $session_info = $db->query('SELECT * FROM user_balance_log WHERE user_id = '.$user_id.' LIMIT '. $count .';');
        $sessions = $session_info->fetchAll(PDO::FETCH_CLASS);

        if(count($sessions) < 1){
            echo '{"error":true, "msg":"No sessions for this user"}';
        } else {
            echo json_encode($sessions);
        }
    }

});




