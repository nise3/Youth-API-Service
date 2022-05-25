<?php

use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


if (!function_exists("clientUrl")) {
    function clientUrl($type)
    {
        return config("httpclientendpoint." . $type);
    }
}

if (!function_exists('formatApiResponse')) {
    /**
     * @param $data
     * @param $startTime
     * @param int $statusCode
     * @return array
     */
    function formatApiResponse($data, $startTime, int $statusCode = 200): array
    {
        return [
            "data" => $data ?: null,
            "_response_status" => [
                "success" => true,
                "code" => $startTime,
                "query_time" => $startTime->diffForHumans(Carbon::now())
            ]
        ];
    }
}

if (!function_exists('generateOtp')) {
    /**
     * @param $digits
     * @return int
     */
    function generateOtp(int $digits)
    {
        return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
    }
}

if (!function_exists("idpUserErrorMessage")) {

    /**
     * @param $exception
     */
    function idpUserErrorMessage($exception): array
    {
        $statusCode = $exception->getCode();
        $errors = [
            '_response_status' => [
                'success' => false,
                'code' => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Idp server Internal Server Error",
                "query_time" => 0
            ]
        ];

        switch ($statusCode) {
            case ResponseAlias::HTTP_UNPROCESSABLE_ENTITY:
            {
                $errors['_response_status']['code'] = ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
                $errors['_response_status']['message'] = "Username already exists in IDP";
                return $errors;
            }
            case ResponseAlias::HTTP_NOT_FOUND:
            {
                $errors['_response_status']['code'] = ResponseAlias::HTTP_NOT_FOUND;
                $errors['_response_status']['message'] = "IDP user not found";
                return $errors;
            }
            case ResponseAlias::HTTP_UNAUTHORIZED:
            {
                $errors['_response_status']['code'] = ResponseAlias::HTTP_UNAUTHORIZED;
                $errors['_response_status']['message'] = "HTTP 401 Unauthorized Error in IDP server";
                return $errors;
            }
            case ResponseAlias::HTTP_CONFLICT:
            {
                $errors['_response_status']['code'] = ResponseAlias::HTTP_CONFLICT;
                $errors['_response_status']['message'] = "Already exists";
                return $errors;
            }
            case 0:
            {
                $errors['_response_status']['message'] = $exception->getHandlerContext()['error'] ?? " SSL Certificate Error: An expansion of the 400 Bad Request response code, used when the client has provided an invalid client certificate";
                return $errors;
            }
            default:
            {
                return $errors;
            }

        }
    }
}

if (!function_exists("bearerUserToken")) {

    function bearerUserToken(\Illuminate\Http\Request $request, $headerName = 'User-Token')
    {
        $header = $request->header($headerName);

        $position = strrpos($header, 'Bearer ');

        if ($position !== false) {
            $header = substr($header, $position + 7);
            return strpos($header, ',') !== false ? strstr(',', $header, true) : $header;
        }
    }
}

if (!function_exists("logSelector")) {

    /**
     * @return array
     */
    function logSelector(): array
    {
        if (env('LOG_CHANNEL') === 'elasticsearch') {
            return config('elasticSearchLogConfig');
        }
        return config('lumenDefaultLogConfig');
    }
}

if (!function_exists("bn2en")) {

    /**
     * @param $number
     * @return array|string
     */
    function bn2en($number): array|string
    {
        $bn = array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০");
        $en = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
        return str_replace($bn, $en, $number);
    }
}

if (!function_exists("en2bn")) {

    /**
     * @param $number
     */
    function en2bn($number): array|string
    {
        $bn = array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০");
        $en = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
        return str_replace($en, $bn, $number);
    }
}

