<?php

header('Content-Type: application/json');


//Lista de codigos de estado HTTP
//https://developer.mozilla.org/es/docs/Web/HTTP/Status
$method = $_SERVER['REQUEST_METHOD'];
try {
    include_once __DIR__ . '/../config/database.php';
    include_once __DIR__ . '/../src/GestorUsuario.php';

    $gestorUsuario = new GestorUsuario($dbh);
} catch (PDOException $e) {
    http_response_code(500); //500 es el codigo Internal Server Error
    echo json_encode([
        'status' => 'error',
        'message' => 'No se pudo conectar a la base de datos'
    ]);
    exit;
}


switch ($method) {
    case "GET":
        try {
            http_response_code(200); //200 es el codigo OK
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'usuarios' => $gestorUsuario->obtenerUsuarios()
                ]
            ]);
        } catch (PDOException $e) {
            http_response_code(500); //500 es el codigo Internal Server Error
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo obtener los usuarios'
            ]);
        }
        break;
    case "POST":
        try {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');

            $nombreRegex = '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,50}$/u';
            $emailRegex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
            $telefonoRegex = '/^09[0-9]{7}$/';

            $listaErrores = []; //Quiero retornar todos los errores que encuentre

            //Validacion Nombre
            if ($nombre === '') {
                $listaErrores["nombre"] = "El nombre no puede ser vacío";
            } elseif (!preg_match($nombreRegex, $nombre)) {
                $listaErrores["nombre"] = "El nombre no es válido. Debe contener solo letras y espacios.";
            }

            //Validacion Email
            if ($email === '') {
                $listaErrores["email"] = "El email no puede ser vacío.";
            } elseif (!preg_match($emailRegex, $email)) {
                $listaErrores["email"] = "El formato del email es inválido.";
            } elseif ($gestorUsuario->existeEmail($email)) {
                $listaErrores["email"] = "El correo ya existe.";
            }

            //Validacion Telefono (campo opcional)
            if ($telefono === '') { //Si el campo telefono esta vacio lo hago null
                $telefono = null;
            }
            if ($telefono !== null) { //Si el telefono no es null, hago la  validacion
                if (!preg_match($telefonoRegex, $telefono)) {
                    $listaErrores["telefono"] = "El formato del numero de celular es inválido";
                } elseif ($gestorUsuario->existeTelefono($telefono)) {
                    $listaErrores["telefono"] = "El número de teléfono ya existe.";
                }
            }

            //Solo tiro los errores de validacion
            if (!empty($listaErrores)) {
                http_response_code(422); //422 es el codigo Unprocessable Content
                echo json_encode([
                    'status' => 'error',
                    'errors' => $listaErrores
                ]);
                break;
            }
            $gestorUsuario->guardarUsuario($nombre, $email, $telefono);
            http_response_code(201); //201 es el codigo Created
            echo json_encode([
                'status' => 'success',
                'message' => 'Usuario guardado correctamente'
            ]);
            break;
        } catch (PDOException $e) {
            http_response_code(500); //500 es el codigo Internal Server Error
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo procesar la solicitud'
            ]);
        }
        break;
    default:
        http_response_code(405); //405 es el codigo Method Not Allowed
        echo json_encode([
            'status' => 'error',
            'message' => 'Método no permitido'
        ]);
        break;
}
