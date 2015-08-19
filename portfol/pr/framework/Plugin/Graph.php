<?php

/**
* Работа с графикой
*/
class Plugin_Graph {

    /**
    * Изменение размеров изображения
    * 
    * @param resource $img
    * @param int $max_width
    * @param int $max_height
    * @param int $transparent_color_index
    * 
    * @return resource
    */
    public static function resizeImage($img, $max_width, $max_height, $transparent_color_index = null, $resize_anywhere = true) {
		$width = imagesx($img);
		$height = imagesy($img);
        if(!$resize_anywhere) {
            if ($width > $max_width || $height > $max_height) {
                // do nothing...
            } else {
                return $img;
            }
        }
		if ($width > $height) {
			$ratio = $max_width / $width;
		} else {
			$ratio = $max_height / $height;
		}
		$new_width  = $ratio * $width;
		$new_height = $ratio * $height;
		if($new_height > $max_height) {
			$ratio = $max_height / $height;
			$new_width  = $ratio * $width;
			$new_height = $ratio * $height;
		}
		$dst_im = imagecreatetruecolor($new_width, $new_height);
        imagecolortransparent($dst_im, imagecolorallocate($dst_im, 0, 0, 0));
        imagealphablending($dst_im, false);
        imagesavealpha($dst_im, true);
        //$white = imagecolorallocate($dst_im, 255, 255, 255);
        //imagefill($dst_im, 0, 0, $white);
		//if (!is_null ($transparent_color_index)) {
		//	imagecolortransparent ($dst_im, $transparent_color_index);
		//}
		imagecopyresampled($dst_im, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	return $dst_im;
    }
    
    static function create_scaled_image($file_path, $new_file_path, $options) {
        list($img_width, $img_height) = @getimagesize($file_path);
        if (!$img_width || !$img_height) {
            return false;
        }
        $scale = min(
            $options['max_width'] / $img_width,
            $options['max_height'] / $img_height
        );
        if ($scale >= 1) {
            if ($file_path !== $new_file_path) {
                return copy($file_path, $new_file_path);
            }
            return true;
        }
        $new_width = $img_width * $scale;
        $new_height = $img_height * $scale;
        $new_img = @imagecreatetruecolor($new_width, $new_height);
        switch (strtolower(substr(strrchr($new_file_path, '.'), 1))) {
            case 'jpg':
            case 'jpeg':
                $src_img = @imagecreatefromjpeg($file_path);
                $write_image = 'imagejpeg';
                $image_quality = isset($options['jpeg_quality']) ?
                    $options['jpeg_quality'] : 75;
                break;
            case 'gif':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                $src_img = @imagecreatefromgif($file_path);
                $write_image = 'imagegif';
                $image_quality = null;
                break;
            case 'png':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                @imagealphablending($new_img, false);
                @imagesavealpha($new_img, true);
                $src_img = @imagecreatefrompng($file_path);
                $write_image = 'imagepng';
                $image_quality = isset($options['png_quality']) ?
                    $options['png_quality'] : 9;
                break;
            default:
                $src_img = null;
        }
        $success = $src_img && @imagecopyresampled(
            $new_img,
            $src_img,
            0, 0, 0, 0,
            $new_width,
            $new_height,
            $img_width,
            $img_height
        ) && $write_image($new_img, $new_file_path, $image_quality);
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy($src_img);
        @imagedestroy($new_img);
        return $success;
    }
    
    /**
     * Возвращает изображение, приведенное к пропорциям квадрата
     *
     * @param binary $img_src исходное изображение
     * @return object
     */
    public static function cropImage($img_src) {
        $t =  (110/220);
        // оригинальные размеры изображения
        $ow = imagesx($img_src);
        $oh = imagesy($img_src);
        if($ow > $oh) {
            // если по ширине больше, тогда обрезаем и оставляем только центральную квадратную часть изображения
            $nw = $oh;
            $x = max(0, $ow/2 - $nw/2);
            $y = 0;
            $nimg = imagecreatetruecolor($nw, $nw * $t);
            imagecopy($nimg, $img_src, 0, 0, $x, $y, $nw, $nw * $t);
            imagedestroy($img_src);
            $img_src = $nimg;
        } elseif($ow < $oh) {
            // если изображение больше по вертикали, тогда оставляем только верхнюю квадратную часть
            $nh = $ow;
            $x = 0;
            $y = 0;
            $nimg = imagecreatetruecolor($nh * $t, $nh);
            imagecopy($nimg, $img_src, 0, 0, $x, $y, $nh * $t, $nh);
            imagedestroy($img_src);
            $img_src = $nimg;
        }
        return $img_src;
    }

	/**
     * Возвращает изображение, приведенное к указанным пропорциям
     *
     * @param binary $img_src исходное изображение
     * @return object
     */
	public static function cropImage2($img_src, $w, $h) {
        $tneed = $w / $h;
        $t = $tneed;
		// оригинальные размеры изображения
		$ow = imagesx($img_src);
		$oh = imagesy($img_src);
        $tfact = $ow / $oh;
        // если ширина оригинальной картинки больше высоты
		if($ow > $oh) {
			// если требуемое соотношение меньше соотношения сторон картинки
			if($tneed > $tfact) {
                $x = 0;
                $nw = $ow;
                $nh = $nw / $tneed;
                $y = ($oh / 2) - ($nh / 2);
            } else {
                $y = 0;
                $nh = $oh;
                $nw = $nh * $tneed;
                $x = ($ow / 2) - ($nw / 2);
            }
            $nimg = imagecreatetruecolor($nw, $nh);
			imagecopy($nimg, $img_src, 0, 0, $x, $y, $nw, $nh);
			//$img_src = $nimg;
        // если высота оригинальной картинки больше ширины
		} else { // ($ow < $oh) {
            // если требуемое соотношение меньше соотношения сторон картинки
            if($tneed > $tfact) {
                $x = 0;
                $nw = $ow;
                $nh = $nw / $tneed;
                $y = 0; // ($oh / 2) - ($nh / 2);
            } else {
                $y = 0;
                $nh = $oh;
                $nw = $nh * $tneed;
                $x = ($ow / 2) - ($nw / 2);
            }
            $nimg = imagecreatetruecolor(ceil($nw), ceil($nh));
            imagecopy($nimg, $img_src, 0, 0, $x, $y, ceil($nw), ceil($nh));
            //$img_src = $nimg;
		}
		return $nimg;
	}

}
