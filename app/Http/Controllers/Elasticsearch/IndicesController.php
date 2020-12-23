<?php

namespace App\Http\Controllers\Elasticsearch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\Elasticsearch\Indices as EsIndicesService;


class IndicesController extends Controller{

    protected $EsIndices;
    public function __construct(EsIndicesService $EsIndices){
        $this->EsIndices = $EsIndices;
    }
    /*
    * create index controller, validate data and mapping inputs
    *
    *
    */
    public function create(Request $request){
        
        $validator = Validator::make($request->all(), [
            'index' => 'required',
            'configPath' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $index = $request->input('index');
        $configPath = $request->input('configPath');
        $backupCount = $request->input('backupCount', -1);
        
        return $this->EsIndices->create($index, $configPath, $backupCount);
    }

    public function startBulk(Request $request){
        
        $validator = Validator::make($request->all(), [
            'index' => 'required',
            'force' => 'nullable|boolean',
            'threshold' => 'nullable|numeric|between:0,1'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $index = $request->input('index');
        $force = $request->input('force', null);
        $docsThreshold = $force?0.0:(float)$request->input('threshold', 0.7);

        return $this->EsIndices->startBulk($index, $docsThreshold);
    }

    public function endBulk(Request $request){
        
        $validator = Validator::make($request->all(), [
            'index' => 'required',        
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $index = $request->input('index');

        return $this->EsIndices->endBulk($index);
    }

    public function deleteIndices(Request $request){
        
        $validator = Validator::make($request->all(), [
            'index' => 'required',
            'backupCount' => 'required|numeric|min:0',
            'force' => 'nullable|boolean',
            'threshold' => 'nullable|numeric|between:0,1'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $index = $request->input('index');
        $backupCount = (int)$request->input('backupCount');
        $force = $request->input('force', null);
        $docsThreshold = $force?0.0:(float)$request->input('threshold', 0.7);

        return $this->EsIndices->deleteIndices($index, $backupCount, $docsThreshold);
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

        return $this->EsIndices->setAliases($index, $alias);
    }

    public function setAliasesLatest(Request $request){

        $validator = Validator::make($request->all(), [
            'index' => 'required',
            'force' => 'nullable|boolean',
            'threshold' => 'nullable|numeric|between:0,1'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $index = $request->input('index');
        $force = $request->input('force', null);
        $docsThreshold = $force?0.0:(float)$request->input('threshold', 0.7);

        return $this->EsIndices->setAliasesLatest($index, $docsThreshold);
    }

}
