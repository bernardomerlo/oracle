<?php

include_once '../../start/init.php';

if (!isset($_SESSION["user"])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    exit();
}

$id_barbeiro = intval($_GET['id']);

try {
    $barbeiro = $oracle->selectOne("SELECT * FROM barbeiros WHERE id = :id", ["id" => $id_barbeiro]);

    if (!$barbeiro) {
        header("Location: ../gerenciar_barbearia.php");
        exit();
    }

    $oracle->delete("DELETE FROM barbeiros WHERE id = :id", ["id" => $id_barbeiro]);

    header("Location: ../gerenciar_barbearia.php");
    exit();
} catch (Exception $e) {
    echo "Erro ao remover barbeiro: " . $e->getMessage();
    exit();
}
