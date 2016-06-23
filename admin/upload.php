<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>Untitled</title>
</head>

<body>
<?
	if(isset($_POST[name])){
		if($_POST[image] != ""){
		if(!preg_match("/jpeg/",$_FILES['image']['type'])){
			print "Wrong File Type!<br>";
		}elseif($_FILES['image']['size'] > 150000){
			print "File too big!<br>";
		}else{
			print "Type: ".$_FILES['image']['size']."<br>";
			if(move_uploaded_file($_FILES['image']['tmp_name'], "1.jpg")){
				chmod("1.jpg", 0644);
				chgrp("1.jpg", "users");
				print "success!";
			}else{
				print "fail!";
			}
		}
		}else{
			print "No Image!";
		}
	}else{
?>
<form action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="name" value="test">
<input type="file" name="image"><br>
<input type="submit">
</form>
<? } ?>


</body>
</html>
