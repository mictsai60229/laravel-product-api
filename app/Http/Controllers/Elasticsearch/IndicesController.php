<?php

namespace App\Http\Controllers\Elasticsearch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class IndicesController extends Controller{

    public function create(Request $request){
        
        $validator = Validator::make($request->all(), [
            'index' => 'required',
            'configPath' => 'required',
            'backupCount' => 'nullable|min:-1'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $index = $request->input('index');
        $configPath = $request->input('configPath');
        $backupCount = $request->input('backupCount', -1);
        
        return app('Service\Elasticsearch\Indices')->create($index, $configPath, $backupCount);
    }

}
