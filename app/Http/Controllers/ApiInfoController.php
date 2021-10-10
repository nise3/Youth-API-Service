<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

class ApiInfoController extends Controller
{
    const SERVICE_NAME = 'NISE-3 Youth Management API Service';
    const SERVICE_VERSION = 'V1';

    public function apiInfo(): JsonResponse
    {
        $response = [
            'service_name' => self::SERVICE_NAME,
            'service_version' => self::SERVICE_VERSION,
            'lumen_version' => App::version(),
            'module_list' => [
                'Youth'
            ],
            'description' => [
                'It is a youth management api service that manages the youths'
            ]
        ];

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML('
                <h1>' . $response['service_name'] . '</h1>' .
            '<p> service_version: ' . $response['service_version'] . '</p>' .
            '<p> lumen_version: ' . $response['lumen_version'] . '</p>' .
            '<p> module_list: ' . $response['module_list'][0] . '</p>' .
            '<p> description: <br>' . $response['description'][0] . '</p>'
        );
        $mpdf->Output('MyPDF.pdf', 'D');

        return Response::json($response, ResponseAlias::HTTP_OK);
    }
}
