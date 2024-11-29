<?php

include_once '../start/init.php';

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    if (!is_numeric($id)) {
        die("ID inválido.");
    }
    $oracle->delete("DELETE FROM cortes WHERE id = :id", ["id" => $id]);
    header("Location: ../index.php");
}
