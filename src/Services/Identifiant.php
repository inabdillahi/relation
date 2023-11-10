<?php 

namespace App\Services;

class Identifiant{


    public function codeIdentifiant($long){
        return strtoupper(substr(md5(uniqid()),0,$long));
    }
}



?>