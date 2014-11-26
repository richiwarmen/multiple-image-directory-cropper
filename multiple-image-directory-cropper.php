<?php
//  error_reporting(E_ALL);
//  ini_set("display_errors", 1);
// 	make a copy from image directorys with small images
// 	need 4 variables when constructing 
// 	imageLibraryPath = the path where the directory of images are (source)
//	imageLibraryPathSmall = the path where to create the copy of the image directory's 
//	imageMaxWidth = max width for the images;
//	imageMaxHeigh = max height of the images
//
// 	how to use:
// 	$myclass = new GalleryMaster($imageLibraryPath,$imageLibraryPathSmall,$imageMaxWidth,$imageMaxHeigh);
// 	then call $myclass->SyncDirectorys(false);
//	the arguments: for SyncDirectorys
// 	1st param bool $onlyreturnimages; if true, don't create files, only get directory's and files
//	2th param bool $forcerebuild; if true rebuild all pervious generated images
//	3th param bool $deleteimageLibraryPathSmall; remove all previous generated images.<br>
//	WARNING be sure you did set the right path: imageLibraryPathSmall!
//  then you can call $myimagearray = $myclass->getImagesAsArray()  ; print_r($myimagearray);
//
//
//example:
//	$imagearray = new GalleryMaster('imagelibrary','imagelibsmall',300,300);
//	$imagearray->SyncDirectorys(true);
//	print_r( $imagearray->getImagesAsArray());


class GalleryMaster
{
	
	private $_imageLibraryPath='';
	private $_imageLibraryPathSmall='';
	private $_imageMaxWidth=300;
	private $_imageMaxHeigh=300;
	
	private $issynced = false;
	
	private $_images = array();
	
	public function __construct($imageLibraryPath,$imageLibraryPathSmall,$imageMaxWidth=300,$imageMaxHeigh=300) 
	{
		$this->_imageLibraryPath=$imageLibraryPath;
		$this->_imageLibraryPathSmall=$imageLibraryPathSmall;
		$this->_imageMaxWidth=$imageMaxWidth;
		$this->_imageMaxHeigh=$imageMaxHeigh;

    }
	public function getImageLibraryPath()
	{
		return $this->_imageLibraryPath;;
	}	
	public function getImageLibraryPathSmall()
	{
		@!mkdir(dirname($this->_imageLibraryPathSmall), 0777, true);
		if (file_exists($this->_imageLibraryPathSmall))
		{
		return $this->_imageLibraryPathSmall;
		}
		else
		{
			echo "error create dir";
			return false;
		}
	}
	public function getMaxWidth()
	{
		return $this->_imageMaxWidth;
	}
	public function getMaxHeight()
	{
		return $this->_imageMaxHeight;
	}
	
	public function SyncDirectorys($onlyreturnimages = true, $forcerebuild = false,$deleteimageLibraryPathSmall = false) 
	{
		// check if path's are defined.
		if(strlen ($this->_imageLibraryPathSmall) < 3 && strlen ($this->_imageLibraryPath) < 3) return;
		// remove previous generated images
		if($deleteimageLibraryPathSmall == true) deleteDir($this->_imageLibraryPathSmall);
		$issynced = true;
		foreach(glob( $this->_imageLibraryPath."/*/{*.jpg,*.png,*.bmp,*.gif}", GLOB_BRACE) as $image) 
		{
		// convert original path to small path
		$smallPath = str_replace($this->_imageLibraryPath,$this->_imageLibraryPathSmall,$image);
		$imageWithPath=substr($image,strlen($this->_imageLibraryPathSmall));
		$directorys = explode('/',$imageWithPath);
	
		if( count($directorys)==2) 
		{
			if($onlyreturnimages == false)
			{
				// check if image is valid
				try {
				$im = new Imagick();
				$im->pingImage($image);;
				}
				catch(ImagickException $e) {
					// not a good image format/ wrong file
					continue;
				}
			}
			else
			{
				
				@$this->_images[($directorys[0])][].=($directorys[1]);
				//$onlyreturnimages = true so no need to go on
				continue;
			}
			
			
			// array to hold images
			@$this->_images[($directorys[0])][].=($directorys[1]);
			}
			else
			{
			// not an image
			continue;
			}
			if (count($directorys) == 1)
			for ($i = 0; $i <= count($directorys); $i++) {
			echo $directorys[$i]."<br/>";
			}
			
			if (file_exists($smallPath)) {
				if($forcerebuild == true)
				{
					$this->existorCreate($image);
				}
				else
				{
					continue;
				}
				
			} else {
			// "The file smallPath does not exist"
			$this->existorCreate($image);
			}
		}
	}

	function existorCreate($bigfile)
	{
		// convert original path to small path
		$smallPath = str_replace($this->_imageLibraryPath,$this->_imageLibraryPathSmall,$bigfile);
		// if exist, nothing to do so return
		if (file_exists($smallPath)) { 	return;	}
		// check if destination dir exist
		if (@!mkdir(dirname($smallPath), 0777, true)) {
		// generate small file
		$im = new Imagick($bigfile);
		
		$width=$im->getImageWidth();
			if ($width > $this->_imageMaxWidth) 
			{ 
				$im->thumbnailImage($this->_imageMaxWidth,null,0); 
			}
			$height=$im->getImageHeight();
			if ($height > $this->_imageMaxHeigh) 
			{ 
				$im->thumbnailImage(null,$this->_imageMaxWidth,0); 
			}
		
		$im->writeImage($smallPath);
		$im->destroy();
		// done
		}
	}
	private function deleteDir($path) {
    return is_file($path) ?
            @unlink($path) :
            array_map(__FUNCTION__, glob($path.'/*')) == @rmdir($path);
	}
	
	public function getImagesAsArray($printarraystring = false) 
	{
		if($this->issynced == false) $this->SyncDirectorys();
		if($printarraystring == true)
		{
			$printr = print_r($this->_images,true);
			return $printr;
		}
		else
		{
			
			return $this->_images;
		}
		
	}
}
?>