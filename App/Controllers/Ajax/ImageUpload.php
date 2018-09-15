<?php

namespace App\Controllers\Ajax;

use Core\AjaxController;

class ImageUpload extends AjaxController
{
    /**
     * @var string the image upload folder, must be writable
     */
    private $imageFolder = "uploaded_images/";
    private $configFolder = "config_images/";
    private $userFolder = "user_images/";

    /**
     * check if the image name is valid
     * @param $image string filename to check
     * @return bool if image name is valid
     *
     */
    private function isimageValid($image)
    {
        // Sanitize input
        if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $image)) {
            return false;
        }

        // Verify extension
        if (!in_array(strtolower(pathinfo($image, PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
            return false;
        }

        return true;
    }

    /**
     * Upload images from TinyMCE
     * grabbed from https://www.codexworld.com/tinymce-upload-image-to-server-using-php/
     */
    public function tinymceUpload()
    {
        //security checks, only admins can upload images to posts
        $this->onlyAdmin();
        if (!$this->container->getRequest()->isPost()) {
            throw new \Core\JsonException('Call is not post');
        }

        $tempFile = $this->request->getUploadedFiles();

        //need to clean up
        if (is_uploaded_file($tempFile['tmp_name'])) {
            if (!$this->isimageValid($tempFile['name'])) {
                header("HTTP/1.1 400 Invalid file name or file extension.");
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


    /**
     * Upload for the file input in the configuration
     */
    public function fileInputConfigUpload()
    {
        //security checks, only admins can upload images to config
        $this->onlyAdmin();
        if (!$this->container->getRequest()->isPost()) {
            throw new JsonException('Call is not post');
        }
        $tempFile = $this->request->getUploadedFiles();

        //need to clean up
        if (is_uploaded_file($tempFile['tmp_name'])) {
            if (!$this->isimageValid($tempFile['name'])) {
                echo json_encode(array('error' => 'Invalid name or file extension'));
                return;
            }

            $filetowrite = $this->configFolder . $tempFile['name'];
            move_uploaded_file($tempFile['tmp_name'], $filetowrite);

            // Respond to the successful upload with JSON.
            echo json_encode(array('location' => $filetowrite));
        } else {
            // Notify editor that the upload failed
            echo json_encode(array('error' => 'Upload failed'));
        }
    }


}