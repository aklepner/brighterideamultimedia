<?
define("THUMB_MAX_HEIGHT",75);
define("THUMB_MAX_WIDTH",75);
if(is_file("$image")){
	header("Content-type: image/jpeg");
	Header("Last-Modified: ".gmdate('D, d M Y H:i:s T', filemtime($image)));
	Header("Expires: ".gmdate('D, d M Y H:i:s T', filemtime($image)+(60*60*24*7)));
	$src_img = imagecreatefromjpeg("$image"); 
	if(imagesx($src_img) > imagesy($src_img)){
		$xsize = THUMB_MAX_HEIGHT;
		$ysize = intval((THUMB_MAX_WIDTH/imagesx($src_img))*imagesy($src_img));
	}else{
		$ysize = THUMB_MAX_WIDTH;
		$xsize = intval((THUMB_MAX_HEIGHT/imagesy($src_img))*imagesx($src_img));
	}
	$dst_img = @imagecreatetruecolor($xsize,$ysize);
	imagecopyresized($dst_img,$src_img,0,0,0,0,$xsize,$ysize,imagesx($src_img),imagesy($src_img)); 
	imagejpeg($dst_img);
	imagedestroy($dst_img);
}else{
	$error_img = imagecreate(THUMB_MAX_HEIGHT,THUMB_MAX_WIDTH);
	$background_color = imagecolorallocate($error_img,255,255,255);
	$black = ImageColorAllocate ($error_img,0,0,0);
	imagestring($error_img,3,10,10,"Error: No Image.",$black);
	header("Content-type: image/jpeg");
	imagejpeg($error_img,'',80);
	imagedestroy($error_img);
}
?> 
