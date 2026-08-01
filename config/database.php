<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "credifama_db";

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

//La excepcion del PDO la manejo en el api.php con el primer try
$dbh = new PDO($dsn, $user, $pass);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); //devuelve un array indexado por nombre de columna.