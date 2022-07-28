<?php
include('conn.php');

if (defined('USE_MYSQL')) {
    $itens_nao_feitos = $conn->query("SELECT * FROM todo WHERE feito='N' ORDER BY id ASC;");
    $itens_ja_feitos = $conn->query("SELECT * FROM todo WHERE feito='S' ORDER BY id DESC;");
}

if (defined('USE_REDIS')) {
    $lista_fazer = get_lista_todo_fazer();
    $lista_concluido = get_lista_todo_concluido();
}
?>

<html>
    <head>
        <title>Lista de tarefas</title>
    </head>
    <body>
        <h3>
            Itens a fazer usando
            <?php if (defined('USE_MYSQL')) echo 'MySQL' ?>
            <?php if (defined('USE_REDIS')) echo 'Redis' ?>
        </h3>
        <ul>
            <?php if (defined('USE_MYSQL')) while ($linha = $itens_nao_feitos->fetch_assoc()) { ?>
                <li>
                    <?php echo $linha['nome']; ?>
                    <button onclick="document.location.href='crud.php?id_concluido=<?php echo $linha['id'];?>';">Concluído</button>
                </li>
            <?php } ?>
            <?php if (defined('USE_REDIS')) foreach ($lista_fazer as $linha) { ?>
                <li>
                    <?php echo $linha->nome; ?>
                    <button onclick="document.location.href='crud.php?id_concluido=<?php echo $linha->id;?>';">Concluído</button>
                </li>
            <?php } ?>
            <li>
                <form method="POST" action="crud.php">
                    <input name="novo" autofocus>
                    <button type="submit">+</button>
                </form>
            </li>
        </ul>

        <h3>
            Itens concluídos usando
            <?php if (defined('USE_MYSQL')) echo 'MySQL' ?>
            <?php if (defined('USE_REDIS')) echo 'Redis' ?>
        </h3>
        <ul>
            <?php if (defined('USE_MYSQL')) while ($linha = $itens_ja_feitos->fetch_assoc()) { ?>
                <li>
                    <?php echo $linha['nome']; ?>
                    <button onclick="document.location.href='crud.php?id_pendente=<?php echo $linha['id'];?>';">Pendente</button>
                    <button onclick="document.location.href='crud.php?id_remover=<?php echo $linha['id'];?>';">Remover</button>
                </li>
            <?php } ?>
            <?php if (defined('USE_REDIS')) foreach ($lista_concluido as $linha) { ?>
                <li>
                    <?php echo $linha->nome; ?>
                    <button onclick="document.location.href='crud.php?id_pendente=<?php echo $linha->id;?>';">Pendente</button>
                    <button onclick="document.location.href='crud.php?id_remover=<?php echo $linha->id;?>';">Remover</button>
                </li>
            <?php } ?>
        </ul>
    </body>
</html>