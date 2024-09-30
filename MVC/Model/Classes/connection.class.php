<?php

class Connection{
  
  public static function connection(){
    try {
      $pdo = new PDO('mysql:host=plannerfocus.mysql.dbaas.com.br;dbname=plannerfocus;charset=utf8', 'plannerfocus', 'Planner90!');
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      
      return $pdo;
    } catch(PDOException $e) {
      echo 'Erro de conexão: ' . $e->getMessage();
    }

  }
}
?>