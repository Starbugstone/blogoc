<?php

namespace App\Controllers\Ajax;

use Cocur\Slugify\Slugify;
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
    private function isImageValid($image): bool
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
     * Check if file exists and add a number to avoid overwrite
     * @param string $folder destination folder
     * @param string $file destination filename
     * @return string the unique file name
     */
    private function getFilename(string $folder, string $file): string
    {
        //slugify the file name to avoid security errors or bugs with special characters.
        $fileName = pathinfo($file, PATHINFO_FILENAME );
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION );
        $slugify = new Slugify();
        $fileName = $slugify->slugify($fileName);
        //if the filename has only special chars, the slugify will be empty, create a unique ID
        if($fileName ==="")
        {
            $fileName = uniqid();
        }
        $file = $fileName.".".$fileExtension;
        $fileUrl = $folder . $file;
        $docRoot = $this->request->getDocumentRoot();
        $filePath = $docRoot . "/public/" . $fileUrl;
        if (file_exists($filePath) !== 1) {
            $fileNum = 0;
            while (file_exists($filePath)) {
                $fileUrl = $folder . $fileNum . "_" . $file;
                $filePath = $docRoot . "/public/" . $fileUrl;
                $fileNum += 1;
            }
        }
        return $fileUrl;
    }

    /**
     * @param $tempFile array
     * @param $folder string
     */
    private function fileInputUpload(array $tempFile, string $folder)
    {
        if (is_uploaded_file($tempFile['tmp_name'])) {
            if (!$this->isImageValid($tempFile['name'])) {
                echo json_encode(array('error' => 'Invalid name or file extension'));
                return;
            }

            $filetowrite = $this->getFilename($folder, basename($tempFile['name']));
            move_uploaded_file($tempFile['tmp_name'], $filetowrite);

            // Respond to the successful upload with JSON.
            echo json_encode(array('location' => $filetowrite));
        } else {
            // Notify editor that the upload failed
            echo json_encode(array('error' => 'Upload failed, file might be too big'));

        }

    }

    /**
     * Upload images from TinyMCE
     * grabbed from https://www.codexworld.com/tinymce-upload-image-to-server-using-php/
     */
    public function tinymceUpload()
    {
        //security checks, only admins can upload images to posts
        $this->onlyAdmin();
        $this->onlyPost();

        $tempFile = $this->request->getUploadedFiles();

        //need to clean up
        if (is_uploaded_file($tempFile['tmp_name'])) {
            if (!$this->isImageValid($tempFile['name'])) {
                header("HTTP/1.1 400 Invalid file name or file extension.");
                return;
            }

            $filetowrite = $this->getFilename($this->imageFolder, basename($tempFile['name']));
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
        $this->onlyPost();
        $tempFile = $this->request->getUploadedFiles();

        $this->fileInputUpload($tempFile, $this->configFolder);

    }

    /**
     * Upload for the file input in the configuration
     */
    public function fileInputPostUpload()
    {
        //security checks, only admins can upload images to config
        $this->onlyAdmin();
        $this->onlyPost();
        $tempFile = $this->request->getUploadedFiles();
        $this->fileInputUpload($tempFile, $this->imageFolder);
    }

    /**
     * Upload for the file input in the configuration
     */
    public function fileInputUserUpload()
    {
        //security checks, only admins can upload images to config
        $this->onlyUser();
        $this->onlyPost();
        $tempFile = $this->request->getUploadedFiles();
        $this->fileInputUpload($tempFile, $this->userFolder);
    }

}