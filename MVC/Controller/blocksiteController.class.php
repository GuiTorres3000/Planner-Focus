<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

include './Traits/session.php';
include '../Model/autoload.php';

class blocksiteController{

    public function controllerFunction($command){
        $blocksiteController = new blocksiteController();
        switch ($command) {
            case 'insert': $blocksiteController->insertBlocksite(); break;
            case 'update': $blocksiteController->updateBlocksite(); break;
            case 'delete': $blocksiteController->deleteBlocksite(); break;
            case 'getOne': $blocksiteController->getOneBlocksite(); break;
            case 'getAllbyCategory': $blocksiteController->getAllBlocksitebyCategory(); break;
        }
    }
    
    public function insertBlocksite(){
            
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $blocksite = new Blocksite();
            $blocksite->setUrl($_POST['url']);
            $blocksite->setId_category($_POST['id_category']);
            $blocksite->setId_user($_SESSION['id_user']);
            $blocksite->insert();

            echo json_encode($blocksite);
        }
    }
    
    public function updateBlocksite(){
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $blocksite = new Blocksite();
            $blocksite->setId_blocksite($_POST['id_blocksite']);
            $blocksite->setUrl($_POST['url']);
            $blocksite->setId_category($_POST['id_category']);
            $blocksite->setId_user($_SESSION['id_user']);
            $blocksite->update();
    
            echo json_encode($blocksite);
        }
    }
    
    public function deleteBlocksite(){
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Blocksite::delete($_POST['id_blocksite'], $_SESSION['id_user']);
        }
    
    }
    
    public function getOneBlocksite(){
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $blocksite = new Blocksite();
            $blocksiteInfo = $blocksite::getOne($_POST['id_blocksite'], $_SESSION['id_user']); 
            if ($blocksiteInfo) {
                $response = array(
                    'id_blocksite' => $blocksiteInfo['id_blocksite'],
                    'url' => $blocksiteInfo['url'],
                );
                echo json_encode($response);
            } else {
                http_response_code(404);
                echo json_encode(array('error' => 'Não foi encontrado uma lista de bloqueio com este ID.'));
            }
        }
    }
    
    public function getAllBlocksitebyCategory(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo json_encode(Blocksite::getAllbyCategory($_POST['id_category'], $_SESSION['id_user']));
            // Regenera o ID da sessão
            session_regenerate_id();
        }
    }
}
    
$blocksiteController = new blocksiteController();
$command = $_GET['command'];
$blocksiteController->controllerFunction($command);
?>    