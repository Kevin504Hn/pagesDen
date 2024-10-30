<?php

// Archivo: process-form.php

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

/*
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

try {
    // Conectar a la base de datos
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}",
        $dbConfig['user'],
        $dbConfig['password'],
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Recoger y sanitizar datos del formulario
    $data = [
        'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
        'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
        'phone' => filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING),
        'event_type' => filter_input(INPUT_POST, 'event-type', FILTER_SANITIZE_STRING),
        'message' => filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Validar campos requeridos
    if (!$data['name'] || !$data['email'] || !$data['phone'] || !$data['event_type'] || !$data['message']) {
        throw new Exception('Todos los campos son requeridos');
    }
    
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