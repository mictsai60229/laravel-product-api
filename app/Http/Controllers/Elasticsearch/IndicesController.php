<?php

namespace App\Http\Controllers\Elasticsearch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class IndicesController extends Controller{

    /*
    * create index controller, validate data and mapping inputs
    *
    *
    */
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

    public function refresh(Request $request){

        $validator = Validator::make($request->all(), [
            'index' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $index = $request->input('index');

        return app('Service\Elasticsearch\Indices')->refresh($index);
    }

    public function setInterval(Request $request){

        $validator = Validator::make($request->all(), [
            'index' => 'required',
            'interval' => 'required'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $index = $request->input('index');
        $interval = $request->input('interval');

        return app('Service\Elasticsearch\Indices')->setInterval($index, $interval);
    }

    public function setAliases(Request $request){

        $validator = Validator::make($request->all(), [
            'index' => 'required',
            'alias' => 'required'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $index = $request->input('index');
        $alias = $request->input('alias');

        return app('Service\Elasticsearch\Indices')->setAliases($index, $alias);
    }

    public function setAliasesLatest(Request $request){

        $validator = Validator::make($request->all(), [
            'index' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $index = $request->input('index');

        return app('Service\Elasticsearch\Indices')->setAliasesLatest($index);
    }

}
