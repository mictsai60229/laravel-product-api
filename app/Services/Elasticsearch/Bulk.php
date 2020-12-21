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
    public function bulk(string $index, string $actionType, array $actions){
        
        $params = ['body' => []];
        
        foreach ($actions as $action){
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