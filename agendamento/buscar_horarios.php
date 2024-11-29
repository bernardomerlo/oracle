<?php

include_once '../start/init.php';

if (isset($_GET["id_barbeiro"]) && isset($_GET["data"])) {
    $id_barbeiro = $_GET["id_barbeiro"];
    $data = $_GET["data"];    

    $horarios = $oracle->select(
        "SELECT h.horario 
        FROM horarios h 
        LEFT JOIN cortes c 
        ON h.horario = c.horario 
        AND TRUNC(c.data_corte) = TO_DATE(:data, 'YYYY-MM-DD')
        AND c.id_barbeiro = :id_barbeiro 
        WHERE h.horario BETWEEN '09:00' AND '19:00' 
        AND c.horario IS NULL",
        [
            "id_barbeiro" => $id_barbeiro,
            "data" => $data
        ]
    );

    $horariosDisponiveis = [];
    foreach ($horarios as $horario) {
        // Certificar-se de que o campo 'horario' está presente
        if (isset($horario->horario)) {
            $horariosDisponiveis[] = $horario->horario;
        }
    }

    echo json_encode($horariosDisponiveis);
}
