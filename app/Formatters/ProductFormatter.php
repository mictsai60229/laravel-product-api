<?php

namespace App\Formmatter;

use Illuminate\Support\Facades\Validator;


class ProductFormatter{

    protected $_name = "Product";

    private function getValidationRules(){
        return [
            "prod_id" => "required|integer",
            "url_id" => "required|integer",
            "name" => "required",
            "introduction" => "required",
            "order" => "required|integer|min:0",
            "countries.*" => "regex:/^A\d{2}\-\d{3}$/",
            "cities.*" => "regex:/^A\d{2}\-\d{3}\-\d{5}$/"
        ];
    }

    private function getTransformFunctions(){
        return [];
    }
}