multiple-image-directory-cropper
================================

Not all functions fully tested, so be care-full.
Let me know if you find some bugs or find better performance

What is multiple-image-directory-cropper
Multiple-image-directory-cropper convert multiple image directory’s to smaller image directory’s.
It creates a copy of the whole directory structure and create a copy of the original images in desired size. 
Example:
Original dir: |imagesdir (total 5 GB)
		|folder 1
		|folder 2
		|folder3
Smaller dir:  |imagesdir (total 3 MB)
		|folder 1
		|folder 2
		|folder3
Mass image convert is a single php class, it uses  Imagick for image manipulation.

Very usefull for very big image librarys or albums.
