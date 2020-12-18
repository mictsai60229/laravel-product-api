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

    public function bulk(Request $request){

        $validator = Validator::make($request->all(), [
            'index' => 'required',
            'config' => 'required',
            'actionType' => ['required', Rule::in(['index', 'delete', 'update'])],
            'actions' => 'required'
        ]);

        $index = $request->input('index');
        $config = $request->input('config');
        $actionType = $request->input('actionType');
        $actions = $request->input('actions');

        $response = [];
        $response['failure'] = [];
        $formatter = app("Formatter/{$config}");

        foreach($actions as $action){

            $action = $formatter->validate($action);
            if (empty($action)){
                $response['failure'][] = $action;
            }
        }

        return $response;
    }
}
