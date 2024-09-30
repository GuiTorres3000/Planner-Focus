<?php

class Category implements JsonSerializable{
    private $id_category;
    private $title;
    private $color;
    private $id_user;

    // Importar metodos get e set mágicos e JSON
    use Model;

    public function insert(){

        $pdo = Connection::connection();
        try{
            $query = 'INSERT INTO categories(title, color, id_user) VALUES (:title, :color, :id_user)';
            $stmt = $pdo->prepare($query);

            $stmt->execute([
                ':title'=>$this->title,
                ':color'=>$this->color,
                ':id_user'=>$this->id_user
            ]);
            // Inserindo o valor da Id_category como o valor no Banco
            $this->id_category = $pdo->lastInsertId();
            return true;
        }catch(Exception $e){
            echo 'Erro ao cadastrar objeto: ' . $e->getMessage();
            return false;
        }
    }

    public function update(){
        $pdo = Connection::connection();
        try{
            $query = 'UPDATE categories SET title = :title, color = :color WHERE id_category = :id_category AND id_user = :id_user';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':title'=>$this->title,
                ':color'=>$this->color,
                ':id_category'=>$this->id_category,
                ':id_user'=>$this->id_user
            ]);
            return true;
        }catch(Exception $e){
            echo 'Erro ao atualizar objeto: ' . $e->getMessage();
            return false;
        }
    }

    public static function delete($id, $iduser){
        $pdo = Connection::connection();
        try{
        
            $queryBlocksite = 'DELETE FROM blocksites WHERE id_category = :id_category AND id_user = :id_user';
            $stmtBlocksite = $pdo->prepare($queryBlocksite);
            $stmtBlocksite->bindParam(':id_category', $id);
            $stmtBlocksite->bindParam(':id_user', $iduser);
            $stmtBlocksite->execute();

            $query = 'DELETE FROM categories WHERE id_category = :id_category AND id_user = :id_user';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id_category', $id);
            $stmt->bindParam(':id_user', $iduser);
            $stmt->execute();
            
            return true;
        }catch(Exception $e){
            echo 'Erro ao deletar objeto: ' . $e->getMessage();
            return false;
        }
    }

    public static function getOne($id, $iduser){
        $pdo = Connection::connection();
        try{
            foreach($pdo->query('SELECT * FROM categories WHERE id_category = ' . $id . ' AND id_user = ' . $iduser) as $object_line){   
                return $object_line;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public static function getAll($iduser){
        $pdo = Connection::connection();
        $objects_list =[];
        
        foreach($pdo->query('SELECT * FROM categories WHERE id_user = ' . $iduser) as $object_line){
            $category = new Category();
            $category->id_category = $object_line['id_category'];
            $category->title = $object_line['title'];
            $category->color = $object_line['color'];
            $category->id_user = $object_line['id_user'];
            $objects_list[] = $category;
        }
        return $objects_list;
    }
}
?>