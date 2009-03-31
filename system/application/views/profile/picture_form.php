<html>
	<head>
		<script type="text/javascript" src="/inc/js/jquery.js"></script>
		<script type="text/javascript">
			function handleResponse(response)
			{
				
				if(response.uri !== undefined) {

					var image = $('<img>').attr({
						'src': response.uri,
						'id': 'picture-preview',
						'style': 'margin-top: -40px'
					});

					$('#picture-frame').fadeOut('fast', function () {
						$('#picture-frame').html(image);
						$('#picture-frame').fadeIn('slow');
					});
					$('#picture-overlay').fadeOut('slow');
					
					// Send the picture URI to the parent window
					window.parent.setPicture(response.uri);
				}
				else {
					$('#uploader-message').html(String(response.error)).slideDown();
					$('#picture-overlay').hide();
				}
			}

			$(document).ready(function() {
				$('#uploader-form').submit(function() {
					$('#uploader-message').slideUp().empty();
					$('#picture-overlay').show();
					return true;
				});
			});
		</script>
		<style>
		
			html, body {
				margin: 0;
				padding: 0;
				color: #666666;
				font-size: 13px;
			}
			
			#form {
				float: left;
				margin-left: 200px;
				padding: 10px;
			}
			
			#picture {
				position: fixed;
				width: 150px;
				height: 150px;
				padding: 10px;
				background-color: #fff;
				border: 1px dotted #ccc;
			}
			
			#picture-frame {
				width: 150px;
				height: 150px;
				line-height: 150px;
				text-align: center;
			}
			
			#picture-frame img {
				position: relative;
				top: 50%;
			}
			
			#picture-overlay {
				position: absolute;
				z-index: 100;
				width: 150px;
				height: 150px;
				background-image: url('/inc/img/loading.gif');
				background-repeat: no-repeat;
				background-position: center;
			}
		</style>
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
                <form action="/user/profile/picture_upload" name="uploader-form" id="uploader-form" target="uploader-frame" enctype="multipart/form-data" method="post">
                	<input type="file" id="uploader-file" name="uploader-file" /><br />
                	<input type="submit" name="uploader-submit" id="uploader-submit" value="Upload" />
                </form>
                <div id="uploader-message" style="display: none;"></div>
                <p>
                	<small>Allowed file types: gif, jpg, png</small><br />
        			<small>Max. size: 250KB</small><br />
        		    <small>Images will be resized to 150 pixels wide and/or 150 pixels high.</small><br />
        		</p>
            </div>
        	<div style="clear:both;">&nbsp;</div>
        </div>
        <iframe id="uploader-frame" name="uploader-frame" style="clear: both; display: none;"></iframe>
    </body>
</html>