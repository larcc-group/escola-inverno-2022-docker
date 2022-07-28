<?php
include('conn.php');

if (!empty($_POST['novo'])) {
    if (defined('USE_MYSQL')) {
        // MySQL
        $stmt = $conn->prepare("INSERT INTO todo(nome, feito) VALUES (?,'N');");
        $stmt->bind_param('s', $_POST['novo']);
        $stmt->execute();
    }

    //Redis
    if (defined('USE_REDIS')) {
        $lista = get_lista_todo_fazer();
        $lista[] = [
            'id' => autoincrement_lista_todo(),
            'nome' => $_POST['novo'],
        ];
        set_lista_todo_fazer($lista);
    }
} else if (!empty($_GET['id_concluido'])) {
    $id = (int)$_GET['id_concluido'];
    if (defined('USE_MYSQL')) {
        // MySQL
        $stmt = $conn->prepare("UPDATE todo SET feito='S' WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }

    //Redis
    if (defined('USE_REDIS')) {
        $lista_fazer = get_lista_todo_fazer();
        $lista_concluido = get_lista_todo_concluido();
        foreach ($lista_fazer as $i => $item) {
            if ($item->id == $id) {
                $lista_concluido[] = $item;
                unset($lista_fazer[$i]);
                break;
            }
        }
        set_lista_todo_fazer($lista_fazer);
        set_lista_todo_concluido($lista_concluido);
    }
} else if (!empty($_GET['id_pendente'])) {
    $id = (int)$_GET['id_pendente'];
    if (defined('USE_MYSQL')) {
        // MySQL
        $stmt = $conn->prepare("UPDATE todo SET feito='N' WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }

    //Redis
    if (defined('USE_REDIS')) {
        $lista_fazer = get_lista_todo_fazer();
        $lista_concluido = get_lista_todo_concluido();
        foreach ($lista_concluido as $i => $item) {
            if ($item->id == $id) {
                $lista_fazer[] = $item;
                unset($lista_concluido[$i]);
                break;
            }
        }
        set_lista_todo_fazer($lista_fazer);
        set_lista_todo_concluido($lista_concluido);
    }
} else if (!empty($_GET['id_remover'])) {
    $id = (int)$_GET['id_remover'];
    if (defined('USE_MYSQL')) {
        // MySQL
        $stmt = $conn->prepare("DELETE FROM todo WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }

    //Redis
    if (defined('USE_REDIS')) {
        $lista_concluido = get_lista_todo_concluido();
        foreach ($lista_concluido as $i => $item) {
            if ($item->id == $id) {
                unset($lista_concluido[$i]);
                break;
            }
        }
        set_lista_todo_concluido($lista_concluido);
    }
}
header('Location: /');
die();
