<?php
namespace App\Controllers\Ajax;

use Core\AjaxController;

class ImageUpload extends AjaxController{
    /**
     * @var string the image upload folder, must be writable
     */
    private $imageFolder = "uploaded_images/";

    public function tinymceUpload(){

        //image uploader for tinymce
        //grabbed from https://www.codexworld.com/tinymce-upload-image-to-server-using-php/


        // Images upload path
        //$imageFolder = "uploaded_images/";

        $tempFile = $this->request->getUploadedFiles();

        //need to clean up
        if(is_uploaded_file($tempFile['tmp_name'])){
            // Sanitize input
            if(preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $tempFile['name'])){
                header("HTTP/1.1 400 Invalid file name.");
                return;
            }

            // Verify extension
            if(!in_array(strtolower(pathinfo($tempFile['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))){
                header("HTTP/1.1 400 Invalid extension.");
                return;
            }


            $filetowrite = $this->imageFolder . $tempFile['name'];
            move_uploaded_file($tempFile['tmp_name'], $filetowrite);

            // Respond to the successful upload with JSON.
            echo json_encode(array('location' => $filetowrite));
        } else {
            // Notify editor that the upload failed
            header("HTTP/1.1 500 Server Error");
        }


    }




}