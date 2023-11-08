<?php

namespace App\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Validation\Validator;
use Spatie\Permission\Exceptions\UnauthorizedException;

trait HasResponse
{
    public function coreResponse(string $message, int $code, bool $isSuccess = true): JsonResponse
    {
        if ($isSuccess) {
            return response()->json([
                'status'  => true,
                'message' => $message,
                'code'    => $code
            ], $code);
        }

        return response()->json([
            'status' => false,
            'message' => $message,
            'code' => $code,
        ], $code);
    }

    protected function serverError(string $message, int $code = 500): JsonResponse
    {
        return $this->coreResponse($message, $code, false);
    }

    protected function successInsert(string $message = "Data added successfully", int $code = 201): JsonResponse
    {
        return $this->coreResponse($message, $code, true);
    }

    protected function successUpdate(string $message = "Data successfully updated", int $code = 200): JsonResponse
    {
        return $this->coreResponse($message, $code, true);
    }

    protected function successGet($data): JsonResponse
    {
        return response()->json([
            'status'    => true,
            'message'   => count($data) > 0 ? 'Successfully fetched data' : 'No data',
            'code'      => 200,
            'total'     => count($data),
            'data'      => $data
        ] , 200);
    }

    protected function paginating($data): JsonResponse
    {
        // dd(json_encode($data));
        $json = [
            'status'    => true,
            'message'   => count($data) > 0 ? 'Successfully fetched data' : 'No data',
            'code'      => 200,
            'total'     => count($data),
        ];
        return response()->json(
            $data
        , 200);
    }

    public function successGetOne($result, string $message, int $code = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'status' => true,
            'message' => $message ?? 'success',
            'code' => $code,
            'data' => $result,
        ];
        return response()->json($response, $code);
    }

    protected function wrongParam(string $message = 'Wrong Parameter Type Data - Unprocessable Content'): JsonResponse
    {
        return $this->coreResponse($message, 422, false);
    }

    protected function successDelete(string $message = "Data deleted successfully", int $code = 200): JsonResponse
    {
        return $this->coreResponse($message, $code, true);
    }

    protected function error(string $message, $data): JsonResponse
    {
        return $this->coreResponse($message, 422, false, $data);
    }

    protected function unauthorized(string $message = "Unauthorized", int $code = 401): JsonResponse
    {
        return $this->coreResponse($message, $code, false);
    }

    protected function forbiddenAccess(array $role): JsonResponse
    {
        throw UnauthorizedException::forPermissions($role);
    }

    protected function dataExist(string $message = "Data already exists", int $code = 409): JsonResponse
    {
        return $this->coreResponse($message, $code, false);
    }

    protected function errorLogin(string $message = "Wrong username or password", int $code = 401): JsonResponse
    {
        return $this->coreResponse($message, $code, false);
    }

    protected function errorToken(string $message = "Tokens are invalid", int $code = 500): JsonResponse
    {
        return $this->coreResponse($message, $code, false);
    }

    protected function successLogin(mixed $data, string $message = "Login Success", int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'code'   => $code,
            'data'   => $data
        ], $code);
    }

    protected function successLogout(mixed $data, string $message = "Logout Success", int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'code'   => $code,
            'data'   => $data
        ], $code);
    }

    public function sendResponse($result, string $message, int $code = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'status' => true,
            'message' => $message ?? 'success',
            'code' => $code,
            'data' => $result,
        ];
        return response()->json($response, $code);
    }

    public function failedRequest(Validator $v, string $model)
    {
        throw new HttpResponseException(response()->json([
            'status'    => false,
            'message'   => "Validation {$model} Error",
            'code'      => Response::HTTP_UNPROCESSABLE_ENTITY,
            'data'      => $v->errors()->all(),
        ], 400)->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    protected function customSuccess(string $message, $data): JsonResponse
    {
        return $this->coreResponse($message, 200, true, $data);
    }
}
