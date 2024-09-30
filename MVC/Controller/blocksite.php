<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
include '../Model/autoload.php';

$id_user = $_GET['id_user'];

echo json_encode(Blocksite::getAll($id_user));