<?php

include './Traits/session.php';
include '../Model/autoload.php';

class categoryController{

    public function controllerFunction($command){
        $categoryController = new categoryController();
        switch ($command) {
            case 'insert': $categoryController->insertCategory(); break;
            case 'update': $categoryController->updateCategory(); break;
            case 'delete': $categoryController->deleteCategory(); break;
            case 'getOne': $categoryController->getOneCategory(); break;
            case 'getAll': $categoryController->getAllCategory(); break;
        }
    }

    public function insertCategory(){
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category = new Category();
            $category->setTitle($_POST['title']);
            $category->setColor($_POST['color']);
            $category->setId_user($_SESSION['id_user']);
            $category->insert();

            echo json_encode($category);
        }
    }

    public function updateCategory(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category = new Category();
            $category->setId_category($_POST['id_category']);
            $category->setTitle($_POST['title']);
            $category->setColor($_POST['color']);
            $category->setId_user($_SESSION['id_user']);
            $category->update();

            echo json_encode($category);
        }

    }

    public function deleteCategory(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Category::delete($_POST['id_category'], $_SESSION['id_user']);
        }

    }

    public function getOneCategory(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category = new Category();
            $categoryInfo = $category::getOne($_POST['id_category'], $_SESSION['id_user']); 
            if ($categoryInfo) {
                $response = array(
                    'id_category' => $categoryInfo['id_category'],
                    'title' => $categoryInfo['title'],
                    'color' => $categoryInfo['color']
                );
                echo json_encode($response);
            } else {
                http_response_code(404);
                echo json_encode(array('error' => 'Não foi encontrado uma categoria com este ID.'));
            }
        }
    }

    public function getAllCategory(){
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            echo json_encode(Category::getAll($_SESSION['id_user']));
            // Regenera o ID da sessão
            session_regenerate_id();
        }

    }
}

$categoryController = new categoryController();
$command = $_GET['command'];
$categoryController->controllerFunction($command);

?>