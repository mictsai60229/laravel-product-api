<?php

namespace App\Http\Controllers\Elasticsearch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class CatController extends Controller{

    public function aliases(Request $request){

        $params = $request->all();

        return app('Repository\Elasticsearch\Cat')->aliases($params);
    }

    public function indices(Request $request){

        $params = $request->all();

        return app('Repository\Elasticsearch\Cat')->indices($params);
    }

    
}