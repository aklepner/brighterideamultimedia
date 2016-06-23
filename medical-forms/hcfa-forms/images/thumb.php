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
	imagecopyresampled($dst_img,$src_img,0,0,0,0,$xsize,$ysize,imagesx($src_img),imagesy($src_img)); 
	Imagejpeg($dst_img);
	ImageDestroy($dst_img);
}else{
	header("HTTP/1.1 404 Not Found");
	exit;	
}
?> 
