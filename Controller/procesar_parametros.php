<?php

function procesarParametros(
    array $session,
    array $post,
    Parametro $parametro,
    Noticia $modeloNoticia
): string {

    if (!isset($session['id'])) {
        return 'redirect:../View/login.php';
    }

    $dias_expiracion = $post['dias_expiracion'] ?? '';
    $imagen_max_mb   = $post['imagen_max_mb']   ?? '';

    if (empty($dias_expiracion) || empty($imagen_max_mb)) {
        return 'redirect:../View/parametros.php?error=campos_vacios';
    }

    if (!is_numeric($dias_expiracion) || !is_numeric($imagen_max_mb) ||
        $dias_expiracion <= 0 || $imagen_max_mb <= 0) {
        return 'redirect:../View/parametros.php?error=valores_invalidos';
    }

    $resultado1 = $parametro->actualizar('dias_expiracion', $dias_expiracion);
    $resultado2 = $parametro->actualizar('imagen_max_mb',   $imagen_max_mb);

    if (!$resultado1 || !$resultado2) {
        return 'redirect:../View/parametros.php?error=error_guardar';
    }

    $modeloNoticia->verificarExpiracion($dias_expiracion);

    return 'redirect:../View/parametros.php?exito=parametros_guardados';
}


if (!defined('TESTING')) {
    session_start();
    require_once '../Config/conexion.php';
    require_once '../Model/Parametro.php';
    require_once '../Model/Noticia.php';

    $destino = procesarParametros(
        $_SESSION,
        $_POST,
        new Parametro($conn),
        new Noticia($conn)
    );

    header('Location: ' . str_replace('redirect:', '', $destino));
    exit();
}

?>