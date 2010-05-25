<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" dir="ltr">

<head>
<?php 
$title = menu_pagetitle();
$title[] = 'Joind.in';
?>
	<title><?php echo implode(' - ', $title); ?></title>
</head>
<body>
	<?php echo $content?>
</body>
	
</html>