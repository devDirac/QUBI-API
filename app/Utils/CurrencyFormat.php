<?php

namespace App\Utils;
use App\Http\Controllers\API\BaseController as BaseController;

class CurrencyFormat extends BaseController
{

   public function moneyFormat($price)
    {
        //echo intval($price);
        return number_format(intval($price),2,'.',',');
    }

}
