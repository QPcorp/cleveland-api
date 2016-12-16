<?php

/*
 *
 * Groups Route Setup
 * Route: /customers
 * Level: 1 + 
 *
*/

require_once 'services/mysql-connect.php';
$db = connect_db();

//Get a list of customers
$app->get('/memberships', function () use ($db, $app) {
    $sth = $db->query('select * from group_memberships;');
    echo json_encode($sth->fetchAll(PDO::FETCH_CLASS));
});

//Get membership about a single customer
$app->get('/membership/:id', function ($id) use ($db, $app) {
    $sth = $db->query('SELECT * FROM group_memberships WHERE customer_id = '.$id.';');
    echo json_encode($sth->fetchAll(PDO::FETCH_CLASS));
});

//Create a membership
$app->post('/membership', function () use ($db, $app) {
    
    //Get Request
    $body = $app->request->getBody();
    $data = json_decode($body);
    
    //Values Setup
    $user_id = $data->customer_id;
    $group_id = $data->group_id;

    $sth = $db->query('select * from group_memberships where group_id = '.$group_id.' and customer_id = '.$user_id.';');
    $user_groups = $sth->fetchAll(PDO::FETCH_CLASS);

    if(count($user_groups) > 0){
        $msg = "Already a member";
        $sth = false;
        $app->halt(400, '{"error":true,"msg":"Already in Group"}');
    } else {
        try{
            $sth = $db->exec("INSERT INTO group_memberships(customer_id, group_id) VALUES ('{$user_id}', '{$group_id}');");
            $msg = 'Membership Created Successfully: '.$user_id;
        } catch (\PDOException $e) {
            $sth = false;
            $app->halt(400, '{"msg":"Cannot add to Group"}');
        }
    }

    
    returnResult('Membership: '.$email, $sth, $msg);
});


//Delete a Membership
$app->delete('/membership/:id', function ($id) use ($db) {
    $sth = $db->exec('DELETE FROM group_memberships WHERE id = '. $id .';');
    //$sth->exec
    echo '{"success":'.$sth.',"msg": "Membership Removed" }';
    //returnResult('delete', $sth);
});

//Get membership of a group for a particular company
$app->get('/membership/company/:company_id/group/:group_id', function ($company_id, $group_id) use ($db, $app) {
    if($group_id == 0){
        $sth = $db->query('SELECT customers.first_name, customers.last_name, customers.points, groups.group_name from customers INNER JOIN group_memberships ON customers.id = group_memberships.customer_id INNER JOIN groups on groups.id = group_memberships.group_id where groups.company_id = '.$company_id.';');
    } else {
         $sth = $db->query('SELECT customers.first_name, customers.last_name, customers.points, groups.group_name from customers INNER JOIN group_memberships ON customers.id = group_memberships.customer_id INNER JOIN groups on groups.id = group_memberships.group_id where groups.company_id = '.$company_id.' and groups.id = '.$group_id.';');
    }
   
    echo json_encode($sth->fetchAll(PDO::FETCH_CLASS));
});


