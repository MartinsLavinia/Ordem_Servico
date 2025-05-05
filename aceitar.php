<?php
$conn = new mysqli("localhost", "root", "", "oscd_lamanna");

$id = $_GET['os'];
$conn->query("UPDATE OS SET aceita = 1 WHERE id = $id");

header("Location: listar_os.php");
