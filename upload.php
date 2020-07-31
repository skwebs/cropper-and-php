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
		
		//	set cropped image name according to date & time
		$new_img_name = time()."-".date("dFY-Hi").".".$fileExt; 
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
						$image = move_uploaded_file($img_tmp_name, $image_path);
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
					$image = file_put_contents($imagePath, $imgData);
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