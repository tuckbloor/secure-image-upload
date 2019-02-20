<?php
    require_once __DIR__ . '/vendor/autoload.php';

// un comment if required

//        $whoops = new \Whoops\Run;
//        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
//        $whoops->register();


        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $test = new Siu\SecureImageUpload($_FILES);
            $test->save();
        }
    ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="1000000"/>
    <input type="file" name="Image"/>
    <input type="submit" value="Upload"/>
</form>


