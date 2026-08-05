<?php

namespace App\Http\Controllers\Api\V1\Cells\Assembly;

use App\Http\Controllers\Controller;
use App\Services\Manufacture\AssemblyService;
use Illuminate\Http\Request;

class AssemblyController extends Controller
{














    public function test(Request $request)
    {
        $result = AssemblyService::test();
        return $result;
    }
}
