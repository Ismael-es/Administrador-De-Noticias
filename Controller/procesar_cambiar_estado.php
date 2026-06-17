<?php


function procesarCambiarEstado(
    array $session,
    array $post,
    Noticia $modeloNoticia
): string {

    if (!isset($session['id'])) {
        return 'redirect:../View/login.php';
    }

    $noticia_id   = $post['noticia_id']   ?? null;
    $estado_nuevo = $post['estado_nuevo'] ?? null;

    if (!$noticia_id || !$estado_nuevo) {
        return 'redirect:../index.php';
    }

    $noticia       = $modeloNoticia->obtenerPorId($noticia_id);
    $estado_actual = $noticia['noticia_estado'];

    $es_autor     = $session['id'] == $noticia['noticia_autor'];
    $es_editor    = in_array(1, $session['roles']);
    $es_validador = in_array(2, $session['roles']);

    $permitido = false;

    if ($estado_actual == 1 && $es_editor && $es_autor && in_array($estado_nuevo, [2, 6])) {
        $permitido = true;
    }

    if ($estado_actual == 2 && $es_validador && !$es_autor && in_array($estado_nuevo, [3, 4])) {
        $permitido = true;
    }

    if ($estado_actual == 3 && $es_editor && $es_autor && in_array($estado_nuevo, [1, 2])) {
        $permitido = true;
    }

    if (!$permitido) {
        return 'redirect:../View/detalle_noticia.php?id=' . $noticia_id . '&error=sin_permiso';
    }

    $resultado = $modeloNoticia->cambiarEstado($noticia_id, $estado_nuevo, $session['id']);

    if ($resultado) {
        return 'redirect:../View/detalle_noticia.php?id=' . $noticia_id . '&exito=estado_cambiado';
    } else {
        return 'redirect:../View/detalle_noticia.php?id=' . $noticia_id . '&error=error_estado';
    }
}


if (!defined('TESTING')) {
    session_start();
    require_once  '../Config/conexion.php';
    require_once   '../Model/Noticia.php';

    $destino = procesarCambiarEstado(
        $_SESSION,
        $_POST,
        new Noticia($conn)
    );

    header('Location: ' . str_replace('redirect:', '', $destino));
    exit();
}

?>