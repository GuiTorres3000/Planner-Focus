<?php

class Task implements JsonSerializable{
    private $id_task;
    private $title;
    private $date;
    private $time;
    private $description;
    private $id_user;

    // Importar metodos get e set mágicos e JSON
    use Model;

    public function insert(){

        $pdo = Connection::connection();
        try{
            $query = 'INSERT INTO tasks(title, date, time, description, id_user) VALUES (:title, :date, :time, :description, :id_user)';
            $stmt = $pdo->prepare($query);

            $stmt->execute([
                ':title'=>$this->title,
                ':date'=>$this->date,
                ':time'=>$this->time,
                ':description'=>$this->description,
                ':id_user'=>$this->id_user
            ]);
            $this->id_task = $pdo->lastInsertId();
            return true;
        }catch(Exception $e){
            echo 'Erro ao cadastrar objeto: ' . $e->getMessage();
            return false;
        }
    }

    public function update(){
        $pdo = Connection::connection();
        try{
            $query = 'UPDATE tasks SET title = :title, date = :date, time = :time, description = :description WHERE id_task = :id_task AND id_user = :id_user';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':title'=>$this->title,
                ':date'=>$this->date,
                ':time'=>$this->time,
                ':description'=>$this->description,
                ':id_task'=>$this->id_task,
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
            $queryTasksTags = 'DELETE FROM tasks_tags WHERE id_task = :id_task';
            $stmtTasksTags = $pdo->prepare($queryTasksTags);
            $stmtTasksTags->bindParam(':id_task', $id);
            $stmtTasksTags->execute();

            $query = 'DELETE FROM tasks WHERE id_task = :id_task AND id_user = :id_user';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id_task', $id);
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
            foreach($pdo->query('SELECT * FROM tasks WHERE id_task = ' . $id . ' AND id_user = ' . $iduser) as $object_line){
                return $object_line;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public static function getAll($iduser){
        $pdo = Connection::connection();
        $objects_list =[];
        
        foreach($pdo->query('SELECT * FROM tasks WHERE id_user = ' . $iduser) as $object_line){
            $task = new Task();
            $task->id_task = $object_line['id_task'];
            $task->title = $object_line['title'];
            $task->date = $object_line['date'];
            $task->time = $object_line['time'];
            $task->description = $object_line['description'];
            $objects_list[] = $task;
        }
        return $objects_list;
    }
}
?>