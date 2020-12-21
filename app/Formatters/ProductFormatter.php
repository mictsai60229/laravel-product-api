<?php

namespace App\Formatters;

use Illuminate\Support\Facades\Validator;
use App\Formatters\BaseFormatter;


class ProductFormatter extends BaseFormatter{

    public $_name = "Product";

    protected $validationRules = [
        "_id" => "required",
        "prod_id" => "required|integer",
        "url_id" => "required|integer",
        "name" => "required",
        "introduction" => "required",
        "order" => "required|integer|min:0",
        "countries.*" => "regex:/^A\d{2}\-\d{3}$/",
        "cities.*" => "regex:/^A\d{2}\-\d{3}\-\d{5}$/"
    ];

    protected $transformFunctions = [];
}