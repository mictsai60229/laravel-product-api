<?php

namespace App\Services\Elasticsearch;

use Illuminate\Support\Facades\Storage;

Class Indices{

    protected $ESIndicesRepo;
    protected $ESCatRepo;

    public function __construct(){
        $this->ESIndicesRepo = app('Repository\Elasticsearch\Indices');
        $this->ESCatRepo  = app('Repository\Elasticsearch\Cat'); 
    }

    /*
    * Create index, set the newest index alias to "{$index}-latest"
    * @param string $index, string $configPath, int $backupCount
    * @return 
    */
    public function create(string $index, string $configPath, int $backupCount = 1){
        
        $indices = $this->countIndex($index);

        // delete index not in backupCount
        if ($backupCount !== -1){
            $indicesCount = count($indices);
            for ( $i=$backupCount ; $i<$indicesCount ; $i++ ){
                $this->delete($indices[$i]);
            }
        }

        //add timestamp
        $date = date("YmdHis");
        $indexTimestamp = "{$index}-{$date}";

        $params = [
            'index' => $indexTimestamp,
            'body' => $this->getCreateBody($configPath)
        ];

        $response = $this->ESIndicesRepo->create($params);
        
        //set alias to "{$index}-newest"
        $createIndex = $response["index"];
        $indexAlias = "{$index}-latest";
        $this->setAliases($createIndex, $indexAlias);
        $response['alias'] = $indexAlias;

        return $response;
        
    }

    /*
    * Delete index
    * @param string $index
    * @return Json
    */
    public function delete(string $index){
        
        $params = [
            "index" => $index
        ];

        return $this->ESIndicesRepo->delete($params);
    }

    /*
    * Refresh Index
    * @param string $index
    * @return Json
    */
    public function refresh(string $index){
        
        $params = [
            'index' => $index
        ];

        return $this->ESIndicesRepo->refresh($params);
    }

    /*
    * Set refresh_interval of Index
    * @param string $index, string interval
    * @return Json
    */
    public function setInterval(string $index, string $interval){
        
        $params = [
            'index' => $index,
            'body' => ['refresh_interval' => $interval ]
        ];

        return $this->ESIndicesRepo->putSettings($params);
    }

    /*
    * Set Aliases to index
    * @param string $targetIndex, string alias
    * @return Json
    */
    public function setAliases(string $targetIndex, string $alias){
        
        # indices with name $alias
        $removeIndices = $this->catAliases($alias);
        $response = [];
        
        $response['remove'] = [];
        foreach ($removeIndices as $index){
            $this->updateAliases("remove", $index, $alias);
            $response['remove'][] = $index;
        }

        $this->updateAliases("add", $targetIndex, $alias);
        $response['add'] = $targetIndex;
        $response['acknowledge'] = true;

        return $response;
    }

    /*
    * Set Alias to the newest created not empty index
    * @param string $index
    * @return Array[string]
    */
    public function setAliasesLatest(string $index){
        
        $indices = $this->countIndex($index);
        return $this->setAliases($indices[0], $index);
    }

    /*
    * Count index starts with $index, sort according to content
    * @param string $index
    * @return Array[string]
    */
    private function countIndex(string $index){
        
        $indicesInfo = $this->ESCatRepo->indices([]);

        //filter index with "{$index}-{$timestamp}"
        $newIndicesInfo = [];
        $pattern = "/{$index}\-\d{14}/";
        foreach ($indicesInfo as $indexInfo){
            if (preg_match($pattern, $indexInfo['index'],$matches)){
                $newIndicesInfo[] = $indexInfo;
            }
        }
        $indicesInfo = $newIndicesInfo;

        //sort with product-name
        usort($indicesInfo, function($a, $b){
            return strcmp($a['index'], $b['index']);
        });

        //sort with empty document and full document
        usort($indicesInfo, function($a, $b){
            if ($a['docs.count'] === $b['docs.count']){
                return 0;
            }
            else if($a['docs.count'] === 0){
                return -1;
            }
            else if($b['docs.count'] === 0){
                return 1;
            }
            return 0;
        });

        $indices = [];
        foreach ($indicesInfo as $indexInfo){
            $indices[] = $indexInfo['index'];
        }

        $indices = array_reverse($indices);

        return $indices;
    }

    /*
    * get elasticsearch mappings and settings
    * @param string $configPath
    * @return string (raw Json)
    */
    private function getCreateBody(string $configPath){
        
        $mappingsJson = Storage::disk('local')->get("{$configPath}/mappings.json", '{}');
        $settingsJson = Storage::disk('local')->get("{$configPath}/settings.json", '{}');

        return "{
            \"mappings\" : {$mappingsJson},
            \"settings\" : {$settingsJson}
        }";
    }

    /*
    * return the index point to alias
    * @param string $name
    * @return array(string)
    */
    private function catAliases(string $name){
        
        $params = [
            'name' => $name
        ];

        $aliases_result = $this->ESCatRepo->aliases($params);
        $indices = [];

        foreach($aliases_result as $alias_result){
            $indices[] = $alias_result["index"];
        }

        return $indices;
    }

    /*
    * Update Alias settings
    * @param string $action, string $index, string $alias
    * @return Json
    */
    public function updateAliases(string $action, string $index, string $alias){


        $params = [
            'body' => [
                'actions' => [
                    [
                        $action => [
                            'index' => $index,
                            'alias' => $alias
                        ]
                    ]
                ]
            ]
        ];

        if ($action === "add"){
            $params['body']['actions'][0][$action]["is_write_index"] = True;
        }

        return $this->ESIndicesRepo->updateAliases($params);
    }
}