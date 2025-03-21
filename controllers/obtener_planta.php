<?php
require_once '../Patrones/Database.php';

use Patrones\Database;

$pdo = Database::getInstance()->getConnection();
$stmt = $pdo->query("SELECT * FROM plantas");
$plantas = $stmt->fetchAll();

echo json_encode($plantas);
?>
