<?php

class TaskTag implements JsonSerializable{
    private $id_task;
    private $id_tag;

    // Importar metodos get e set mágicos e JSON
    use Model;

    public function insert(){

        $pdo = Connection::connection();
        try{
            $query = 'INSERT INTO tasks_tags(id_task, id_tag) VALUES (:id_task, :id_tag)';
            $stmt = $pdo->prepare($query);

            $stmt->execute([
                ':id_task'=>$this->id_task,
                ':id_tag'=>$this->id_tag
            ]);
            return true;
        }catch(Exception $e){
            echo 'Erro ao cadastrar objeto: ' . $e->getMessage();
            return false;
        }
    }

    public static function delete($idtask, $idtag){
        $pdo = Connection::connection();
        try{
            $query = 'DELETE FROM tasks_tags WHERE id_task = :id_task AND id_tag = :id_tag';
            $stmt = $pdo->prepare($query);

            // $stmt->execute([]) e $stmt->bindParam() são a mesma coisa escrita de formas diferentes
            $stmt->bindParam(':id_task', $idtask);
            $stmt->bindParam(':id_tag', $idtag);
            $stmt->execute();
            return true;
        }catch(Exception $e){
            echo 'Erro ao deletar objeto: ' . $e->getMessage();
            return false;
        }
    }

    public static function getAllbyTask($idtask){
        $pdo = Connection::connection();
        $objects_list =[];
        try{
            $stmt = $pdo->prepare('SELECT * FROM tasks_tags WHERE id_task = :id_task');
            $stmt->bindParam(':id_task', $idtask);
            $stmt->execute();

            while ($object_line = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $taskTag = new TaskTag();
                $taskTag->id_task = $object_line['id_task'];
                $taskTag->id_tag = $object_line['id_tag'];
                $objects_list[] = $taskTag;
            }
            return $objects_list;
        }catch(Exception $e){
            echo 'Erro ao cadastrar objeto: ' . $e->getMessage();
            return false;
        }
    }

    public static function getAllbyTag($idtag){
        $pdo = Connection::connection();
        $objects_list =[];
        
        $stmt = $pdo->prepare('SELECT * FROM tasks_tags WHERE id_tag = :id_tag');
        $stmt->bindParam(':id_tag', $idtag);
        $stmt->execute();

        while ($object_line = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $taskTag = new TaskTag();
            $taskTag->id_task = $object_line['id_task'];
            $taskTag->id_tag = $object_line['id_tag'];
            $objects_list[] = $taskTag;
        }
        return $objects_list;
    }
}
?>