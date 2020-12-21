<?php

namespace App\Formatters;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


#transform
#validation


class BaseFormatter{

    protected $transformFunctions;
    protected $validationRules;
    protected $_name = "Base";

    public function __construct(){
        $this->transformFunctions = $this->getTransformFunctions();
        $this->validationRules = $this->getValidationRules();
    }

    /*
    *
    *@retrun 
    */
    public function validate(array $data){

        $validateData = [];

        $validator = $this->getValidator($data);
        if ($validator->fails()){

            $validatorErrorMessages = json_encode($validator->errors());
            Log::error("{$this->_name} Formatter Error on : {$validatorErrorMessages}");

            return null;
        }

        foreach (array_keys($data) as $key){
            if (array_key_exists($key, $this->validationRules)){
                $validateData[$key] = $data[$key];
            }
        }
        return $this->transform($validateData);
    }


    public function transform(array $data){

        foreach (array_keys($data) as $key){
            if (array_key_exists($key, $this->transformFunctions)){
                $data[$key] = $this->transformFunctions[$key]($data[$key]);
            }
        }

        return $data;
    }

    public function getValidator(array $data){
        return Validator::make($data, $this->validationRules);
    }

    private function getValidationRules(){
        return [];
    }

    private function getTransformFunctions(){
        return [];
    }
    
}