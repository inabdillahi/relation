<?php 

namespace App\Services;

class Slug{


    public function slug($variable){

        $slug = preg_replace('/[^a-z0-9]+/','-',trim(strtolower($variable)));
        return $slug;
    }
}
