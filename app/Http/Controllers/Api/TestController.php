<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function admin()
    {
        return response()->json([
            'success' => true,
            'message' => 'Akses admin berhasil.',
            'data' => [
                'role' => 'admin',
            ],
        ], 200);
    }

    public function semuaRole()
    {
        return response()->json([
            'success' => true,
            'message' => 'Admin dan Petugas dapat mengakses endpoint ini.',
            'data' => [
                'akses' => ['admin', 'petugas'],
            ],
        ], 200);
    }
}