<?php

namespace App\Formatters;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


#transform
#validation


class BaseFormatter{

    protected $transformFunctions = [];
    protected $validationRules = [];
    public $_name = "Base";

    /*
    *
    *@retrun 
    */
    public function validate(array $data, string $range="all"){

        $validator = $this->getValidator($data, $range);
        if ($validator->fails()){

            $validatorErrorMessages = json_encode($validator->errors());
            Log::error("{$this->_name} Formatter Error on : {$validatorErrorMessages}");

            return null;
        }

        return $this->transform($data);
    }


    public function transform(array $data){

        foreach (array_keys($data) as $key){
            if (array_key_exists($key, $this->transformFunctions)){
                $data[$key] = $this->transformFunctions[$key]($data[$key]);
            }
        }

        return $data;
    }

    public function getValidator(array $data, string $range){

        if ($range === "all"){
            return Validator::make($data, $this->validationRules);
        }
        else if($range === "part"){

            $validationRules = [];
            foreach (array_keys($data) as $key){
                if (array_key_exists($key, $this->validationRules)){
                    $validationRules[$key] = $this->validationRules[$key];
                }
            }
            return Validator::make($data, $validationRules);
        }
    }
    
}