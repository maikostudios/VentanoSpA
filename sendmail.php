<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "contacto@ventanospa.cl"; // Cambia esto por tu correo
    function limpiar_entrada($str) {
        $str = trim($str);
        $str = strip_tags($str);
        $str = htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $str = preg_replace('/[\x00-\x1F\x7F]/u', '', $str); // elimina caracteres de control
        return $str;
    }

    $name = isset($_POST["name"]) ? limpiar_entrada($_POST["name"]) : '';
    $email = isset($_POST["email"]) ? limpiar_entrada($_POST["email"]) : '';
    $phone = isset($_POST["phone"]) ? limpiar_entrada($_POST["phone"]) : '';
    $message = isset($_POST["message"]) ? limpiar_entrada($_POST["message"]) : '';

    // Validación básica
    if (empty($name) || empty($email) || empty($phone) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Por favor, completa todos los campos correctamente."
        ]);
        exit;
    }

    // Forzar UTF-8 en el subject y body
    $subject = "=?UTF-8?B?" . base64_encode("Consulta sobre $message - Cliente: $name") . "?=";
    $body = "Nombre: $name\nEmail: $email\nTeléfono: $phone\nMensaje:\n$message";
    $headers = "From: $email\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n";

    if (mail($to, $subject, $body, $headers)) {
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "¡Gracias por contactarnos, $name! Hemos recibido tu mensaje y te responderemos pronto."
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Hubo un problema al enviar tu mensaje. Por favor, intenta nuevamente más tarde o contáctanos directamente a contacto@ventanospa.cl."
        ]);
    }
}
?>