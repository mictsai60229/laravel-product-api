<?php

namespace App\Services\Elasticsearch;

Class Bulk{

    protected $ESIndicesRepo;

    public function __construct(){
        $this->ESIndicesRepo = app('Repository\Elasticsearch\Indices');
    }

    /**
     * Undocumented function
     *
     * @param string $index
     * @param string $actionType
     * @param array $actions
     * @return void
     */
    public function bulk(string $index, string $config, string $actionType, array $actions, string $validateRange){
        
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
                    '_index' => $index,
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

        return $this->ESIndicesRepo->bulk($params);
    }

    
}