<?php
session_start();
$pdo = new PDO('mysql:host=192.168.1.3;dbname=dbdb', 'root', '');

if (isset($_SESSION['userid'])) {
    $statement = $pdo->prepare("SELECT * FROM haustiere WHERE user_fk = ?");
    $statement->execute(array($_SESSION['userid']));
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
} else {
    die("Zugriff verweigert");
}
