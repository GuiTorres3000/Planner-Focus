<?php

class Blockpage implements JsonSerializable{
    private $id_blockpage;
    private $title;
    private $description;
    private $background;
    private $id_user;

    // Importar metodos get e set mágicos e JSON
    use Model;

    public function setBackground($background){
        if(is_array($background)){
            $uploaddir = __DIR__ . '/../../View/assets/uploads/';
            $extension = pathinfo($background['name'], PATHINFO_EXTENSION);
            $name = md5(uniqid()) . $extension;
            $uploadfile = $uploaddir . $name;
            if (move_uploaded_file($background['tmp_name'], $uploadfile)) {
                $this->background = 'assets/uploads/' . $name;
            } else {
                echo "Possível ataque de upload de arquivo!\n";
            }
        }else{
            $this->background = $background;
        }
    }

    public function insert(){

        $pdo = Connection::connection();
        try{
            $query = 'INSERT INTO blockpages(title, description, background, id_user) VALUES (:title, :description, :background, :id_user)';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':title'=> $this->title,
                ':description'=> $this->description,
                ':background'=> $this->background,
                ':id_user'=>$this->id_user
            ]);

            // Inserindo o valor da Id_blockpage como o valor no Banco
            $this->id_blockpage = $pdo->lastInsertId();
            return true;
        }catch(Exception $e){
            echo 'Erro ao cadastrar objeto: ' . $e->getMessage();
            return false;
        }
    }

    public function update(){
        $pdo = Connection::connection();
        try{
            $query = 'UPDATE blockpages SET title = :title, description = :description, background = :background WHERE id_user = :id_user';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':title'=> $this->title,
                ':description'=> $this->description,
                ':background'=> $this->background,
                ':id_user'=>$this->id_user
            ]);
            return true;
        }catch(Exception $e){
            echo 'Erro ao atualizar objeto: ' . $e->getMessage();
            return false;
        }
    }

    public static function delete($id){
        $pdo = Connection::connection();
        try{
            $query = 'DELETE FROM blockpages WHERE id_blockpage = :id_blockpage';
            $stmt = $pdo->prepare($query);

            // $stmt->execute([]) e $stmt->bindParam() são a mesma coisa escrita de formas diferentes
            $stmt->bindParam(':id_blockpage', $id);
            $stmt->execute();
            return true;
        }catch(Exception $e){
            echo 'Erro ao deletar objeto: ' . $e->getMessage();
            return false;
        }
    }

    public static function getOneByUser($id){
        $pdo = Connection::connection();
        try{
            foreach($pdo->query('SELECT * FROM blockpages WHERE id_user = ' . $id) as $object_line){
                return $object_line;
            }
        }catch(Exception $e){
            throw $e;
        } 
    }
}
?>