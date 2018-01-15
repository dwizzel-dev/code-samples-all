<?php
/**
 * File Utilities.
 * @author $Author: Wei Zhuo $
 * @version $Id: Files.php 26 2004-03-31 02:35:21Z Wei Zhuo $
 * @package ImageManager
 */

define('FILE_ERROR_NO_SOURCE', 100);
define('FILE_ERROR_COPY_FAILED', 101);
define('FILE_ERROR_DST_DIR_FAILED', 102);
define('FILE_COPY_OK', 103);

/**
 * File Utilities
 * @author $Author: Wei Zhuo $
 * @version $Id: Files.php 26 2004-03-31 02:35:21Z Wei Zhuo $
 * @package ImageManager
 * @subpackage files
 */
class Files 
{
	
	/**
	 * Copy a file from source to destination. If unique == true, then if
	 * the destination exists, it will be renamed by appending an increamenting 
	 * counting number.
	 * @param string $source where the file is from, full path to the files required
	 * @param string $destination_file name of the new file, just the filename
	 * @param string $destination_dir where the files, just the destination dir,
	 * e.g., /www/html/gallery/
	 * @param boolean $unique create unique destination file if true.
	 * @return string the new copied filename, else error if anything goes bad.
	 */
	static function copyFile($source, $destination_dir, $destination_file, $unique=true) 
	{
		if(!(file_exists($source) && is_file($source))) 
			return FILE_ERROR_NO_SOURCE;

		$destination_dir = Files::fixPath($destination_dir);

		if(!is_dir($destination_dir)) 
			Return FILE_ERROR_DST_DIR_FAILED;

		$filename = Files::escape($destination_file);

		if($unique) 
		{
			$dotIndex = strrpos($destination_file, '.');
			$ext = '';
			if(is_int($dotIndex)) 
			{
				$ext = substr($destination_file, $dotIndex);
				$base = substr($destination_file, 0, $dotIndex);
			}
			$counter = 0;
			while(is_file($destination_dir.$filename)) 
			{
				$counter++;
				$filename = $base.'_'.$counter.$ext;
			}
		}
		/*
		echo '$source:'.$source.'<br>';
		echo '$destination_dir:'.$destination_dir.'<br>';
		echo '$destination_file:'.$destination_file.'<br>';
		exit();		
		*/

		if (!copy($source, $destination_dir.$filename)){
			return FILE_ERROR_COPY_FAILED;
			}
		
		//verify that it copied, new file must exists
		if (is_file($destination_dir.$filename)){
			//Dwizzel 05-10-2012 08:33
			//exceptions depending on the directory PATH_IMAGE/(coupons,homepage,icons) qui sont dans ../define.php
			$arrExcept = explode(';',str_replace("\n", '', PATH_IMAGE_ACCEPT));
			$bCreateFormats = false;
			foreach($arrExcept as $k=>$v){
				if(DIR_MEDIA.trim($v) == $destination_dir){
					$bCreateFormats = true;
					break;
					}
				}
			if($bCreateFormats){
				Files::createAllFormat($destination_dir, $filename);
				}
			Return $filename;
		}else{
			return FILE_ERROR_COPY_FAILED;
			}
	}

	/**
	 * Created by:: Dwizzel 08-07-2012 13:33
	 * Create tout les format neccessair au web.
	 * creer un repertoire .zoom, .listing, .thumbnail
	 * deplacer la photo originale dans le repertoire .zoom
	 * creer un thumbnail dans le repertoire .thumbnail
	 * faire un format photo dans le repertoire original
	 */
	static function createAllFormat($origDir, $filename){
		//echo 'DIR:'.$origDir.'<br>FILE:'.$filename;
		//on creer les repertoire .zoom .thumbnail .listing
		if(!is_dir($origDir.PATH_IMAGE_ZOOM)){
			Files::createFolder($origDir.PATH_IMAGE_ZOOM);
			}
		if(!is_dir($origDir.PATH_IMAGE_RESPONSIVE)){		
			Files::createFolder($origDir.PATH_IMAGE_RESPONSIVE);
			}	
		//on copie la photo original dans .zoom car c'est la plus grosse que l'on aura
		if (!copy($origDir.$filename, $origDir.PATH_IMAGE_ZOOM.'/'.$filename)){
			return FILE_ERROR_COPY_FAILED;
		}else{
			//
			}
			
		//on fait un thumbs a partir de la photo original
		require_once('Thumbnail.php');	
		//celui du thumbnails
		$thumbnailer = new Thumbnail(RESPONSIVE_SIZE_W, RESPONSIVE_SIZE_H);
		$thumbnailer->createThumbnail($origDir.$filename, $origDir.PATH_IMAGE_RESPONSIVE.'/'.$filename);
		//on modifie la photo originale et la laisse au meme endroit
		$thumbnailer = new Thumbnail(PHOTO_SIZE_W, PHOTO_SIZE_H);
		$thumbnailer->createThumbnail($origDir.$filename, $origDir.$filename);
		
		}
	
	
	/**
	 * Create a new folder.
	 * @param string $newFolder specifiy the full path of the new folder.
	 * @return boolean true if the new folder is created, false otherwise.
	 */
	static function createFolder($newFolder) 
	{
		mkdir ($newFolder, 0777);
		return chmod($newFolder, 0777);
	}


	/**
	 * Escape the filenames, any non-word characters will be
	 * replaced by an underscore.
	 * @param string $filename the orginal filename
	 * @return string the escaped safe filename
	 */
	static function escape($filename) 
	{
		Return preg_replace('/[^\w\._]/', '_', $filename);
	}

	/**
	 * Delete a file.
	 * @param string $file file to be deleted
	 * @return boolean true if deleted, false otherwise.
	 */
	static function delFile($file) 
	{
		
		if(is_file($file)) 
			Return unlink($file);
		else
			Return false;
	}

	/**
	 * Delete folder(s), can delete recursively.
	 * @param string $folder the folder to be deleted.
	 * @param boolean $recursive if true, all files and sub-directories
	 * are delete. If false, tries to delete the folder, can throw
	 * error if the directory is not empty.
	 * @return boolean true if deleted.
	 */
	static function delFolder($folder, $recursive=false) 
	{
		$deleted = true;
		if($recursive) 
		{
			$d = dir($folder);
			while (false !== ($entry = $d->read())) 
			{
				if ($entry != '.' && $entry != '..')
				{
					$obj = Files::fixPath($folder).$entry;
					//var_dump($obj);
					if (is_file($obj))
					{
						$deleted &= Files::delFile($obj);					
					}
					else if(is_dir($obj))
					{
						$deleted &= Files::delFolder($obj, $recursive);
					}
					
				}
			}
			$d->close();

		}

		//$folder= $folder.'/thumbs';
		//var_dump($folder);
		if(is_dir($folder)) 
			$deleted &= rmdir($folder);
		else
			$deleted &= false;

		Return $deleted;
	}

	/**
	 * Append a / to the path if required.
	 * @param string $path the path
	 * @return string path with trailing /
	 */
	static function fixPath($path) 
	{
		//append a slash to the path if it doesn't exists.
		if(!(substr($path,-1) == '/'))
			$path .= '/';
		Return $path;
	}

	/**
	 * Concat two paths together. Basically $pathA+$pathB
	 * @param string $pathA path one
	 * @param string $pathB path two
	 * @return string a trailing slash combinded path.
	 */
	static function makePath($pathA, $pathB) 
	{
		$pathA = Files::fixPath($pathA);
		if(substr($pathB,0,1)=='/')
			$pathB = substr($pathB,1);
		Return Files::fixPath($pathA.$pathB);
	}

	/**
	 * Similar to makePath, but the second parameter
	 * is not only a path, it may contain say a file ending.
	 * @param string $pathA the leading path
	 * @param string $pathB the ending path with file
	 * @return string combined file path.
	 */
	static function makeFile($pathA, $pathB) 
	{		
		$pathA = Files::fixPath($pathA);
		if(substr($pathB,0,1)=='/')
			$pathB = substr($pathB,1);
		
		Return $pathA.$pathB;
	}

	
	/**
	 * Format the file size, limits to Mb.
	 * @param int $size the raw filesize
	 * @return string formated file size.
	 */
	static function formatSize($size) 
	{
		if($size < 1024) 
			return $size.' bytes';	
		else if($size >= 1024 && $size < 1024*1024) 
			return sprintf('%01.2f',$size/1024.0).' Kb';	
		else
			return sprintf('%01.2f',$size/(1024.0*1024)).' Mb';	
	}
}

?>