<?php

session_start();
include '../Model/autoload.php';

class userController{

    public function controllerFunction($command){
        $userController = new userController();
        switch ($command) {
            case 'register': $userController->registerUser(); break;
            case 'login': $userController->loginUser(); break;
            case 'getOne': $userController->getOneUser(); break;
            case 'getOneToken': $userController->getOneUserbyToken(); break;
            case 'updatePicture': $userController->updatePictureUser(); break;
            case 'updateUsername': $userController->updateUsernameUser(); break;
            case 'updatePassword': $userController->updatePasswordUser(); break;
            case 'logout': $userController->logoutUser(); break;
        }
    }

    public function registerUser(){
        
        // Verifique se o formulário foi enviado via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verifique se ambos os campos de senha estão preenchidos
            if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password'])) {
                // Verifique se as senhas coincidem
                if ($_POST['password'] === $_POST['passwordrepeat']) {
                    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

                    $user = new User();
                    $user->setUsername($_POST['username']);
                    $user->setEmail($_POST['email']);
                    $user->setPassword($hash);
                    $result = $user->insert();

                    if ($result === true) {
                        // A inserção foi bem-sucedida
                        $_SESSION['id_user'] = $user->getId_user();
    
                        $blockpage = new Blockpage();
                        $blockpage->setTitle("Este site está presente em sua lista de bloqueio!");
                        $blockpage->setDescription("Você pode editar essa mensagem no botão superior direito no canto da tela!");
                        $blockpage->setId_user($_SESSION['id_user']);
                        $blockpage->insert();
    
                        echo json_encode(array('success' => '/plannerfocus/MVC/View/index.html', 'token' => $_SESSION['id_user']));
                    } else {
                        // A função insert() retornou uma mensagem de erro
                        echo json_encode(array('error' => 'Email já existente! Por favor, insira um novo email.'));
                    }

                } else {
                    // As senhas não coincidem, defina a mensagem de erro no elemento HTML
                    echo json_encode(array('error' => 'As senhas não correspondem, verifique novamente!'));
                }
            }else {
                // Um ou ambos os campos de senha estão vazios, defina a mensagem de erro no elemento HTML
                echo json_encode(array('error' => 'Algum dos campos está vazio, vefifique novamente!'));
            }
        }
    }

    public function loginUser(){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            
            $pdo = Connection::connection();
            $query = "SELECT id_user, password FROM users WHERE email = :email";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':email', $_POST['emailLogin']);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
            if ($user && password_verify($_POST['passwordLogin'], $user['password'])) {
                // Senha válida, o usuário está autenticado
                $_SESSION['id_user'] = $user['id_user'];
                // Autenticação por Token    
                /*$key = 'chave';
                $payload = ["id_user" => $user['id_user']];
                
                $token= JWT::encode($payload, $key, 'HS256');
                */
                echo json_encode(array('success' => '/plannerfocus/MVC/View/index.html', 'token' => $_SESSION['id_user']));
            } else {
                echo json_encode(array('error' => 'Erro ao logar! Verifique a senha e o email novamente!'));
            }    
        }
    }

    public function getOneUser(){

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_SESSION['id_user'])) {
                $id_user = $_SESSION['id_user'];
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
            } else {
                // Sessão não está configurada
                http_response_code(401);
                echo json_encode(array('error' => 'Sessão não está configurada'));
            }
        }
    }

    public function updatePictureUser(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_SESSION['id_user'])) {
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $id_user = $_SESSION['id_user'];
                    $userInfo = User::getOne($id_user);
                    if($userInfo){
                        $user = new User();
                        $user->setId_user($id_user);
                        $user->setUsername($userInfo['username']);
                        $user->setEmail($userInfo['email']);
                        $user->setPassword($userInfo['password']);
                        $user->setPicture($_FILES['profilePicture']);
                        $user->update();
                    }
                }else {
                    // Usuário não encontrado no banco de dados
                    http_response_code(404);
                    echo json_encode(array('error' => 'Usuário não encontrado'));
                }
            } else {
                // Sessão não está configurada
                http_response_code(401);
                echo json_encode(array('error' => 'Sessão não está configurada'));
            }
        }
    }

    public function updateUsernameUser(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_SESSION['id_user'])) {
                $id_user = $_SESSION['id_user'];
                $userInfo = User::getOne($id_user);
                $userInfo['username'] = $_POST['profileUsername'];
                    
                if ($userInfo) {
                    $user = new User();
                    $user->setId_user($id_user);
                    $user->setUsername($userInfo['username']);
                    $user->setEmail($userInfo['email']);
                    $user->setPassword($userInfo['password']);
                    $user->setPicture($userInfo['picture']);
                    $user->update();
                }
            } else {
                // Sessão não está configurada
                http_response_code(401);
                echo json_encode(array('error' => 'Sessão não está configurada'));
            }
        }
    }

    public function updatePasswordUser(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_SESSION['id_user'])) {
                $id_user = $_SESSION['id_user'];
                $userInfo = User::getOne($id_user);
                if ($userInfo) {
                    if (password_verify($_POST['oldpassword'], $userInfo['password'])) {
                        if ($_POST['newpassword'] === $_POST['passwordrepeat']) {
                            $hash = password_hash($_POST['newpassword'], PASSWORD_DEFAULT);
                            $userInfo['password'] = $hash;
                            $user = new User();
                            $user->setId_user($id_user);
                            $user->setUsername($userInfo['username']);
                            $user->setEmail($userInfo['email']);
                            $user->setPassword($userInfo['password']);
                            $user->setPicture($userInfo['picture']);
                            $user->update();
                            echo json_encode($user);
                        }else{
                            echo ("Duas senhas não são iguais!");
                        }
                    }else{
                        echo json_encode(array('error' => 'Senha antiga não coincide!'));
                    }
                }
            } else {
                // Sessão não está configurada
                http_response_code(401);
                echo json_encode(array('error' => 'Sessão não está configurada'));
            }        
        }
    }

    public function logoutUser(){
        session_destroy();
        header("Location: /");
    }
}

$userController = new userController();
$command = $_GET['command'];
$userController->controllerFunction($command);

?>