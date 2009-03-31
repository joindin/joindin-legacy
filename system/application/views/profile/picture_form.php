<html>
	<head>
    	<link rel="stylesheet" type="text/css" href="/inc/css/upload_form.css" />
		<script type="text/javascript" src="/inc/js/jquery.js"></script>
		<script type="text/javascript">
		
		    /**
		     * Handles response from the upload action.
		     * @param json response
		     */
			function handleUploadResponse(response)
			{
				if(response.error === undefined) {
				    $('#temp_picture').val(response.uri);
					showPicture(response.uri);
					
					// Send the picture URI to the parent window
					window.parent.setPicture(response.uri);
				}
				else {
				    showError(response.error);
				}
			}
			
			/**
			 * Shows a picture in the picture frame.
			 * @param string uri
			 * @param string height
			 */
			function showPicture(uri)
			{
			    if(uri == '') { return; }
			    
			    // Create a new Image
			    var image = new Image();
			    image.onload = function() {
			        var imageElement = $('<img>').attr({
					    'src': this.src,
					    'id': 'picture-preview',
					    'style': 'margin-top: -' + (parseInt(this.height)/2) + 'px'
				    });

				    $('#picture-frame').fadeOut('fast', function () {
					    $('#picture-frame').html(imageElement);
					    $('#picture-frame').fadeIn('slow');
				    });
				    $('#picture-overlay').fadeOut('slow');
				    // Remove the delete flag
				    window.parent.deletePicture(0);
				    $('#delete-picture').fadeIn('slow');
			    };
			    image.onerror = function() {
			        showError('Loading of image failed.');
			    }
			    // Load the uri into the Image
			    image.src = uri;
			}

            /**
             * Shows an error message
             * @param string message
             */			
			function showError(message) {
				$('#picture-overlay').hide();
				$('#uploader-message').html(String(message)).slideDown();
			}
			
            /**
             * Actions when document is done loading
             */
			$(document).ready(function() {
			    // Add some extra events to the form submit
				$('#uploader-form').submit(function() {
					$('#uploader-message').slideUp().empty();
					$('#picture-overlay').show();
					return true;
				});
				
				$('#delete-picture').click(function() {
    				// Set the delete flag
				    window.parent.deletePicture(1);
				    $('#picture-frame').fadeOut('slow', function() {
				        $('#picture-frame').html('No picture');
				        $('#picture-frame').fadeIn('slow');
				    });
				    $('#delete-picture').hide();
				});
				
				if($('#temp_picture').val() != '') {
				    $('#delete-picture').show('fast');
				}
				
				// Check if the user already had a picture
                showPicture(window.parent.getPicture());
			});
		</script>
	</head>
	<body>
        
        <div id="container">
        
            <div id="picture">
            	<div id="picture-overlay" style="display: none;"></div>
            	<div id="picture-frame">
            		No picture
            	</div>
            </div>
            
            <div id="form">
                <p>
                	<small>Allowed file types: gif, jpg, png</small><br />
        			<small>Max. size: 2MB</small><br />
        		    <small>
        		        Images will be resized to 150 pixels wide and/or 150 pixels 
        		        high with respect to the aspect ratio.
        		    </small><br />
        		</p>
                <form action="/user/profile/picture_upload" name="uploader-form" id="uploader-form" target="uploader-frame" enctype="multipart/form-data" method="post">
                	<input type="file" id="uploader-file" name="uploader-file" /><br />
                	<input type="submit" name="uploader-submit" id="uploader-submit" value="Upload" />
                	<input type="hidden" name="temp_picture" id="temp_picture" value="" />
                </form>
                <div id="uploader-message" style="display: none;"></div>
                
            </div>
            
        	<div style="clear:both;">&nbsp;</div>
        	
        	<div id="delete-container">
                <button id="delete-picture" style="display: none;">Delete picture</button>
            </div>
        </div>
        
        
        
        <iframe id="uploader-frame" name="uploader-frame" style="clear: both; display: none;"></iframe>
    </body>
</html>
