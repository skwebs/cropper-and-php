<?php
	//	set defaut timezone 
	date_default_timezone_set('Asia/Kolkata');
	
	//	set img_directory for store image
	$img_directory = "./cropped_img/";
	//	create directory if does not exist
	if (!file_exists($img_directory)) {
		mkdir($img_directory, 0777, true);
	}
	
	
	//	set data array to store all data
	$data = [];
	// error handling purpose
	$errors = [];$post = [];$fl = array();
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$post = $_POST;
		extract($_POST);
		//	DATA RECEIVED FROM CLIENT SIDE
		
		//	file extension
		//$fileExt = $_POST['fileExt'];
		
		//	cropped image blob
		
		//	change extention jpeg into jpg
		if($fileExt=='jpeg'){$fileExt = 'jpg';}
	//	echo $fileExt;
		//	set cropped image name according to date & time
		$new_img_name = "IMG_CROP_".time(); //date("Ymd_His"); //.".".$fileExt; 
		//$new_img_name = time()."-".date("dFY-Hi").".".$fileExt; 
		//$new_img_name = date("dFY").".".$fileExt; 
		//$_POST['fileExt'];
		
		//	set image img_directory path
		$image_path = $img_directory.$new_img_name;
		
		
		
		//	Delete old image if exist.
		if (file_exists($image_path)) {
			if(!unlink($image_path)){
				$data["success"] = false ;
				$data["error"] = true ;
				$errors["msg"] = "Old image could not delete!";
			};
		}
		
		if (file_exists($img_directory)) {
			// blob method
			if(isset($action) && $action === "blob"){
				$fl = $_FILES;
				// 
				if($_FILES['cropped_image']){
					$img_tmp_name = $_FILES["cropped_image"]["tmp_name"];
					//save image in file
					if(empty($errors)){
						
						$file=$_FILES['cropped_image']['tmp_name'];
						list($width,$height)=getimagesize($file);
						$nwidth=$width;
						$nheight=$height;
						$newimage=imagecreatetruecolor($nwidth,$nheight);
						if($_FILES['cropped_image']['type']=='image/jpeg'){
						$source=imagecreatefromjpeg($file);
						imagecopyresized($newimage,$source,0,0,0,0,$nwidth,$nheight,$width,$height);
						$file_name=time().'.jpg';
						if(imagejpeg($newimage,'cropped_img/n_'.$file_name)){
							if(move_uploaded_file($img_tmp_name, )){
								echo "new img created by new script\n";
							};
						}else{
						echo "new img not created by new script\n";
						};
						}
						//////////{{{{{{{{{{{{{{{{
						
						function resizeImage($resourceType,$image_width,$image_height,$resizeWidth,$resizeHeight) {
							// $resizeWidth = 100;
							// $resizeHeight = 100;
							$imageLayer = imagecreatetruecolor($resizeWidth,$resizeHeight);
							imagecopyresampled($imageLayer,$resourceType,0,0,0,0,$resizeWidth,$resizeHeight, $image_width,$image_height);
							return $imageLayer;
						}
						
						if(isset($_FILES["cropped_image"]) && $_POST["action"] == "blob") {
							$imageProcess = 0;
							if(is_array($_FILES)) {
							
								//$new_width = 100; // $_POST['new_width'];
								//$new_height = 100; //$_POST['new_height'];
								$fileName = $_FILES['cropped_image']['tmp_name'];
								$sourceProperties = getimagesize($fileName);
								$resizeFileName = $new_img_name; //time();
								$uploadPath = $img_directory; // "./uploads/";
								//$fileExt = pathinfo($_FILES['cropped_image']['name'], PATHINFO_EXTENSION);
								$uploadImageType = $sourceProperties[2];
								$sourceImageWidth = $sourceProperties[0];
								$sourceImageHeight = $sourceProperties[1];
								
								switch ($uploadImageType) {
									case IMAGETYPE_GIF:
										$resourceType = imagecreatefromgif($fileName); 
										$imageLayer = resizeImage($resourceType,$sourceImageWidth,$sourceImageHeight,$sourceImageWidth,$sourceImageHeight); //,$new_width,$new_height);
										imagegif($imageLayer,$uploadPath.$resizeFileName."_server".'.'.$fileExt);
									break;
									
									case IMAGETYPE_JPEG:
										$resourceType = imagecreatefromjpeg($fileName); 
										$imageLayer = resizeImage($resourceType,$sourceImageWidth,$sourceImageHeight,$sourceImageWidth,$sourceImageHeight); //,$new_width,$new_height);
										imagejpeg($imageLayer,$uploadPath.$resizeFileName."_server".'.'.$fileExt);
									break;
									case IMAGETYPE_PNG:
										$resourceType = imagecreatefrompng($fileName); 
										$imageLayer = resizeImage($resourceType,$sourceImageWidth,$sourceImageHeight,$sourceImageWidth,$sourceImageHeight); //,$new_width,$new_height);
										imagepng($imageLayer,$uploadPath.$resizeFileName."_server".'.'.$fileExt);
									break;
									
									default:
										$imageProcess = 0;
									break;
									
								}
								if(move_uploaded_file($fileName, $uploadPath. $resizeFileName."_client". ".". $fileExt))
								$imageProcess = 1;
							}
							
							if($imageProcess == 1){
								$image = true;
							}else{
								$image = false;
							}
							$imageProcess = 0;
						}
						/////////}}}}}}}}}}}}}}}}/
						
					//	$image = move_uploaded_file($img_tmp_name, $image_path);
					//}
					}
				}else{
					$errors['file'] = 'file not found.';
				}
			}
			
			
			// dataURL method
			if(isset($action) && $action === "dataURL"){
				// remove data url (data:image/png;base64) from image source
				$img = str_replace('data:image/'.$fileExt.';base64,', '', $croppedImageDataURL);
				
				// replace space to +
				$img = str_replace(' ', '+', $img);
				
				//decode base64 code
				$imgData = base64_decode($img);
				
				//save image in file
				if(empty($errors)){
					$image = file_put_contents($imagePath.".".$fileExt, $imgData);
				}
			}
		}else{
			$errors['img_directory'] = 'img_directory '.$img_directory.' didn\'t found.';
		}
		
		
		//check image created or, not
		if($image){
			//	if image created 
			$data['success'] = true;
			$data['imgPath'] = $image_path; 
			$data['msg'] = 'Image saved successfully!';
			$data["imgName"] = $new_img_name;
		} else {
			//	if image didn't created 
			$data['success'] = false;
			$data['msg'] = 'Image did not create.';
			$data['errors'] = $errors;
			$data['post'] = $post;
			$data['fl'] = $fl;
		};
		//	all array data encode into json for client
		echo json_encode($data);
	}
?>