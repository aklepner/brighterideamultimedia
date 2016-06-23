<?
	header("Last-Modified: ".gmdate('D, d M Y H:i:s T', getlastmod()));
	header("Expires: ".gmdate('D, d M Y H:i:s T', time()+(60*60*24*7)));
	header("Content-Type: application/x-javascript");
?>
function viewphoto(img){
		  photo = new Image();
		  photo.src=(img);
		  control(img);
		}
function control(img){
		  if((photo.width!=0)&&(photo.height!=0)){
		    newwindow=window.open(img,"","width="+(photo.width+20)+",height="+(photo.height+20));
		  }else{
		    interval=setTimeout("control('"+img+"')",0);
		  }
		}
