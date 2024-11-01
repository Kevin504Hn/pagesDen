<?php

// Archivo: process-form.php
/*
$ip = $_SERVER['REMOTE_ADDR'];
$captcha = $_POST['g-recaptcha-response'];
$secretKey = "6LcqOnAqAAAAAP-cA-9lLESCx__pNJhd71epQtDl";

$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha&remoteip=$ip");

$atributos = json_decode($response, true);

if ($atributos['success'] == false) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, completa el CAPTCHA.'
    ]);
    exit;
}


// Configuración de reCAPTCHA
$secretKey = '6LfB9W8qAAAAAIZ0tG-q2PGPz8FdrEs_4Nd8EPdC'; // Reemplaza con tu clave secreta

// Recoger datos del formulario
$recaptchaResponse = $_POST['g-recaptcha-response'];
$userIP = $_SERVER['REMOTE_ADDR'];

// Verificar reCAPTCHA
$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}&remoteip={$userIP}");
$responseKeys = json_decode($response, true);

if (intval($responseKeys["success"]) !== 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, completa el CAPTCHA.'
    ]);
    exit;
}
*/
// Archivo: process-form.php
$ip = $_SERVER['REMOTE_ADDR'];
$captcha = $_POST['g-recaptcha-response'];
$secretKey = "6LcqOnAqAAAAAP-cA-9lLESCx__pNJhd71epQtDl";
// Headers para AJAX
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Configuración de la base de datos para XAMPP
$dbConfig = [
    'host' => 'localhost',
    'dbname' => 'densuenos',
    'user' => 'root',
    'password' => ''
];

function verifyCaptcha($secretKey, $captchaResponse, $ip) {
    try {
        // Validar que tengamos una respuesta del captcha
        if (empty($captchaResponse)) {
            throw new Exception('Por favor, completa el CAPTCHA.');
        }

        // Construir la URL con datos codificados
        $url = sprintf(
            "https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s&remoteip=%s",
            urlencode($secretKey),
            urlencode($captchaResponse),
            urlencode($ip)
        );

        // Configurar contexto para la petición HTTP
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 5, // 5 segundos de timeout
                'ignore_errors' => true
            ]
        ]);

        // Realizar la petición
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            throw new Exception('Error al verificar el CAPTCHA. Por favor, intenta de nuevo.');
        }

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error al procesar la respuesta del CAPTCHA.');
        }

        if (!isset($responseData['success'])) {
            throw new Exception('Respuesta del CAPTCHA inválida.');
        }

        if ($responseData['success'] === false) {
            $errorMessage = 'Verificación del CAPTCHA fallida';
            if (isset($responseData['error-codes'])) {
                switch ($responseData['error-codes'][0]) {
                    case 'missing-input-secret':
                        $errorMessage = 'La clave secreta del CAPTCHA no fue proporcionada.';
                        break;
                    case 'invalid-input-secret':
                        $errorMessage = 'La clave secreta del CAPTCHA no es válida.';
                        break;
                    case 'missing-input-response':
                        $errorMessage = 'Por favor, completa el CAPTCHA.';
                        break;
                    case 'invalid-input-response':
                        $errorMessage = 'La respuesta del CAPTCHA no es válida.';
                        break;
                    case 'timeout-or-duplicate':
                        $errorMessage = 'El CAPTCHA ha expirado. Por favor, inténtalo de nuevo.';
                        break;
                }
            }
            throw new Exception($errorMessage);
        }

        return true;

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    }
}


function validateRequiredFields($data)
{
    $requiredFields = [
        'name' => 'El nombre es requerido',
        'email' => 'El email es requerido',
        'phone' => 'El teléfono es requerido',
        'event_type' => 'El tipo de evento es requerido',
        'message' => 'El mensaje es requerido'
    ];

    $errors = [];

    foreach ($requiredFields as $field => $message) {
        if (empty($data[$field])) {
            $errors[] = $message;
        }
    }

    if (!empty($errors)) {
        throw new Exception(implode('. ', $errors));
    }
}


function validateFormData(): array
{
    $data = [];

    // Validar nombre
    $name = trim($_POST['name'] ?? '');
    if (empty($name) || strlen($name) > 100) {
        throw new InvalidArgumentException('Nombre inválido');
    }
    $data['name'] = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

    // Validar email
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new InvalidArgumentException('Email inválido');
    }
    $data['email'] = $email;

    // Validar teléfono (ejemplo para formato internacional)
    $phone = trim($_POST['phone'] ?? '');
    if (!preg_match('/^(?:\+504)?\d{8}$/', $phone)) {
        throw new InvalidArgumentException('Teléfono inválido');
    }
    $data['phone'] = preg_replace('/[^\d+]/', '', $phone);

    // Validar tipo de evento
    $eventType = trim($_POST['event-type'] ?? '');
    $allowedTypes = ['Bodas', 'Cumpleaños', 'Celebraciones', 'Otro']; // Ajusta según tus necesidades
    if (!in_array($eventType, $allowedTypes)) {
        throw new InvalidArgumentException('Tipo de evento inválido');
    }
    $data['event_type'] = $eventType;

    // Validar mensaje
    $message = trim($_POST['message'] ?? '');
    if (empty($message) || strlen($message) > 1000) {
        throw new InvalidArgumentException('Mensaje inválido');
    }
    $data['message'] = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    $data['created_at'] = date('Y-m-d H:i:s');

    return $data;
}

function validateData($data) {
    $errors = [];

    // Validar el correo electrónico
    if (!$data['email']) {
        $errors[] = "El correo electrónico no es válido.";
    }

    // Validar el número de teléfono (prefijo opcional +504, seguido de 8 dígitos)
    $phonePattern = '/^(?:\+504)?\d{8}$/';
    if (!preg_match($phonePattern, $data['phone'])) {
        $errors[] = "El número de teléfono debe tener 8 dígitos y opcionalmente el prefijo +504, sin espacios ni guiones.";
    }

    // Validar que el mensaje no tenga más de 250 caracteres
    if (strlen($data['message']) > 250) {
        $errors[] = "El mensaje no debe exceder los 250 caracteres.";
    }

    // Retornar errores si existen
    return $errors;
}


try {

    verifyCaptcha($secretKey, $captcha, $ip);
    // Conectar a la base de datos
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}",
        $dbConfig['user'],
        $dbConfig['password'],
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = [
        'name' => htmlspecialchars(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS)),
        'email' => filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL),
        'phone' => htmlspecialchars(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS)),
        'event_type' => htmlspecialchars(filter_input(INPUT_POST, 'event-type', FILTER_SANITIZE_SPECIAL_CHARS)),
        'message' => htmlspecialchars(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS)),
        'created_at' => date('Y-m-d H:i:s')
    ];

    $errors = validateData($data);
    //validateRequiredFields($data);
    //$validatedData = validateFormData();

    // Validar campos requeridos
    if (empty($errors)) {
 // Guardar en la base de datos
    $sql = "INSERT INTO contact_submissions 
            (name, email, phone, event_type, message, created_at) 
            VALUES 
            (:name, :email, :phone, :event_type, :message, :created_at)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    // Responder al cliente con éxito
    echo json_encode([
        'success' => true,
        'message' => 'Gracias, hemos recibido tus datos'
    ]);
    }

   
} catch (Exception $e) {
    // Registrar el error
    error_log("Error en formulario de contacto: " . $e->getMessage());
    // Responder al cliente con error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
    ]);
}
