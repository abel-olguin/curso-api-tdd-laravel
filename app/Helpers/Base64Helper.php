<?php

namespace App\Helpers;

class Base64Helper
{
    public static function getDataImage(string $base64){
        $parts  = explode(",", $base64);
        $data = end($parts);

        try{
            $decode = base64_decode($data);
            imagecreatefromstring($decode);
            $size = getimagesizefromstring($decode);
            $allowed = ['image/png', 'image/jpeg'];

            if(!$size || !$size[0] || !$size[1] || !$size['mime'] || !in_array($size['mime'], $allowed)){
                return [];
            }
            $parts = explode("/", $size['mime']);
            $extension = end($parts);
            return [$decode, $extension];
        }catch (\Exception $e){
            return [];
        }
    }
}
