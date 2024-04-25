<?php 
session_start();
require_once('classes/actions.class.php');
$actionClass = new Actions();
$action = $_GET['action'] ?? "";
$response = [];
switch($action){
    case 'save_mdt':
        $response = $actionClass->save_mdt();
        break;
    case 'delete_mdt':
        $response = $actionClass->delete_mdt();
        break;
    case 'save_member':
        $response = $actionClass->save_member();
        break;
    case 'delete_member':
        $response = $actionClass->delete_member();
        break;
    case 'save_attendance':
        $response = $actionClass->save_attendance();
        break;
    default:
        $response = ["status" => "error", "msg" => "Undefined API Action!"];
        break;
}

echo json_encode($response);
?>