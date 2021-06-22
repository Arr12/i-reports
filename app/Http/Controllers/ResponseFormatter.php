<?php

namespace App\Http\Controllers;

class ResponseFormatter
{
  protected static $response = [
    'meta' => [
      'code' => 200,
      'status' => 'success',
      'message' => null
    ],
    'data' => null
  ];

  public static function success($data = null, $message = null, $status = 200, $path = false)
  {
    self::$response['meta']['code'] = $status;
    self::$response['meta']['message'] = $message;
    self::$response['data'] = $data;

    // if ($path) {
    //     self::generateFile($path, $data);
    // }
    return response()->json(self::$response, $status);
  }

  public static function error($data = null, $message = null, $code = 400)
  {
    self::$response['meta']['status'] = 'error';
    self::$response['meta']['code'] = $code;
    self::$response['meta']['message'] = $message;
    self::$response['data'] = $data;

    return response()->json(self::$response, self::$response['meta']['code']);
  }

  public static function generateFile($path , $data)
  {
      $path = "../public/page-cache/".$path;
      if (!file_exists($path)) {
          mkdir($path, 0777, true);
      }
      file_put_contents($path.'/index.json', json_encode($data));
  }

}
