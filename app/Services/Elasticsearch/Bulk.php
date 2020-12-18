<?php

namespace App\Services\Elasticsearch;

Class Bulk{

    /*
    * Bulk index
    * @param string $index, string actionType, array $actions
    * @return Json
    */
    public function bulk(string $index, string $actionType, array $actions){
        
        $params = ['body' => []];
        
        foreach ($requestBody as $fields){
            $params['body'][] = [
                $actionType => [
                    '_index' => $index,
                    '_type' => '_doc'
                ]
            ];
            $params['body'][] = $fields;
        }

        return $this->ESIndicesRepo->bulk($params);
    }
}