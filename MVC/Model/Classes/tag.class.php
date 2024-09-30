<?php

class Tag implements JsonSerializable{
    private $id_tag;
    private $title;
    private $color;
    private $id_user;

    // Importar metodos get e set mágicos e JSON
    use Model;

    public function insert(){

        $pdo = Connection::connection();
        try{
            $query = 'INSERT INTO tags(title, color, id_user) VALUES (:title, :color, :id_user)';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':title'=>$this->title,
                ':color'=>$this->color,
                ':id_user'=>$this->id_user
            ]);
            // Inserindo o valor da Id_Tag como o valor no Banco
            $this->id_tag = $pdo->lastInsertId();
            return true;
        }catch(Exception $e){
            echo 'Erro ao cadastrar objeto: ' . $e->getMessage();
            return false;
        }
    }

    public function update(){
        $pdo = Connection::connection();
        try{
            $query = 'UPDATE tags SET title = :title, color = :color WHERE id_tag = :id_tag AND id_user = :id_user';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':title'=>$this->title,
                ':color'=>$this->color,
                ':id_tag'=>$this->id_tag,
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
            $queryTasksTags = 'DELETE FROM tasks_tags WHERE id_tag = :id_tag';
            $stmtTasksTags = $pdo->prepare($queryTasksTags);
            $stmtTasksTags->bindParam(':id_tag', $id);
            $stmtTasksTags->execute();

            $query = 'DELETE FROM tags WHERE id_tag = :id_tag AND id_user = :id_user';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id_tag', $id);
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
            foreach($pdo->query('SELECT * FROM tags WHERE id_tag = ' . $id . ' AND id_user = ' . $iduser) as $object_line){
                return $object_line;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public static function getAll($iduser){
        $pdo = Connection::connection();
        $objects_list =[];
        
        foreach($pdo->query('SELECT * FROM tags WHERE id_user = ' . $iduser) as $object_line){
            $tag = new Tag();
            $tag->id_tag = $object_line['id_tag'];
            $tag->title = $object_line['title'];
            $tag->color = $object_line['color'];
            $tag->id_user = $object_line['id_user'];
            $objects_list[] = $tag;
        }
        return $objects_list;
    }
}
?>