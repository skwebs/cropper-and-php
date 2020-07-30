<?php
	/*/	set defaut timezone 
	date_default_timezone_set('Asia/Kolkata');
	
	//	DATA RECEIVED FROM CLIENT SIDE
	//	file extension
	$fileExt = $_POST['fileExt'];
	
	//	cropped image 
	$croppedImageDataURL = $_POST['croppedImageFile'];
	
	//	change extention jpeg into jpg
	if($fileExt=='jpeg'){$fileExt = 'jpg';}
	
	//	set data array to store all data
	$data = [];
	
	//	set cropped image name according to date & time
	$croppedImageName = time()."-".date("dFY-Hi").".".$fileExt; 
	//$_POST['fileExt'];
	
	//	set folder for store image
	$folder = "./cropped_img/";
	
	//	create directory if does not exist
	if (!file_exists($folder)) {
	    mkdir($folder, 0777, true);
	}
	
	//	set image folder path
	$imagePath = $folder.$croppedImageName;
	
	//	Delete old image if exist.
	if (file_exists($imagePath)) {
		if(!unlink($imagePath)){
			$data["success"] = false ;
			$data["error"] = true ;
			$data["msg"] = "Old image could not delete!";
		};
	}
	
	// remove data url (data:image/png;base64) from image source
	$img = str_replace('data:image/'.$_POST['fileExt'].';base64,', '', $croppedImageDataURL);
	
	// replace space to +
	$img = str_replace(' ', '+', $img);
	
	//decode base64 code
	$imgData = base64_decode($img);
	
	//save image in file
	if(!$data[error]){
		$image = file_put_contents($imagePath, $imgData);
	}
	
	//check image created or, not
	if($image){
		//	if image created 
		$data['success'] = true;
		$data['imgPath'] = $imagePath; 
		$data['msg'] = 'Image saved successfully!';
		$data["imgName"] = $croppedImageName;
	} else {
		//	if image didn't created 
		$data['success'] = false;
		$data['msg'] = 'Image did not create.';
	};
	//	all array data encode into json for client
	echo json_encode($data);
	*/
?>

<?php
	//	set defaut timezone 
	date_default_timezone_set('Asia/Kolkata');
	
	//	create directory if does not exist
	if (!file_exists($folder)) {
		mkdir($folder, 0777, true);
	}
	
	//	set data array to store all data
	$data = [];
	
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
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
		
		//	set folder for store image
		$folder = "./cropped_img/";
		
		//	set image folder path
		$image_path = $folder.$new_img_name;
		
		
		
		//	Delete old image if exist.
		if (file_exists($image_path)) {
			if(!unlink($image_path)){
				$data["success"] = false ;
				$data["error"] = true ;
				$data["msg"] = "Old image could not delete!";
			};
		}
		
		
		// blob method
		if(isset($dataURL_or_blob) && $dataURL_or_blob === "blob"){
			// 
			if($_FILES['cropped_image']){
				$img_tmp_name = $_FILES["cropped_image"]["tmp_name"];
				$target_file = $folder.$new_img_name;
				//save image in file
				if(!$data[error]){
					$image = move_uploaded_file($img_tmp_name, $target_file);
				}
			}
		}
		
		
		// dataURL method
		if(isset($dataURL_or_blob) && $dataURL_or_blob === "dataURL"){
			// remove data url (data:image/png;base64) from image source
			$img = str_replace('data:image/'.$fileExt.';base64,', '', $croppedImageDataURL);
			
			// replace space to +
			$img = str_replace(' ', '+', $img);
			
			//decode base64 code
			$imgData = base64_decode($img);
			
			//save image in file
			if(!$data[error]){
				$image = file_put_contents($imagePath, $imgData);
			}
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
		};
		//	all array data encode into json for client
		echo json_encode($data);
	}
?>