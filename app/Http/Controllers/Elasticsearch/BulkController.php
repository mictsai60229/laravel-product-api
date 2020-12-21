<?php

namespace App\Http\Controllers\Elasticsearch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class BulkController extends Controller{


    public function bulk(Request $request){

        $validator = Validator::make($request->all(), [
            'index' => 'required',
            'config' => 'required',
            'actionType' => ['required', Rule::in(['index', 'delete', 'update'])],
            'actions' => 'required'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $index = $request->input('index');
        $config = $request->input('config');
        $actionType = $request->input('actionType');
        $actions = $request->input('actions');

        $formatter = app("Formatter/{$config}");

        $validatedActions = [];
        $failureActions = [];
        foreach($actions as $action){

            $validateRange = ($actionType === "index")?"all":"part";
            $validatedAction = $formatter->validate($action, $validateRange);
            
            if (empty($validatedAction)){
                $failureActions[] = $action;
            }
            else{
                $validatedActions[] = $validatedAction;
            }
        }


        if (!empty($validatedActions)){
            $response = app('Service\Elasticsearch\Bulk')->bulk($index, $actionType, $validatedActions);
        }
        else{
            $response = [];
        }
        $response['failure'] = $failureActions;

        return $response;
    }
}