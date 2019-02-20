<?php

    class SecureImageUpload
    {
        protected $allowed_extensions = ["jpg", "jpeg", "png", 'gif'];

        protected $file;

        protected $file_name;

        protected $dir;

        protected $error = false;

        protected $error_array_message = [
            'The GD Extension Is Not Installed',
            'The exif_imagetype Function Does Not Exist',
            'Not A Valid File Extension',
            'Image Type Does Not Match Extension',
            'Image Is To Big',
            'Image Failed To Upload, Check File Directory Permissions'
        ];

        protected $size;

        protected $max_size = 100000000;

        protected $extension;

        protected $image_type;

        protected $tmp_name;

        protected $new_file_name;

        protected $img;

        /**
         * @param $file
         * @param string $dir
         * @message you may want to use .htaccess for more security in the uploaded file directory
         */
        public function __construct($file, $dir = '../../images') {

            if (!extension_loaded('gd')) {

                 $this->error(0);

                 return false;
            }

            else if (!function_exists('exif_imagetype')) {

                $this->error(1);

                return false;
            }

            $this->file      = $file;
            $this->dir       = $dir;
            $this->file_name = $file['Image']['name'];
            $this->tmp_name  = $file['Image']['tmp_name'];
            $this->size      = $file['Image']['size'];

            $this->validateExtension();
            $this->validateMemeType();
            $this->getImageSize();
            return true;

        }

        /**
         * @param $method
         * @param $args
         */
        public function __call($method, $args)
        {
            echo sprintf("Method %s Does Not Exist try save(); ", $method);
        }


        /**
         *
         */
        public function save()
        {
            if(!$this->error) {
                $this->renameFile();
                $this->createNewImage();
            }
        }


        /**
         *
         */
        protected function validateExtension()
        {
            //get the extension of the uploaded file
            $ext = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));

            //validate the file extension against the allow_extensions array
            if (!in_array($ext,$this->allowed_extensions) ) {

                $this->error(2);
                return false;
            }

            $this->extension = $ext;

            //find the IMAGETYPE for the extension type these return an (int)
            switch ($ext) {

                case "png":
                    $this->image_type = IMAGETYPE_PNG;
                break;

                case "jpg":
                case "jpeg":
                    $this->image_type = IMAGETYPE_JPEG;
                break;

                case "gif":
                    $this->image_type = IMAGETYPE_GIF;
                break;

            }

        }

        /**
         *
         */
        protected function validateMemeType()
        {
            //get the file type securely
            if (exif_imagetype($this->tmp_name) != $this->image_type ) {
                $this->error(3);
                return false;
            }

            return true;
        }

        /**
         *
         */
        protected function getImageSize()
        {
            //validate the image size
            if($this->size > $this->max_size) {
                $this->error(4);
                return false;
            }

            return true;
        }

        /**
         *
         */
        protected function renameFile()
        {
            //timestamp + unique id for the file_name
            return $this->new_file_name = time() . '-' . uniqid() . '-' .  '.' . $this->extension;
        }

        /**
         *
         */
        protected function createNewImage()
        {

            if(!is_dir($this->dir)) {
                mkdir($this->dir, 0777);
            }

            switch ($this->extension) {

                case "png":
                        $this->img = imagecreatefrompng($this->tmp_name);
                        imagepng($this->img, $this->dir . '/' . $this->new_file_name);
                    break;

                case "jpg":
                case "jpeg":
                        imagecreatefromjpeg($this->file);
                        imagejpeg($this->img, $this->dir . '/' . $this->new_file_name);
                        break;

                case "gif":
                        imagecreatefromgif($this->file);
                        imagegif($this->img, $this->dir . '/' . $this->new_file_name);
                        break;
            }

            //check to see if the image was su
            $this->checkIfFileExists();
        }


        /**
         *
         */
        protected function checkIfFileExists()
        {
            //check make sure the file was uploaded successfully
            if(file_exists($this->dir . '/' . $this->new_file_name)) {
                echo "The Image Was Uploaded Successfully";
                return true;
            }
            else {
                $this->error(5);
                return false;
            }
        }

        /**
         * @param $error
         * @return bool
         */
        protected function error($error)
        {
            //display the error
            echo $this->error_array_message[$error] . "<br>";
            $this->error = true;
            return false;
        }

        /**
         *
         */
        public function __destruct()
        {
            //destroy the image memory
            if(is_resource($this->img)) {
                imagedestroy($this->img);
            }
        }
    }