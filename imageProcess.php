<?php
/**
 * @author Serkan Ceylan
 * @license GPL
 * @version 1.1
 */

class imageProcess extends CApplicationComponent
{
    /**
     * Belirlenen boyutlarda resmi bozmadan, beyaz alan ekleyerek olusturur, filigran ekler ve kaydeder
     * @param $r_w
     * @param $r_h
     * @param $orjYol
     * @param $hedefYol
     * @param $dosya_ad
     * @param bool $watermark
     */
    public function resimThumbOlustur($r_w, $r_h, $orjYol, $hedefYol, $dosya_ad, $watermark = true)
    {
        $new_image = imagecreatetruecolor($r_w, $r_h);
        $orj_image = null;

        $info = getimagesize($orjYol.$dosya_ad);
        if($info['mime'] == "image/jpeg"){
            $orj_image = imagecreatefromjpeg($orjYol.$dosya_ad);
        }
        elseif($info['mime'] == "image/png"){
            $orj_image = imagecreatefrompng($orjYol.$dosya_ad);
        }
        elseif($info['mime'] == "image/gif"){
            $orj_image = imagecreatefromgif($orjYol.$dosya_ad);
        }

        imagefill($new_image, 0, 0, 0xFFFFFF);

        $orjW = imagesx($orj_image);
        $orjH = imagesy($orj_image);
        $hW = 0;
        $hH = 0;
        $posX = 0;
        $posY = 0;
        if(($orjW / $r_w) > ($orjH / $r_h)){
            $hW = $r_w;
            $hH = intval($orjH / ($orjW / $r_w));
            $posX = 0;
            $posY = round(($r_h - $hH) / 2);
        }
        else{
            $hH = $r_h;
            $hW = intval($orjW / ($orjH / $r_h));
            $posY = 0;
            $posX = round(($r_w - $hW) / 2);
        }

        imagecopyresampled($new_image, $orj_image, $posX, $posY, 0, 0, $hW, $hH, $orjW, $orjH);

        if($watermark){
            $filigran = imagecreatefrompng(Yii::app()->getBasePath().'/../images/filigran.png');
            $f_w = imagesx($filigran);
            $f_h = imagesy($filigran);
            $fPosX = round(($r_w - $f_w) / 2);
            $fPosY = round(($r_h - $f_h) / 2);
            imagecopyresampled($new_image, $filigran, $fPosX, $fPosY, 0, 0, $f_w, $f_h, $f_w, $f_h);
            @imagedestroy($filigran);
        }

        if(!file_exists($hedefYol))
            mkdir($hedefYol, 0777, true);

        imagejpeg($new_image, $hedefYol.$dosya_ad, 80);
        @imagedestroy($new_image);
        @imagedestroy($orj_image);
    }

    /**
     * Belirlenen boyutlarda resmi kirparak olusturur ve kaydeder
     * @param $r_w
     * @param $r_h
     * @param $orjYol
     * @param $hedefYol
     * @param $dosya_ad
     */
    public function resimThumbCrop($r_w, $r_h, $orjYol, $hedefYol, $dosya_ad){
        $new_image = imagecreatetruecolor($r_w, $r_h);
        $orj_image = null;
        $info = getimagesize($orjYol.$dosya_ad);
        if($info['mime'] == "image/jpeg"){
            $orj_image = imagecreatefromjpeg($orjYol.$dosya_ad);
        }
        elseif($info['mime'] == "image/png"){
            $orj_image = imagecreatefrompng($orjYol.$dosya_ad);
        }
        elseif($info['mime'] == "image/gif"){
            $orj_image = imagecreatefromgif($orjYol.$dosya_ad);
        }

        //imagefill($new_image, 0, 0, 0xFFFFFF);

        $orjW = imagesx($orj_image);
        $orjH = imagesy($orj_image);
        $hW = 0;
        $hH = 0;
        $posX = 0;
        $posY = 0;
        $orj_aspect = $orjW / $orjH;
        $thmb_aspect = $r_w / $r_h;
        if($orj_aspect >= $thmb_aspect){
            $hH = $r_h;
            $hW = $orjW / ($orjH / $r_h);
        }
        else{
            $hW = $r_w;
            $hH = $orjH / ($orjW / $r_w);
        }

        imagecopyresampled($new_image, $orj_image, (0 - ($hW - $r_w) / 2), (0 - ($hH - $r_h) / 2), 0, 0, $hW, $hH, $orjW, $orjH);

        if(!file_exists($hedefYol))
            mkdir($hedefYol, 0777, true);

        imagejpeg($new_image, $hedefYol.$dosya_ad.".jpg", 80);
        @imagedestroy($new_image);
        @imagedestroy($orj_image);
    }
}

?>
