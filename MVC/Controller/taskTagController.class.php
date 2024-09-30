<?php

include './Traits/session.php';
include '../Model/autoload.php';
include '../Model/Classes/taskTag.class.php';

class taskTagController{

    public function controllerFunction($command){
        $taskTagController = new taskTagController();
        switch ($command) {
            case 'insert': $taskTagController->insertTaskTag(); break;
            case 'update': $taskTagController->updateTaskTag(); break;
            case 'delete': $taskTagController->deleteTaskTag(); break;
            case 'getAll': $taskTagController->getAllTaskTagbyTask(); break;
            case 'getAllbyTag': $taskTagController->getAllTaskTagbyTag(); break;
        }
    }

    public function insertTaskTag(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskTag = new TaskTag();
            $taskTag->setId_task($_POST['id_task']);
            $taskTag->setId_tag($_POST['id_tag']);
            $taskTag->insert();

            echo json_encode($taskTag);
    }
    }

    public function updateTaskTag(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskTag = new TaskTag();
            $taskTag->setId_task($_POST['id_task']);
            $taskTag->setId_tag($_POST['id_tag']);
            $taskTag->update();
        }

    }

    public function deleteTaskTag(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo($_POST['id_task'] . "-" . $_POST['id_tag']);
            TaskTag::delete($_POST['id_task'], $_POST['id_tag']);
        }

    }

    /*public function getOneTag(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_tag = $_POST['id_tag'];
            $tag = new Tag();
            $tagInfo = $tag::getOne($id_tag); 
            if ($tagInfo) {
                $response = array(
                    'title' => $tagInfo['title'],
                    'color' => $tagInfo['color']
                );
                echo json_encode($response);
            } else {
                // Usuário não encontrado no banco de dados
                http_response_code(404);
                echo json_encode(array('error' => 'Usuário não encontrado'));
            }
        }
    }*/

    public function getAllTaskTagbyTask(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo json_encode(TaskTag::getAllbyTask($_POST['id_task']));
        }
    }

    public function getAllTaskTagbyTag(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo json_encode(TaskTag::getAllbyTag($_POST['id_tag']));
        }
    }
}

$taskTagController = new taskTagController();
$command = $_GET['command'];
$taskTagController->controllerFunction($command);

?>