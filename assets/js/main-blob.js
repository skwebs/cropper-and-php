"use strict";
// 
var imgForCropSection = $('#img_for_crop_section');
var imgForCrop = $('#img_for_crop');
var imgPreviewSection = $("#img_preview_section");
var imageInput = document.getElementById('image-input');
var processing = $(".processing");
var processingText = $(".processing p");
var fileName = $("#file_name");
var fileSize = $("#file_size");
var fileLabel = $('.custom-file-label');
var cropBtn = $("#cropBtn");
//	var cancelBtn = $(".cancelEdit");
var labelImg = $("#label_image");
var response = $("#response");
var mimeType = '';

//	 IMAGE LOAD SECTION ================================
imageInput.addEventListener("change", (event) => {
    cancelEdit();
    
    console.time('FileOpen');
    
    var files = event.target.files;
    if (files && files.length > 0) {
        var file = files[0];
        // show file details
        fileLabel.html(file.name);
        fileName.html("File: " + file.name);
        fileSize.html("Size: " + ((file.size / 1024).toFixed(2)) + "KB;&nbsp&nbsp");
        
        showProcees('image loading for crop...');
        
        var fileReaderForDataURL = new FileReader();
        fileReaderForDataURL.onloadend = function(e) {
            imgForCrop.attr('src', e.target.result);
			// console image url data
			console.log(e.target.result)

            imgPreviewSection.fadeOut(() => {
                imgForCropSection.fadeIn();
                editImage();
                hideProcess();
            });
        };
        fileReaderForDataURL.readAsDataURL(file);
        
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
            }
            console.timeEnd('FileOpen')
        };
        var BLOB = file.slice(0, 4);
        fileReaderForArrayBuffer.readAsArrayBuffer(BLOB)
    }
}); // load image for crop ended here.

//	IMAGE MIME SECTION ==================================
var checkMimeType = (signature) => {
    switch (signature) {
        case '89504E47':
            return 'image/png';
        case '47494638':
            return 'image/gif';
        case '25504446':
            return 'application/pdf';
        case 'FFD8FFDB':
        case 'FFD8FFE0':
        case 'FFD8FFE1':
            return 'image/jpeg';
        case '504B0304':
            return 'application/zip';
        default:
            return 'Unknown filetype'
    }
};

//	CROPPER PLUGIN SECTION =========================
var cropperOptions = {
    viewMode: 3,
    dragMode: 'move',
    aspectRatio: 20 / 23,
    autoCropArea: 1,
    restore: !1,
    modal: !1,
    highlight: !1,
    cropBoxMovable: !1,
    cropBoxResizable: !1,
    toggleDragModeOnDblclick: !1,
};

function crop() {
    console.time("crop time")
    var cropCanvas = imgForCrop.cropper('getCroppedCanvas', {
        width: 600,
        height: 690,
        //fillColor: '#fff',
        imageSmoothingQuality: 'high'
    });
    
    // convert cropped image into data url
    var cropImgURL = cropCanvas.toDataURL(mimeType);
    
    // convert cropped image into blob file
    cropCanvas.toBlob((blob)=>{
	    var formData = new FormData();
	    //formData.append('field_name', blob_file, 'blob_file_name');
	    formData.append('cropped_image', blob, 'avatar.'+mimeType.slice(6));
	    formData.append('fileExt', mimeType.slice(6));
	    formData.append('dataURL_or_blob', 'blob');
	    
	    $.ajax('upload.php', {
	        method: 'POST',
	        data: formData,
	        processData: false,
	        contentType: false,
	        dataType:'json',
	        beforeSend: () => {
		       showProcees('image cropping and uploading ...');
	        },
	        success: (res) => {
	        console.log(res);
	            console.timeEnd("Image cropped.")
	            // console sever response
	            console.log('response:' + JSON.stringify(res))
	            imgForCropSection.fadeOut(() => {
	                imgPreviewSection.fadeIn();
	                hideProcess();
	                cancelEdit();
	            });
	            fileLabel.html("Choose image");
				imageInput.value = "";
				
	            if (res.success) {
	                console.timeEnd("crop time")
	                //	change image of label image
	                labelImg.attr("src", res.imgPath);
	                response.removeClass("alert-danger").addClass("alert-success d-block").html('<button type="button" class="close" data-dismiss="alert">&times;</button>' + res.msg + '<br/>' + res.imgName);
	
	                //	let imgPreview = new Image(300,345);imgPreview.src = res.imgPath;document.getElementById("response").appendChild(imgPreview);
	            } else {
	                response.removeClass("alert-success").addClass("alert-danger d-block").html('<button type="button" class="close" data-dismiss="alert">&times;</button>' + res.msg);
	            }
	        },
	        error: (res, stat) => {
	            hideProcess();
	            cancelEdit();
	            response.html(res + "<br>" + stat)
	        }
	    })
    },mimeType);
    
}

//	OTHER SUPPORTING FUNCTIONS ==================================
function editImage() {
    imgForCrop.cropper(cropperOptions);
    cropBtn.removeClass('d-none').addClass('d-block')
    //cancelBtn.removeClass('d-none').addClass('d-block')
}

function cancelEdit() {
    imgForCrop.cropper('destroy');
    cropBtn.removeClass('d-block').addClass('d-none')
    //	cancelBtn.removeClass('d-block').addClass('d-none')
    imgForCrop.attr('src', './assets/img/select-an-image.jpg');
    //	fileLabel.html("Choose image");
    //	fileSize.html("");
}

function showProcees(msg) {
    processing.removeClass('d-none').addClass('d-flex');
    processingText.html(msg);
}

function hideProcess() {
    processingText.html('');
    processing.removeClass('d-flex').addClass('d-none');
}

function cancelCrop() {
    fileSize.html("");
    fileName.html("");
    imgForCropSection.fadeOut(() => {
        imgPreviewSection.fadeIn();
        hideProcess();
        cancelEdit();
    });
    imageInput.value = "";
}