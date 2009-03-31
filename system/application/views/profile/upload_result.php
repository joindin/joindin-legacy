<html>
	<head>
		<style>
			html, body {
				margin: 0;
				padding: 0;
			}
		</style>
	</head>
	<body>
        
        <script type="text/javascript">
			window.parent.handleResponse(<?php echo json_encode($return); ?>);
        </script>
        
    </body>
</html>