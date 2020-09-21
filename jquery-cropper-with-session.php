<?php
  session_start();
  
  
  $img_dir = "./cropped_img/".$_SESSION["user"]."_profile.";
  $jpg = $img_dir."jpg";
 $png = $img_dir."png";
  if(file_exists($jpg)){
  $img_dir = $jpg;
  }else 
  if(file_exists($png)){
  $img_dir = $png;
  }else{
  $img_dir = "./assets/img/user-vector.jpg";
  }
  
  ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Cropper.js</title>
  <!-- bootstrap -->
  <link href="./assets/vendor/bootstrap/4.5.0/bootstrap.min.css" rel="stylesheet">
  <!-- cropper -->
  <link href="./assets/vendor/cropper/cropperjs/1.5.7/cropper.css" rel="stylesheet">
  <link href="./assets/vendor/toastify/1.9.0/toastify.css" rel="stylesheet">
  <!-- all vendors js file-->
  <!-- jQuery -->
  <script src="./assets/vendor/jquery/3.5.1/jquery.min.js"></script>
  <!-- bootstrap bundle -->
  <script src="./assets/vendor/bootstrap/4.5.0/bootstrap.bundle.min.js"></script>
  <script src="./assets/vendor/toastify/1.9.0/toastify.js"></script>
  <!-- cropperjs  -->
  <script src="./assets/vendor/cropper/cropperjs/1.5.7/cropper.min.js"></script>
  <!-- jquery-cropper [wrapper for cropperjs to use with jquery] -->
  <script src="./assets/vendor/cropper/jquery-cropper/1.0.1/jquery-cropper.min.js"></script>
  <style>
    .label {
      cursor: pointer;
    }

    .progress {
      display: none;
      margin-bottom: 1rem;
    }

    .alert {
      display: none;
    }

    .img-container img {
      max-width: 100%;
    }
    .image_wrapper{
	    width:300px;
	    height:345px;
    }
    .image_wrapper img{
	    width:100%;
    }
  </style>
</head>
<body>
  <div class="container" style="overflow:hidden;" >
    
  
    <div class="py-4 " >
    <div class="row" >
    <div class="col-md-6 mx-auto py-md-5" >
	    <h3 class="py-2 bg-secondary text-light text-center rounded" >Crop & upload.</h3>
	    <div class="d-flex flex-column align-items-center" >
	    <!-- <div class="collapse show mt-4 " id="preview">
		    <div class="card card-body">
			    <h3>Image preview</h3>
			    <div class="image_wrapper border" >
					 <label class="label" data-toggle="tooltip" title="Change image">
						 <img id="avatar" src="./assets/img/select-an-image.jpg" alt="crop image" >
					 <input type="file" class="sr-only" id="input" name="image" accept="image/*">
					 </label>
				 </div>
		    </div>
	    </div>-->
	    
	    <div class="collapse" id="cropSec">
		    <div class="card card-body">
			    <h3>Crop</h3>
			    <div class="image_wrapper border" >
				    <img id="image" src="./assets/img/select-an-image.jpg" alt="crop image" >
			    </div>
			    <div class="d-flex pt-4" >
				    <button type="button" id="cropCancelBtn" class="btn btn-secondary mx-5" >Cancel</button>
				    <button type="button" class="btn btn-primary" id="crop">Crop</button>
			    </div>
		    </div>
	    </div>
		<div class="collapse show mt-4 " id="preview">
		    <div class="card card-body">
			    <h3>User Image</h3>
			    <div class="image_wrapper border" >
					 <label class="label" data-toggle="tooltip" title="Change image">
						 <img id="avatar" src="<?php echo $img_dir; ?>" alt="crop image" >
					 <input type="file" class="sr-only" id="input" name="image" accept="image/*">
					 </label>
				 </div>
		    </div>
	    </div>
	    </div>
    </div>
    </div>
    </div>
    
    <div class="progress">
    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
    </div>
    
    <div class="alert" role="alert"></div>
    
  </div>


<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Fill the details</h5>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <input type="text" class="form-control" id="username" placeholder="Please enter username"  data-toggle="tooltip" title="Please enter username/your name without space.">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button id="proceed" type="button" class="btn btn-primary">Proceed</button>
      </div>
    </div>
  </div>
</div>

  <script>
    "use strict";
    window.addEventListener('DOMContentLoaded', function () {
      var avatar = document.getElementById('avatar');
      var input = document.getElementById('input');
      var $image = $("#image");
      var $progress = $('.progress');
      var $progressBar = $('.progress-bar');
      var $alert = $('.alert');
      var $preview = $("#preview");
      var $cropSec = $("#cropSec");
      var $cropCancelBtn = $("#cropCancelBtn");
      var $cropper = false;
      var mimeType;

      $('[data-toggle="tooltip"]').tooltip();

      input.addEventListener('change', function (e) {
        var files = e.target.files;
        var done = function (url) {
          input.value = '';
          $image.attr("src", url);
          $alert.hide();
          $cropSec.collapse('show')
        };
        var reader;
        var file;
        var url;

        if (files && files.length > 0) {
          file = files[0];
          getMimeType(file);


          if (URL) {
            URL.revokeObjectURL(file);
            done(URL.createObjectURL(file));
          } else if (FileReader) {
            reader = new FileReader();
            reader.onload = function (e) {
              done(reader.result);
            };
            reader.readAsDataURL(file);
          }
        }
      });

      $cropSec.on('show.bs.collapse', function () {
        $image.cropper( {
          aspectRatio: 20/23,
          dragMode: 'move',
          autoCropArea: 1,
          restore: !1,
          modal: !1,
          highlight: !1,
          cropBoxMovable: !1,
          cropBoxResizable: !1,
          toggleDragModeOnDblclick: !1,
          viewMode: 3
        });
        $cropper = true;
      }).on('hidden.bs.collapse', function () {
        $image.cropper('destroy');
        $cropper = false;
      });

      document.getElementById('crop').addEventListener('click', function () {
        console.time('crop time');
        var initialAvatarURL;
        var canvas;

        $cropSec.collapse("hide")
      
        if ($cropper) {
          canvas = $image.cropper("getCroppedCanvas",{
            width: 600,
            height: 690,
          });
          initialAvatarURL = avatar.src;
          avatar.src = canvas.toDataURL();
          $progress.show();
          $alert.removeClass('alert-success alert-warning');
          canvas.toBlob(function (blob) {
            var formData = new FormData();

            formData.append('cropped_image', blob, 'avatar.'+mimeType.slice(6));
            formData.append('fileExt', mimeType.slice(6));
            formData.append('action', 'cropImage');
            formData.append('method', 'blob');
            
            $.ajax('upload_with_session.php', {
              method: 'POST',
              data: formData,
              processData: false,
              contentType: false,
              dataType:'json',

              xhr: function () {
                var xhr = new XMLHttpRequest();

                xhr.upload.onprogress = function (e) {
                  var percent = '0';
                  var percentage = '0%';

                  if (e.lengthComputable) {
                    percent = Math.round((e.loaded / e.total) * 100);
                    percentage = percent + '%';
                    $progressBar.width(percentage).attr('aria-valuenow', percent).text(percentage);
                  }
                };

                return xhr;
              },

              success: function (res) {
                if(res.success){
	                toastMsg("Image saved successfully.");
	                //$alert.show().addClass('alert-success').text(JSON.stringify(res));
	                console.timeEnd('crop time');
	                console.log(res)
                }else{
	                toastMsg("Something goes wrong..");
	                //$alert.show().addClass('alert-danger').text(JSON.stringify(res.errors));
	                console.log(res)
	                console.log(res.errors)
                }
              },

              error: function () {
                toastMsg("Error(s)..");
                avatar.src = initialAvatarURL;
                //$alert.show().addClass('alert-warning').text('Upload error');
              },

              complete: function () {
                $progress.hide();
              },
            });
          },mimeType);
        }
      });
		 // check mime type     
		function getMimeType(file){
			var fileReaderForArrayBuffer = new FileReader();
			fileReaderForArrayBuffer.onloadend = function(evt) {
			    if (evt.target.readyState === FileReader.DONE) {
			        var uInt8Array = new Uint8Array(evt.target.result);
			        let bytes = [];
			        
			        uInt8Array.forEach((byte) => {
			            bytes.push(byte.toString(16))
			        });
			        
			        var hex = bytes.join('').toUpperCase();
			        mimeType = checkMimeType(hex);
			        console.log(mimeType)
			    }
			};
			var BLOB = file.slice(0, 4);
			fileReaderForArrayBuffer.readAsArrayBuffer(BLOB)
		}
		var checkMimeType = (signature) => {
		    switch (signature) {
		        case '89504E47':return 'image/png';
		        case '47494638':return 'image/gif';
		        case '25504446':return 'application/pdf';
		        case 'FFD8FFDB':
		        case 'FFD8FFE0':
		        case 'FFD8FFE1':return 'image/jpeg';
		        case '504B0304':return 'application/zip';
		        default:return 'Unknown filetype'
		    }
		};
		
		$cropSec.on("show.bs.collapse",()=>{
			$preview.collapse('hide');
			$('[data-toggle="tooltip"]').tooltip('hide');
		})
		$cropSec.on("hide.bs.collapse",()=>{
			$preview.collapse('show');
		})
		$cropCancelBtn.on("click",()=>{
			$cropSec.collapse('hide')
		})
		
		function toastMsg(msg,bg="#000"){
			Toastify({
				text: msg, 
				//gravity: "bottom", 
				backgroundColor: bg,
				position: "center",
				duration: 4000
			}).showToast();
			
		}
		
		
		
		
		
		// check login
		var user;
		user = "<?php echo $_SESSION['user'] ?>";
		if(user === ""){
			$("#loginModal").modal({backdrop: false,keyboard: false,});
		}
		
		$("#proceed").click((e)=>{
			e.preventDefault();
			
			var username = $("#username").val();
			if(username === ""){
				alert("Username should not be empty.")
			}else{
				alert(username);
				$.ajax('upload2.php', {
					method: 'POST',
					data: {
						action : "setSessionUser",
						user : username
					},
					dataType:'json',
					success : (res)=>{
						if (res.success){
							$("#loginModal").modal("hide");
							toastMsg(res.msg);
						}
						
					},
					error : (err)=>{
						alert(err)
					}
				})
				
			}
		})
    });
  </script>
</body>
</html>