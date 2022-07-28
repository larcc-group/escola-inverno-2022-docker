<?php
$host = getenv('REDIS_HOST');

define('USE_REDIS', 1);
$redis = new Redis();
$redis->connect($host);


function autoincrement_lista_todo() {
    global $redis;
    return $redis->incr('todo_autoincrement');
}
function get_lista_todo_fazer() {
    return get_lista_redis('todo_fazer');
}
function set_lista_todo_fazer($lista) {
    return set_lista_redis('todo_fazer', $lista);
}

function get_lista_todo_concluido() {
    return get_lista_redis('todo_concluido');
}
function set_lista_todo_concluido($lista) {
    return set_lista_redis('todo_concluido', $lista);
}

function get_lista_redis($chave) {
    global $redis;
    $lista = json_decode($redis->get($chave));
    if (!$lista) {
        $lista = [];
    }
    return $lista;
}
function set_lista_redis($chave, $lista) {
    global $redis;
    return $redis->set($chave, json_encode(array_values($lista)));
}