<?php

include './Traits/session.php';
include '../Model/autoload.php';
class tagController{

    public function controllerFunction($command){
        $tagController = new tagController();
        switch ($command) {
            case 'insert': $tagController->insertTag(); break;
            case 'update': $tagController->updateTag(); break;
            case 'delete': $tagController->deleteTag(); break;
            case 'getOne': $tagController->getOneTag(); break;
            case 'getAll': $tagController->getAllTag(); break;
        }
    }

    public function insertTag(){
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tag = new Tag();
            $tag->setTitle($_POST['title']);
            $tag->setColor($_POST['color']);
            $tag->setId_user($_SESSION['id_user']);
            $tag->insert();

            echo json_encode($tag);
        }
    }

    public function updateTag(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tag = new Tag();
            $tag->setId_tag($_POST['id_tag']);
            $tag->setTitle($_POST['title']);
            $tag->setColor($_POST['color']);
            $tag->setId_user($_SESSION['id_user']);
            $tag->update();

            echo json_encode($tag);
        }

    }

    public function deleteTag(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Tag::delete($_POST['id_tag'], $_SESSION['id_user']);
        }

    }

    public function getOneTag(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tag = new Tag();
            $tagInfo = $tag::getOne($_POST['id_tag'], $_SESSION['id_user']); 
            if ($tagInfo) {
                $response = array(
                    'id_tag' => $tagInfo['id_tag'],
                    'title' => $tagInfo['title'],
                    'color' => $tagInfo['color']
                );
                echo json_encode($response);
            } else {
                http_response_code(404);
                echo json_encode(array('error' => 'Não foi encontrado uma etiqueta com este ID.'));
            }
        }
    }

    public function getAllTag(){
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            echo json_encode(Tag::getAll($_SESSION['id_user']));
            // Regenera o ID da sessão
            session_regenerate_id();
        }

    }
}

$tagController = new tagController();
$command = $_GET['command'];
$tagController->controllerFunction($command);

?>