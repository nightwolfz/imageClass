<?php
#-------------------------------------------------------------
# Ryan Megidov
# 20 July 2012
# Class used to create texts based on fonts with a background
#-------------------------------------------------------------
class imageClass {
    function __construct() {
        header('Content-Type: image/png');
    }

    function getRed($color){
        return hexdec('0x'.$_GET[$color]{0}.$_GET[$color]{1});
    }
    function getGreen($color){
        return hexdec('0x'.$_GET[$color]{2}.$_GET[$color]{3});
    }
    function getBlue($color){
        return hexdec('0x'.$_GET[$color]{4}.$_GET[$color]{5});
    }
    function getBackgroundColor($color){
        return isset($_GET[$color]) ? imagecolorallocate($this->im, $this->getRed($color), $this->getGreen($color), $this->getBlue($color)) : imagecolorallocate($this->im, 0, 0, 0);
    }
    function getTextColor($color){
        return isset($_GET[$color]) ? imagecolorallocate($this->im, $this->getRed($color), $this->getGreen($color), $this->getBlue($color)) : imagecolorallocate($this->im, 60, 60, 60);
    }

    function create(){
        $width = isset($_GET['width']) ? $_GET['width'] : 300;
        $height = isset($_GET['height']) ? $_GET['height'] : 16;

        $this->im = imagecreatetruecolor($width, $height);// Create the image

        $this->font = (isset($_GET['font']) && $_GET['font']) ? $_GET['font'] : 'arialbd.ttf';
        $this->fontSize = isset($_GET['size']) ? $_GET['size'] : 18;

        // Add the background
        $bgcolor = $this->getBackgroundColor('bgcolor');

        $this->createBackground($width, $height, $bgcolor);
        $this->createText();
    }

    function createText(){
        imagettftext($this->im, $this->fontSize, 0,
                isset($_GET['x']) ? $_GET['x'] : 2,
                isset($_GET['y']) ? $_GET['y'] : 8,
                $this->getTextColor('color'),
                dirname(__FILE__).'/../lib/'.$this->font, urldecode($_GET['text'])
        );
    }

    function createBackground($width, $height, $bgcolor){
        if (!isset($_GET['bgcolor'])){
            imagealphablending($this->im, false);
            imagefilledrectangle($this->im, 0, 0, $width, $height, imagecolorallocatealpha($this->im,0,0,0,127)); // This will make it transparent
            imagesavealpha($this->im, true);
        }else{
            imagefilledrectangle($this->im, 0, 0, $width, $height, $bgcolor);
        }
    }

    function getCacheFilePath(){
        $temp_name = (isset($_GET['text']) ? $_GET['text'] : '').(isset($_GET['color']) ? $_GET['color'] : '000000');
        $cachename = rtrim( strtr( base64_encode( $temp_name ), '+/', '-_' ), '=' );
        //@NEXT DEVELOPER : FOR DECODING, USE THE FOLLOWING : base64_decode( str_pad( strtr( $cachename, '-_', '+/' ), strlen( $cachename ) % 4, '=', STR_PAD_RIGHT ) );

        $cachepath = dirname(__FILE__)."/../auto/cache/$cachename.png";
        return $cachepath;
    }

    function displayIfCached(){
        $cachepath = $this->getCacheFilePath();

        if (file_exists($cachepath)){
            echo file_get_contents($cachepath);
            return true;
        }
        return false;
    }

    function cache(){
        $cachepath = $this->getCacheFilePath();
        imagepng($this->im, $cachepath);
        $oldumask = umask(0);
        chmod($cachepath, 0664);
        umask($oldumask);
    }

    function display(){
        if (!$this->displayIfCached()){
            if (!file_exists(dirname(__FILE__)."/../auto/cache")){
                $oldumask = umask(0);
                mkdir(dirname(__FILE__)."/../auto/cache", 0755);
                umask($oldumask);
            }

            // Create image
            $this->create();
            //$this->cache();
            //imagejpeg($im)
            imagepng($this->im);
            imagedestroy($this->im);
        }
    }
}
?>
