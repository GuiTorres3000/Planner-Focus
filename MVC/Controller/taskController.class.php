<?php

include './Traits/session.php';
include '../Model/autoload.php';

class taskController{

    public function controllerFunction($command){
        $taskController = new taskController();
        switch ($command) {
            case 'insert': $taskController->insertTask(); break;
            case 'update': $taskController->updateTask(); break;
            case 'delete': $taskController->deleteTask(); break;
            case 'getOne': $taskController->getOneTask(); break;
            case 'getAll': $taskController->getAllTask(); break;
            case 'getAllCalendar': $taskController->getAllCalendar(); break;
        }
    }

    public function insertTask(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task = new Task();
            $task->setTitle($_POST['title']);
            $task->setDate($_POST['date']);
            $task->setTime($_POST['time']);
            $task->setDescription($_POST['description']);
            $task->setId_user($_SESSION['id_user']);
            $task->insert();
            echo json_encode($task);
        }
    }

    public function updateTask(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task = new Task();
            $task->setId_task($_POST['id_task']);
            $task->setTitle($_POST['title']);
            $task->setDate($_POST['date']);
            $task->setTime($_POST['time']);
            $task->setDescription($_POST['description']);
            $task->setId_user($_SESSION['id_user']);
            $task->update();

            echo json_encode($task);
        }
    }

    public function deleteTask(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Task::delete($_POST['id_task'], $_SESSION['id_user']);
        }
    }

    public function getOneTask(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task = new Task();
            $taskInfo = $task::getOne($_POST['id_task'], $_SESSION['id_user']); 
            if ($taskInfo) {
                $response = array(
                    'id_task' => $taskInfo['id_task'],
                    'title' => $taskInfo['title'],
                    'date' => $taskInfo['date'],
                    'time' => $taskInfo['time'],
                    'description' => $taskInfo['description']
                );
                echo json_encode($response);
            } else {
                http_response_code(404);
                echo json_encode(array('error' => 'Não foi encontrado uma tarefa com este ID.'));
            }
        }
    }

    public function getAllTask(){
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            echo json_encode(Task::getAll($_SESSION['id_user']));
            // Regenera o ID da sessão
            session_regenerate_id();
        }

    }

    public function getAllCalendar(){
        
            $task = new Task();
            $taskInfoList = $task::getAll($_SESSION['id_user']);

            $response = array();
            foreach ($taskInfoList as $taskInfo) {
                $taskArray = array(
                    'title' => $taskInfo->getTitle(),
                    'description' => $taskInfo->getDescription(),
                    'color' => "#3493fa",
                    'display' => "list-item"
                );
        
                if ($taskInfo->getTime() != "00:00:00") {
                    $taskArray['start'] = $taskInfo->getDate() . "T" . $taskInfo->getTime();
                }else{
                    $taskArray['start'] = $taskInfo->getDate();
                }
                $response[] = $taskArray;
            }
        
            echo json_encode($response);
            // Regenera o ID da sessão
            session_regenerate_id();
        
    }
}

$taskController = new taskController();
$command = $_GET['command'];
$taskController->controllerFunction($command);

?>