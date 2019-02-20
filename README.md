# Secure Image Upload

    A simple php script that securely uploads images

    Checks the extension type

    Checks the image size

    Checks the image type using

    recreates A new image from the uploaded image


     The following are PHP requirements

      1) PHP >= 5.3
      2) GD Extension
      3) exif_imagetype function

    For Testing

    <?php

        //include the image SecureImageUpload Class here

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $test = new SecureImageUpload($_FILES);
            $test->save();
        }
    ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="1000000"/>
        <input type="file" name="Image"/>
        <input type="submit" value="Upload"/>
    </form>

