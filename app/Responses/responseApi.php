<?php
namespace App\Responses;

trait responseApi{
    public function responseApi($data, $message = null, $status = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $status);
    }
    public function responseError($message, $status = 500)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
        ], $status);
    }
    public function responseSuccess($message, $data = null, $status = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $status);
    }
}
