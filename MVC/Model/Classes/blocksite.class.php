<?php

class Blocksite implements JsonSerializable{
    private $id_blocksite;
    private $url;
    private $id_category;
    private $id_user;

    // Importar metodos get e set mágicos e JSON
    use Model;

    public function insert(){

        $pdo = Connection::connection();
        try{
            $query = 'INSERT INTO blocksites(url, id_category, id_user) VALUES (:url, :id_category, :id_user)';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':url'=> $this->url,
                'id_category'=> $this->id_category,
                ':id_user'=>$this->id_user
            ]);

            // Inserindo o valor da Id_blocksite como o valor no Banco
            $this->id_blocksite = $pdo->lastInsertId();
            return true;
        }catch(Exception $e){
            echo 'Erro ao cadastrar objeto: ' . $e->getMessage();
            return false;
        }
    }

    public function update(){
        $pdo = Connection::connection();
        try{
            $query = 'UPDATE blocksites SET url = :url  WHERE id_blocksite = :id_blocksite AND id_category = :id_category AND id_user = :id_user';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':url'=>$this->url,
                ':id_blocksite'=>$this->id_blocksite,
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
            $query = 'DELETE FROM blocksites WHERE id_blocksite = :id_blocksite AND id_user = :id_user';
            $stmt = $pdo->prepare($query);

            // $stmt->execute([]) e $stmt->bindParam() são a mesma coisa escrita de formas diferentes
            $stmt->bindParam(':id_blocksite', $id);
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
            foreach($pdo->query('SELECT * FROM blocksites WHERE id_blocksite = ' . $id . 'AND id_user = ' . $iduser) as $object_line){                
                return $object_line;
            }
        }catch(Exception $e){
            throw $e;
        } 
    }

    public static function getAll($iduser){
        $pdo = Connection::connection();
        $objects_list =[];
        
        foreach($pdo->query('SELECT * FROM blocksites WHERE id_user = ' . $iduser) as $object_line){
            $blocksite = new Blocksite();
            $blocksite->id_blocksite = $object_line['id_blocksite'];
            $blocksite->url = $object_line['url'];
            $blocksite->id_category = $object_line['id_category'];
            $blocksite->id_user = $object_line['id_user'];
            $objects_list[] = $blocksite;
        }
        return $objects_list;
    }

    public static function getAllbyCategory($idcategory, $iduser){
        $pdo = Connection::connection();
        $objects_list =[];
        foreach($pdo->query('SELECT * FROM blocksites WHERE id_category = ' . $idcategory . ' AND id_user = ' . $iduser) as $object_line){
            $blocksite = new Blocksite();
            $blocksite->id_blocksite = $object_line['id_blocksite'];
            $blocksite->url = $object_line['url'];
            $blocksite->id_category = $object_line['id_category'];
            $blocksite->id_user = $object_line['id_user'];
            $objects_list[] = $blocksite;
        }
        return $objects_list;
    }
}
?>