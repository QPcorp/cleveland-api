<?php

/*
 *
 * Functions Setup
 * 
*/


function returnResult($action, $success = true, $msg){
	$habit = array('action' => $action,'success' => $success,'msg' => $msg);
    echo json_encode($habit);
}