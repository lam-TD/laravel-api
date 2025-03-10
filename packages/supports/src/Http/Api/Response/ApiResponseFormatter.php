<?php
namespace Ltd\Supports\Http\Api\Response;

class ApiResponseFormatter
{
  protected array $structure = [
    'code' => '200',
    'message' => null,
    'data' => null
  ];

  public function ok($message = "OK", $data = null, $meta = [])
  {
    $responseData = [
      'status' => 'OK',
      'message' => $message,
      'data' => $data
    ];

    if (!empty($meta)) {
      $responseData['meta'] = $meta;
    }

    return response()->json($responseData);
  }

  public function created()
  {
    return response()->json([
      'status' => 'CREATED',
      'message' => 'Created',
    ], 201);
  }

  public function internalServerError($message = "Server Error", $data = null)
  {
    return response()->json([
      'status' => 'ERROR',
      'message' => $message,
      'data' => $data
    ], 500);
  }

  public function notFound($message = 'Resource Not Found')
  {
    return response()->json([
      'message' => $message,
    ], 404);
  }

  public function tooManyRequests($message = 'Too Many Requests')
  {
    return response()->json([
      'message' => $message,
    ], 429);
  }

  public function forbidden($message = 'Forbidden')
  {
    return response()->json([
      'message' => $message,
    ], 403);
  }

  public function badRequest($message = 'Bad Request')
  {
    return response()->json([
      'message' => $message,
    ], 400);
  }
}