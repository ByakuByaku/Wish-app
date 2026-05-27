<?php

class Response
{
    public static function success($message, $data = [], $status = 200)
    {
        self::send([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public static function error($message, $data = [], $status = 400)
    {
        Logger::error($message, [
            'status' => $status,
            'data' => $data,
        ]);

        self::send([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public static function send($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
}
?>