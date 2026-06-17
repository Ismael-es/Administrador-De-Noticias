<?php
function procesarCrearNoticia(
    array $session,
    array $post,
    array $files,
    Noticia $modeloNoticia, 
    Parametro $parametro
): string {

    if (!isset($session['id'])) {
        return 'redirect:../View/login.php';
    }

    if (!in_array(1, $session['roles'])) {
        return 'redirect:../index.php?error=sin_permiso';
    }

    $titulo      = $post['titulo']      ?? '';
    $descripcion = $post['descripcion'] ?? '';

    $url_error = '../View/crear_noticia.php?error=';
    $url_datos = '&titulo=' . urlencode($titulo) . '&descripcion=' . urlencode($descripcion);

    if (empty($titulo) || empty($descripcion)) {
        return 'redirect:' . $url_error . 'campos_vacios' . $url_datos;
    }

    if (strlen($titulo) < 10 || strlen($titulo) > 100) {
        return 'redirect:' . $url_error . 'titulo_invalido' . $url_datos;
    }

    if (strlen($descripcion) < 50) {
        return 'redirect:' . $url_error . 'descripcion_invalida' . $url_datos;
    }

    if ($modeloNoticia->existeTitulo($titulo)) {
        return 'redirect:' . $url_error . 'titulo_duplicado' . $url_datos;
    }

    $max_mb        = $parametro->obtenerPorClave('imagen_max_mb');
    $tamano_maximo = $max_mb * 1024 * 1024;
    $nombre_imagen = null;

    if (isset($files['imagen']) && $files['imagen']['error'] === UPLOAD_ERR_OK) {

        $extension = strtolower(pathinfo($files['imagen']['name'], PATHINFO_EXTENSION));
        $tamano    = $files['imagen']['size'];

        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return 'redirect:' . $url_error . 'imagen_formato' . $url_datos;
        }

        if ($tamano > $tamano_maximo) {
            return 'redirect:' . $url_error . 'imagen_tamano' . $url_datos;
        }

        $nombre_imagen = uniqid() . '.' . $extension;
        $destino       = '../uploads/' . $nombre_imagen;

        if (!move_uploaded_file($files['imagen']['tmp_name'], $destino)) {
            return 'redirect:' . $url_error . 'imagen_error' . $url_datos;
        }
    }

    $resultado = $modeloNoticia->crear($titulo, $descripcion, $session['id'], $nombre_imagen);

    if ($resultado) {
        return 'redirect:../index.php?exito=noticia_creada';
    } else {
        return 'redirect:' . $url_error . 'error_guardar' . $url_datos;
    }
}


// Solo se ejecuta cuando la vista llama al controlador (no durante el test)
if (!defined('TESTING')) {
    session_start();
    require_once '../Config/conexion.php';
    require_once '../Model/Noticia.php';
    require_once '../Model/Parametro.php';

    $destino = procesarCrearNoticia(
        $_SESSION,
        $_POST,
        $_FILES,
        new Noticia($conn),
        new Parametro($conn)
    );

    header('Location: ' . str_replace('redirect:', '', $destino));
    exit();
}
?>