<?php

namespace App\Http\Controllers\Elasticsearch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\Elasticsearch\Bulk as EsBulkService;

class BulkController extends Controller{

    protected $EsBulk;
    public function __construct(EsBulkService $EsBulk){
        $this->EsBulk = $EsBulk;
    }

    public function bulk(Request $request){

        $validator = Validator::make($request->all(), [
            'index' => 'required',
            'config' => 'required',
            'actionType' => ['required', Rule::in(['index', 'delete', 'update'])],
            'actions' => 'required',
            'force' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $index = $request->input('index');
        $config = $request->input('config');
        $actionType = $request->input('actionType');
        $actions = $request->input('actions');
        $validateRange = ($request->input('actionType') === "index")?"all":"part";
        $force = $request->input('force', false);

        return $this->EsBulk->bulk($index, $config, $actionType, $actions, $validateRange, $force);
    }
}