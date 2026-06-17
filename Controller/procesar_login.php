<?php

function procesarLogin(
    array $post,
    Usuario $modeloUsuario
): array {

    $nombre   = $post['usuario']  ?? '';
    $password = $post['password'] ?? '';

    if (empty($nombre) || empty($password)) {
        return ['redirect' => '../View/login.php?error=campos_vacios', 'session' => []];
    }

    $resultado = $modeloUsuario->login($nombre, $password);

    if ($resultado) {
        $roles = $modeloUsuario->obtenerRoles($resultado['id_usuario']);
        return [
            'redirect' => '../index.php',
            'session'  => [
                'id'     => $resultado['id_usuario'],
                'nombre' => $resultado['nombre_usuario'],
                'roles'  => $roles
            ]
        ];
    } else {
        return ['redirect' => '../View/login.php?error=credenciales_invalidas', 'session' => []];
    }
}


if (!defined('TESTING')) {
    session_start();
    require_once '../Config/conexion.php';
    require_once '../Model/Usuario.php';

    $respuesta = procesarLogin($_POST, new Usuario($conn));

    foreach ($respuesta['session'] as $clave => $valor) {
        $_SESSION[$clave] = $valor;
    }

    header('Location: ' . $respuesta['redirect']);
    exit();
}

?>