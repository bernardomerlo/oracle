<?php
header('Content-Type: text/html; charset=utf-8');

include_once '../start/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_barbeiro = $_POST["id_barbeiro"];
    $nome_cliente = $_POST["nome_cliente"];
    $telefone_cliente = $_POST["telefone_cliente"];
    $data = $_POST["data"];
    $horario = $_POST["horarios"];
    $cliente_ip = $_SERVER["REMOTE_ADDR"];
    $tipo_corte = $_POST["tipo_corte"];

    try {
        $data_convertida = date("d-m-Y", strtotime($data));

        $oracle->beginTransaction();

        $data_convertida = date("d/m/y", strtotime($data)); 
        echo $data_convertida;

        $oracle->insert(
            "INSERT INTO cortes (nome_cliente, telefone_cliente, data_corte, id_barbeiro, cliente, horario, tipo_corte) 
            VALUES (:nome_cliente, :telefone_cliente, TO_DATE(:data, 'DD-MM-YYYY'), :id_barbeiro, :cliente, :horario, :tipo_corte)",
            [
                "nome_cliente" => $nome_cliente,
                "telefone_cliente" => $telefone_cliente,
                "data" => $data_convertida, // Garantir que o formato esteja correto
                "id_barbeiro" => $id_barbeiro,
                "cliente" => $cliente_ip,
                "horario" => $horario,
                "tipo_corte" => $tipo_corte
            ]
        );
        $oracle->endTransaction();

        $id = $oracle->selectOne("SELECT MAX(id) as id FROM cortes WHERE cliente = :cliente", ["cliente" => $cliente_ip])->id;

        header("Location: visualiza_agendado.php?id=" . $id);
        exit();
    } catch (Exception $e) {
        $oracle->rollBack();
        echo "Erro ao agendar corte: " . $e->getMessage();
    }
}
