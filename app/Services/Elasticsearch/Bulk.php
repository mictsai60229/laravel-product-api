<?php

namespace App\Services\Elasticsearch;

Class Bulk{

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
        
        foreach ($requestBody as $fields){
            $params['body'][] = [
                $actionType => [
                    '_index' => $index,
                    '_id' => $fields['_id'],
                    '_type' => '_doc'
                ]
            ];
            unset($fields['_id']);
            $params['body'][] = $fields;
        }

        return $this->ESIndicesRepo->bulk($params);
    }
}