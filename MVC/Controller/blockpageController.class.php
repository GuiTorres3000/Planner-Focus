<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

include '../Model/autoload.php';

class blockpageController{

    public function controllerFunction($command){
        $blockpageController = new blockpageController();
        switch ($command) {
            case 'userBlockpage': $blockpageController->userBlockpage(); break;
            case 'update': $blockpageController->updateBlockpage(); break;
            case 'delete': $blockpageController->deleteBlockpage(); break;
            case 'getOne': $blockpageController->getOneBlockpage(); break;
        }
    }

    public function userBlockpage(){
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!isset($_GET['id_user'])) {
                http_response_code(400); // Bad Request
                echo json_encode(array('error' => 'Parâmetro id_user não fornecido'));
                return;
            }       
            $id_user = $_GET['id_user'];
            $user = new User();
            $userInfo = $user::getOne($id_user); // Substitua 'getById' pelo método real que você usa para buscar informações do usuário
            
            if ($userInfo) {
                $response = array(
                    'username' => $userInfo['username'], // Substitua 'username' pelo nome real do campo no seu banco de dados
                    'email' => $userInfo['email'],
                    'picture' => $userInfo['picture'] // Substitua 'user_image' pelo nome real do campo no seu banco de dados
                );
                echo json_encode($response);
            } else {
                // Usuário não encontrado no banco de dados
                http_response_code(404);
                echo json_encode(array('error' => 'Usuário não encontrado'));
            }
        }
    }
    public function updateBlockpage(){
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['id_user'])) {
                http_response_code(400); // Bad Request
                echo json_encode(array('error' => 'Parâmetro id_user não fornecido'));
                return;
            }
            
                $id_user = $_POST['id_user'];
                $blockpage = new Blockpage();
                $blockpageInfo = $blockpage::getOneByUser($id_user);
                if($blockpageInfo){
                    $blockpage = new Blockpage();
                    $blockpage->setId_blockpage($blockpageInfo['id_blockpage']);
                    $blockpage->setTitle($_POST['title']);
                    $blockpage->setDescription($_POST['description']);
                    $blockpage->setBackground($_FILES['background']);
                    $blockpage->setId_user($blockpageInfo['id_user']);
                    $blockpage->update();
                    echo json_encode($blockpage);
                }
        }
    }
    
    public function getOneBlockpage(){
    
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!isset($_GET['id_user'])) {
                http_response_code(400); // Bad Request
                echo json_encode(array('error' => 'Parâmetro id_user não fornecido'));
                return;
            }
            
                $id_user = $_GET['id_user'];
                $blockpageInfo = Blockpage::getOneByUser($id_user); // Substitua 'getById' pelo método real que você usa para buscar informações do usuário
                
                if ($blockpageInfo) {
                    $response = array(
                        'title' => $blockpageInfo['title'], // Substitua 'title' pelo nome real do campo no seu banco de dados
                        'description' => $blockpageInfo['description'],
                        'background' => $blockpageInfo['background'] // Substitua 'user_image' pelo nome real do campo no seu banco de dados
                    );
                    echo json_encode($response);
                } else {
                    // Usuário não encontrado no banco de dados
                    http_response_code(404);
                    echo json_encode(array('error' => 'Página de bloqueio não registrada'));
                }
        }
    }
}
    
$blockpageController = new blockpageController();
$command = $_GET['command'];
$blockpageController->controllerFunction($command);
?>    