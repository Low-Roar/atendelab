<?php

$host = "localhost";
$porta = "3307";
$banco = "aula_univille";
$porta = "3307";
$usuario = "root";
$senha = "";
  try {
    $pdo = new PDO(
        "mysql:host=$host;port=$porta;dbname=$banco;charset=utf8",
        $usuario,
        $senha
    );
    echo "Conexao realizada com sucesso!";
  } catch(PDOException $e) {
      echo "Erro: " . $e->getMessage();
  }