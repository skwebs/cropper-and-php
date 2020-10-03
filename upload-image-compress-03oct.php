<?php
//	set defaut timezone
date_default_timezone_set('Asia/Kolkata');
//	set img_directory for store image
$compress_img_dir = "./compressed_img/";
//	create directory if does not exist
if (!file_exists($compress_img_dir)) {
    mkdir($compress_img_dir, 0777, true);
}
//	set data array to store all data
$data   = array();
// error handling purpose
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    //	change extention jpeg into jpg
    if ($fileExt == 'jpeg') {
        $fileExt = 'jpg';
    }
    //	set cropped image name according to date & time
    $img_name = date("Ymd_His") . "." . $fileExt;
    if (file_exists($compress_img_dir)) {
        // blob method
        if (isset($action) && $action === "blob") {
            if ($_FILES['cropped_image']) {
                $img_tmp_name = $_FILES["cropped_image"]["tmp_name"];
                //save image in file"
                if (empty($errors)) {
                    $file = $_FILES['cropped_image']['tmp_name'];
                    list($width, $height, $type) = getimagesize($file);
                    $nwidth   = $width;
                    $nheight  = $height;
                    $newimage = imagecreatetruecolor($nwidth, $nheight);
                    if ($type == IMAGETYPE_JPEG) {
                        $source = imagecreatefromjpeg($file);
                        imagecopyresized($newimage, $source, 0, 0, 0, 0, $nwidth, $nheight, $width, $height);
                        $file_name = time() . '.jpg';
                        if (imagejpeg($newimage, $compress_img_dir . "IMG_S_" . $img_name)) {
                            // if want to upload original cropped image then uncomment below code
                            //$crop_img_dir = "./cropped_img/";if (!file_exists($crop_img_dir)){ mkdir($crop_img_dir, 0777, true);}
                            //move_uploaded_file($img_tmp_name, $crop_img_dir."IMG_C_".$img_name);
                            $image = true;
                        } else {
                            $errors["upload"] = "\nDid not upload server side compress image.\n";
                        };
                    }
                }
            } else {
                $errors['file'] = 'file not found.';
            }
        }
    } else {
        $errors['img_directory'] = 'img_directory ' . $img_dir . ' didn\'t found.';
    }
    //check image created or, not
    if ($image) {
        //	if image created
        $data['success'] = true;
        $data['imgPath'] = $compress_img_dir . "IMG_S_" . $img_name;
        $data['msg']     = 'Image saved successfully!';
        $data["imgName"] = "IMG_S_" . $img_name;
    } else {
        //	if image didn't created
        $data['success'] = false;
        $data['msg']     = 'Image did not create.';
        $data['errors']  = $errors;
    };
    //	all array data encode into json for client
    echo json_encode($data);
}
?>