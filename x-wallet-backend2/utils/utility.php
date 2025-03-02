<?php
function sendJsonResponse($statusCode, $message, $data = null) {
    http_response_code($statusCode);
    $response = ["message" => $message];
    
    if ($data !== null) {
        $response["data"] = $data;
    }

    echo json_encode($response);
    exit;
}

?>
