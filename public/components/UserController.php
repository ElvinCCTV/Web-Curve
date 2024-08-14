<?php
require_once __DIR__ . '/UserModel.php';

header('Content-Type: application/');

$json = file_get_contents('php://input');
$data = json_decode($json, true);
// print_r( $data); die;

$action = isset($_GET['action']) ? $_GET['action'] : '';

$guest = new User();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    if($action == 'signup') {
        $guest->handleSignup($data);

    } elseif ($action == 'login') {
        $guest->handleLogin($data);

    }elseif ($action == 'dashboard') {

        $guest->fetchUsers($data);

    } elseif ($action == 'update'){
        $guest->updateEmail($data);
    }
    else {
        echo json_encode(["success" => false, "message" => "Invalid action"]);

    }
}








