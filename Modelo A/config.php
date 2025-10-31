<?php

$host = 'localhost';
$db   = 'Consultoría';
$user = 'root'; 
$pass = '';     
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";


$options = [

    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

    PDO::ATTR_EMULATE_PREPARES   => false,
];


try {
   
     $pdo = new PDO($dsn, $user, $pass, $options);
   
} catch (\PDOException $e) {
    
     error_log('Error de conexión a la BBDD: ' . $e->getMessage());
     die('Error: No se pudo establecer conexión con la base de datos. Por favor, intente más tarde.');
}