<?php

function procesarRegistro(
    array $post,
    Usuario $modeloUsuario
): string {

    $nombre    = $post['usuario']               ?? '';
    $password  = $post['password']              ?? '';
    $password2 = $post['password_confirmacion'] ?? '';
    $roles     = $post['rol']                   ?? [];

    if (empty($nombre) || empty($password) || empty($password2)) {
        return 'redirect:../view/login.php?error=campos_vacios';
    }

    if ($password !== $password2) {
        return 'redirect:../view/login.php?error=passwords_no_coinciden';
    }

    if (empty($roles)) {
        return 'redirect:../view/login.php?error=sin_rol';
    }

    $resultado = $modeloUsuario->registrar($nombre, $password, $roles);

    if ($resultado) {
        return 'redirect:../view/login.php?exito=registro_ok';
    } else {
        return 'redirect:../view/login.php?error=usuario_existente';
    }
}


if (!defined('TESTING')) {
    require_once '../Config/conexion.php';
    require_once '../Model/Usuario.php';

    $destino = procesarRegistro($_POST, new Usuario($conn));

    header('Location: ' . str_replace('redirect:', '', $destino));
    exit();
}

?>