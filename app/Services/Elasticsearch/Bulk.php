<?php

namespace App\Services\Elasticsearch;

use App\Exceptions\CommonApiException;

Class Bulk extends Indices{

    /**
     * Undocumented function
     *
     * @param string $index
     * @param string $actionType
     * @param array $actions
     * @return void
     */
    public function bulk(string $index, string $config, string $actionType, array $actions, string $validateRange, bool $force){

        $indexLatest = "{$index}-latest";
        // check {$index}-latest is setted
        if (!$force && count($this->catAliases($indexLatest)) == 0){
            throw new CommonApiException("Index with name {$index}-latest doesn't exist.");
        }

        $formatter = app("Formatter/{$config}");

        $validatedActions = [];
        $failureActions = [];
        foreach($actions as $action){

            $validatedAction = $formatter->validate($action, $validateRange);
            if (empty($validatedAction)){
                $failureActions[] = $action;
            }
            else{
                $validatedActions[] = $validatedAction;
            }
        }

        if (empty($validatedActions)){
            $response = [];
            $response['failure'] = $failureActions;

            return $responses;
        }
        
        $params = ['body' => []];
        
        foreach ($validatedActions as $action){
            $params['body'][] = [
                $actionType => [
                    '_index' => $indexLatest,
                    '_id' => $action['_id'],
                    '_type' => '_doc'
                ]
            ];

            unset($action['_id']);
            if ($actionType === "index"){
                $params['body'][] = $action;
            }
            else if($actionType === "update"){
                $params['body'][] = [
                    "doc" => $action
                ];
            }
            
        }

        return $this->EsIndicesRepo->bulk($params);
    }
    
}