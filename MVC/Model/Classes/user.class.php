<?php


class User implements JsonSerializable{
    private $id_user;
    private $username;
    private $email;
    private $password;
    private $picture;

    // Importar metodos get e set mágicos e JSON
    use Model;

    public function setPicture($picture){
        if(is_array($picture)){
            $uploaddir = __DIR__ . '/../../View/assets/uploads/';
            $extension = pathinfo($picture['name'], PATHINFO_EXTENSION);
            $name = md5(uniqid()) . $extension;
            $uploadfile = $uploaddir . $name;
            if (move_uploaded_file($picture['tmp_name'], $uploadfile)) {
                $this->picture = 'assets/uploads/' . $name;
            } else {
                echo "Possível ataque de upload de arquivo!\n";
            }
        }else{
            $this->picture = $picture;
        }
    }

    // DML - Data Manipulation Language
    // Pois você manipula as linhas de uma tabela
    public function insert(){

        $pdo = Connection::connection();
        try{

            // Verificar se o email já está cadastrado
            $checkQuery = 'SELECT COUNT(*) FROM users WHERE email = :email';
            $checkStmt = $pdo->prepare($checkQuery);
            $checkStmt->bindParam(':email', $this->email);
            $checkStmt->execute();

            $count = $checkStmt->fetchColumn();

            if ($count > 0) {                
                // O email já está cadastrado, retorne uma mensagem
                return false;
            } else {
            // Query é uma solicitação feita ao banco de dados para executar uma ação específica
            $query = 'INSERT INTO users(username, email, password, picture) VALUES (:username, :email, :password, :picture)';

            // Statement em consulta SQL refece a uma instrução de SQL
            $stmt = $pdo->prepare($query);

            $stmt->execute([
                ':username'=>$this->username,
                ':email'=>$this->email,
                ':password'=>$this->password,
                ':picture'=>$this->picture
            ]);
            // Inserindo o valor da Id_User como o valor no Banco
            $this->id_user = $pdo->lastInsertId();
            return true;
        }
        }catch(Exception $e){
            var_dump('Erro ao cadastrar objeto: ' . $e->getMessage());
            return false;
        }
    }

    public function update(){
        $pdo = Connection::connection();
        try{
            $query = 'UPDATE users SET username = :username, email = :email, password = :password, picture = :picture WHERE id_user = :id_user';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':username'=>$this->username,
                ':email'=>$this->email,
                ':password'=>$this->password,
                ':picture'=>$this->picture,
                ':id_user'=>$this->id_user
            ]);
            return true;
        }catch(Exception $e){
            echo 'Erro ao atualizar objeto: ' . $e->getMessage();
            return false;
        }
    }

    public function delete($id){
        $pdo = Connection::connection();
        try{
            $query = 'DELETE FROM users WHERE id_user = :id_user';
            $stmt = $pdo->prepare($query);

            // $stmt->execute([]) e $stmt->bindParam() são a mesma coisa escrita de formas diferentes
            $stmt->bindParam(':id_user', $id);
            $stmt->execute();
            return true;
        }catch(Exception $e){
            echo 'Erro ao deletar objeto: ' . $e->getMessage();
            return false;
        }
    }

    public static function getOne($id){
        $pdo = Connection::connection();
        try{
            foreach($pdo->query('SELECT * FROM users WHERE id_user = ' . $id) as $object_line){
                return $object_line;
            }
        }catch(Exception $e){
            throw $e;
        } 
    }

    public static function getAll(){
        $pdo = Connection::connection();
        $objects_list =[];
        
        foreach($pdo->query('SELECT * FROM users') as $object_line){
            $user = new User();
            $user->username = $object_line['username'];
            $user->email = $object_line['email'];
            $user->password = $object_line['password'];
            $user->picture = $object_line['picture'];
            $objects_list[] = $user;
        }
        return $objects_list;
    }
}
?>