<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;


class FilesHelper extends Helper
{

    public $error, $files, $request_data, $file_info;
    private $options = [

	    # данные о входящем файле
	    'file_client_basename' => null,
        'file_client_ext' => null,
        'file_client_mime' => null,
        'file_client_size' => null,
        'file_client_title' => null,
        'file_client_error' => null, # контейнер для ошибок xhr

        # параметры файла на сервере
	    'file_server_basename' => null,
	    'file_server_filename' => null,
        'file_server_path' => null,
        'file_server_path_local' => null,
        'file_server_error' => [],
        'file_server_response' => false, # возврат данных после загрузки и сохранения в базе
        'file_overwrite' => false, # FALSE - не перезаписывать, модифицировать имя, TRUE - перезаписать существующий файл

        # дополнительные параметры, если загрузили изоражение
        'file_image_type' => false,
        'file_size' => [ 0, 0 ],
        'file_resize' => [ 0, 0 ], # if 0 not change size
        'file_offset' => [ 0, 0, 0, 0 ], # Значения смещения по часовой стрелке top, right, bottom, left
        'save_img_original' => true,
        'file_jpg_quality' => 100,
        'file_png_quality' => 10,
        'file_img_fit' => 'center', # тип резайза, по умолчанию по центру
        
        # ini установки для валидации размера файла
        'post_max_size_byte' => 0, #максимально допустимый размер данных, отправляемых методом POST  
        'upload_max_filesize_byte' => 0, # Максимальный размер закачиваемого файла.
        'max_file_uploads' => 0 #Максимально разрешенное количество одновременно закачиваемых файлов

    ];    
    private $custom_options;
    private $title;


    
    public function index()
    {
	    
    }


    public function show( $path, $size = 'medium' )
    {
	    switch($size) {
		    case('small') : $size_px = [ 380, 214 ]; break;
		    case('medium') : $size_px = [ 1020, 574 ]; break;
	    }

        $absolute_path = $_SERVER['DOCUMENT_ROOT'].preg_replace('%(https?://)?'.$_SERVER['SERVER_NAME'].'%i', '/webroot', $path);
        
	    extract(pathinfo($absolute_path), EXTR_PREFIX_SAME, "wddx");
	    $new_path = $dirname.'/'.$size_px[0].'x'.$size_px[1].'/'.$basename;
	    if(!is_file($new_path) || !is_readable($new_path)) {
		    $this->resizeImage( $absolute_path, $size_px, $size.'_', 'imagejpeg' );
	    }

	    extract(pathinfo($path));
	    $new_path = $dirname.'/'.$size_px[0].'x'.$size_px[1].'/'.$size.'_'.$basename;
	    return $new_path;    
    }
    

    # Ручное переопределение параметров загрузки и обработки изображений
    public function setUploadOption( $key, $value = null )
    {
	    $this->options[$key] = $value;
    }



    public function getTableSchema($table_name)
    {
        $mysqli = new mysqli("localhost", "maksimovd4", "otr909090", "maksimovd4");                
        $result = $mysqli->query("select * from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='$table_name'");
        
        $row = $result->fetch_assoc();
        
        $rows = [];
        while($row = $result->fetch_assoc()) {
            $rows[$row['COLUMN_NAME']][] = $row;
        }
        return $rows;
    }


/* -------------- Функции загрузки --------------  */    
    

    /**
     * Разбор $_POST и $_FORM данных
     *
     * @param  array $custom_options merge with default $options
     */

    public function post_upload( $custom_options = [ 'file_resize' => [ 1020, 574 ] ] )
    {
	    
	    # если передаем параметры из другого класса    
	    if($custom_options) {
	        $this->options = array_merge( $this->options, $custom_options );	        
	    }
	    # если параметры приходят через POST (JSON)
	    if( isset($_POST['options']) ) {
	        $this->options = array_merge( $this->options, $_POST['options'] );	        
	    }	    
	    
	    $this->request_data = $_POST;


	    foreach($_FILES as $files) {
		    
		    if(!empty($files)) {	
	                
                $this->files = $files;            
		        
                # если в поле  разрешен множественный выбор multiple и параметр name с автоключем []
                if(!isset($this->files['name'])) {
		        
	                foreach($this->files['name'] as $key => $file_info) { 
	                	$this->setOptions($key);
	                    $this->upload_file($key);
		            } 
		                    
                } else {
	                
                    $this->setOptions();
                    $this->upload_file();
		        }
		    }
		}
		
    }	

    # Устанавливаем параметры для загрузки файла
    public function setOptions( $key = null )
    {
            $file_name = (!$key) ? $this->files['name'] : $this->files['name'][$key];

            # основной путь для загрузки + пользовательский из параметров
            $path = $_SERVER['DOCUMENT_ROOT'].mBOX_SITE_DIR.'/files/' . $this->options['file_server_path_local']. '/';
            $path = str_ireplace( '//' , '/', $path);            
 
             $this->file_info = pathinfo($path.$file_name);

            
		    $this->custom_options = [	
	            'file_client_ext' =>  strtolower($this->file_info['extension']),	     
			    'file_client_basename' => $this->file_info['basename'],	        
		        'file_client_mime' => (!$key) ? $this->files['type'] : $this->files['type'][$key],
		        'file_client_size' => (!$key) ? $this->files['size'] : $this->files['size'][$key],
		        'file_server_tmp_name' => (!$key) ? $this->files['tmp_name'] : $this->files['tmp_name'][$key],
		        'file_server_path' => $path
		    ];		    
		    $this->custom_options['file_server_basename'] = $this->setFileName();
		    $this->custom_options['file_server_filename'] = basename($this->custom_options['file_server_basename'], '.'.$this->file_info['extension']);
		   
		    $this->options = array_merge( $this->options, $this->custom_options );		    
		    
		    if(!empty($this->request_data)) {
		        $this->options['file_client_title'] = (!$key) ? $this->request_data['title'] : $this->request_data['title'][$key];			    
		    }        
    }

    /*
	 * Проверка на перезапись существующего файла 
     */
    private function setFileName()
    {
        # имя файла
        $file_name = $this->wordTranscript($this->file_info['filename']) . '.' .$this->file_info['extension'];
	    
	    #если опция перезаписи по умолчанию FALSE (не перезаписывать)
	    if(!$this->options['file_overwrite']) {
		    
            $file_full_path = $this->custom_options['file_server_path'].$file_name;
          
		    if(file_exists( $file_full_path )) {			    
                return $this->renameFile( $file_name );			    
		    }
	    }	     
	    return $file_name;   
    }


	private function upload_file( $key = null )
	{
		$this->checkUploadFile($key);
		$this->check_allowed_mime_types($this->custom_options['file_client_mime']);
		
		if(empty($this->error)) {
			$path = $this->custom_options['file_server_path'].$this->custom_options['file_server_basename'];
			# проверяем и создаем при необходимости директорию
			$this->checkDirectory($this->custom_options['file_server_path']);
			
			
            if (move_uploaded_file( $this->custom_options['file_server_tmp_name'], $path )) {
	           
			    	       
	            # Если это изображение создаем обрезанные копии | значение устанавливается при проверке mime type
	            if($this->options['file_image_type']) { 

	                # Начинаем наполнять массив для базы
			        list($this->request_data['width'], $this->request_data['height']) = getimagesize($path);
			    
	                $this->request_data['uri_medium'] = $this->resizeImage( $path, [ 1020, 574 ], 'medium_', 'imagejpeg' );
	                $this->request_data['uri_medium'] = str_replace( $_SERVER['DOCUMENT_ROOT'].mBOX_SITE_DIR, '', $this->request_data['uri_medium']);
	                
	                $this->request_data['uri_small'] = $this->resizeImage( $path, [ 380, 214 ], 'small_', 'imagejpeg');
	                $this->request_data['uri_small'] = str_replace( $_SERVER['DOCUMENT_ROOT'].mBOX_SITE_DIR, '', $this->request_data['uri_small']);
	            }
	            
	            $this->post_save_html();
	            
	            if($this->options['file_server_response']) {	                
	                return json_encode($this->custom_options);	                   
	            } 


            }
		} else {
			return json_encode($this->error); // отправляем ошибки ?
		}            
	}


    public function renameFile( $file_name )
    {
	    $file_name_expl = explode( '.', $file_name );
	    
	    for($i = 1; ; $i++){
	    
	        $newName = $file_name_expl['0'].'-'.$i.'.'.$file_name_expl['1'];

		    if(!file_exists($this->custom_options['file_server_path'].$newName)) { 
			    return $newName;
		    }  
	    }
    }


	# Проверка ошибок загрузки
	function checkUploadFile($key)
	{   
		$error = ($key) ? $this->files['error'][$key] : $this->files['error'];
		if(!empty($error))
		{
			switch($error)
			{
				case '1': $this->error='Размер принятого файла превысил максимально допустимый размер'; break;
				case '2': $this->error='Размер загружаемого файла превысил значение, указанное в HTML-форме.'; break;
				case '3': $this->error='Загружаемый файл был получен только частично.'; break;
				case '4': $this->error='Файл не был загружен.'; break;
				case '6': $this->error='Отсутствует временная папка.'; break;
				case '7': $this->error='Не удалось записать файл на диск.'; break;
				case '8': $this->error='PHP-расширение остановило загрузку файла.'; break;
				default: $this->error='Ошибка не определена';
			}

			return false;
		}
		return true;
	}


    public function check_allowed_mime_types( $mime_type )
    {
        $allowed_mimes = [
            'application/arj',
            'application/excel',
            'application/gnutar',
            'application/mspowerpoint',
            'application/msword',
            'application/octet-stream',
            'application/onenote',
            'application/pdf',
            'application/plain',
            'application/postscript',
            'application/powerpoint',
            'application/rar',
            'application/rtf',
            'application/vnd.ms-excel',
            'application/vnd.ms-excel.addin.macroEnabled.12',
            'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'application/vnd.ms-excel.sheet.macroEnabled.12',
            'application/vnd.ms-excel.template.macroEnabled.12',
            'application/vnd.ms-office',
            'application/vnd.ms-officetheme',
            'application/vnd.ms-powerpoint',
            'application/vnd.ms-powerpoint.addin.macroEnabled.12',
            'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
            'application/vnd.ms-powerpoint.slide.macroEnabled.12',
            'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
            'application/vnd.ms-powerpoint.template.macroEnabled.12',
            'application/vnd.ms-word',
            'application/vnd.ms-word.document.macroEnabled.12',
            'application/vnd.ms-word.template.macroEnabled.12',
            'application/vnd.oasis.opendocument.chart',
            'application/vnd.oasis.opendocument.database',
            'application/vnd.oasis.opendocument.formula',
            'application/vnd.oasis.opendocument.graphics',
            'application/vnd.oasis.opendocument.graphics-template',
            'application/vnd.oasis.opendocument.image',
            'application/vnd.oasis.opendocument.presentation',
            'application/vnd.oasis.opendocument.presentation-template',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.oasis.opendocument.spreadsheet-template',
            'application/vnd.oasis.opendocument.text',
            'application/vnd.oasis.opendocument.text-master',
            'application/vnd.oasis.opendocument.text-template',
            'application/vnd.oasis.opendocument.text-web',
            'application/vnd.openofficeorg.extension',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.openxmlformats-officedocument.presentationml.slide',
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'application/vnd.openxmlformats-officedocument.presentationml.template',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'application/vocaltec-media-file',
            'application/wordperfect',
            'application/x-bittorrent',
            'application/x-bzip',
            'application/x-bzip2',
            'application/x-compressed',
            'application/x-excel',
            'application/x-gzip',
            'application/x-latex',
            'application/x-midi',
            'application/xml',
            'application/x-msexcel',
            'application/x-rar',
            'application/x-rar-compressed',
            'application/x-rtf',
            'application/x-shockwave-flash',
            'application/x-sit',
            'application/x-stuffit',
            'application/x-troff-msvideo',
            'application/x-zip',
            'application/x-zip-compressed',
            'application/zip',
            'audio/*',
            'image/*',
            'multipart/x-gzip',
            'multipart/x-zip',
            'text/plain',
            'text/rtf',
            'text/richtext',
            'text/xml',
            'video/*'
        ];

	    $mime_array = explode( '/', $mime_type);
	    
	    if($mime_array[0] == 'image') {
		    $this->options['file_image_type'] = true; 
	    }
	           
        if( in_array( $mime_type , $allowed_mimes) ) {
	        return true;
        } elseif( in_array( $mime_array[0].'/*', $allowed_mimes )){
	        return true;
        } else {
	        $this->error[] = 'Файл ' .$this->custom_options['file_client_mime']. ' имеет неразрешенный для загрузки MIME тип';
        }
              
        return false;
    }

    



/* -------------- Обработка изображений --------------  */


    # Уменьшаем изображение по центру
    /*
	 * Param   
	 * @ $src_img - абсолютный путь к исходному изображению   
	 * @ $resize_wh - массив с новыми значенимями ширины и высоты нового изображения
	 * @ $dst_ext - указание формата сжатия ( png, jpg, gif ) _!!__ не реализовано
	 * @ $prefix - префикс размера к файлу
	 *
	 *   Возвращает абсолютный путь к созданному файлу
     */
    function resizeImage( $src_img = null, $resize_wh = [], $prefix = null,  $dst_ext = 'imagejpeg' )
    {
	    if(file_exists($src_img)) {
            # Опеределяем путь к оригинальному изображению, определяем размеры
	        if(!$src_img) $src_img = $this->options['file_server_path'].$this->options['file_server_basename'];
	        $this->options['file_size'] = getimagesize($src_img);
            if(!empty($resize_wh)) { $this->options['file_resize'] = $resize_wh; } 
		    
            # определяем новую папку с учетом ресайза ( path / new_width x new_height / basename )
            $dst_path = $this->getFolderPath($src_img, $prefix);
   		    
            # определяем координаты положения и размеры нового изображения
            list( $src_w, $src_h ) = $this->options['file_size'];
            list($max_w, $max_h) = $this->options['file_resize'];      
       	    
            $quality = isset($this->options['file_jpg_quality']) ? $this->options['file_jpg_quality'] : '100';
    	    
            $mime_type = (!empty($this->options['file_client_mime'])) ? $this->options['file_client_mime'] : 
            mime_content_type($src_img);
    	    
            switch($mime_type){
                case 'image/gif':
                    $src_img = imagecreatefromgif($src_img);
                    $dst_func = "imagegif";
                    break;
     	    
                case 'image/png':
                    $src_img = imagecreatefrompng($src_img);
                    $dst_func = "imagepng";
                    //$quality = 7;
                    break;
     	    
                case 'image/jpeg':
                    $src_img = imagecreatefromjpeg($src_img);
                    $dst_func = "imagejpeg";
                    //$quality = $this->options['file_jpg_quality'];
                    break;
     	    
                default:
                    return false;
                    break;
            }
            
            $dst_source = imagecreatetruecolor($max_w, $max_h);
   		    
            $width_new = $src_h * $max_w / $max_h;
            $height_new = $src_w * $max_h / $max_w;
            #если новая ширина больше фактической, то высота слишком велика
            if($width_new > $src_w){ 
                # режем по высоте
                $h_point = (($src_h - $height_new) / 2);
                # копируем изображение
                imagecopyresampled($dst_source, $src_img, 0, 0, 0, $h_point, $max_w, $max_h, $src_w, $height_new);
            }else{ 
                # режем по ширине
                $w_point = (($src_w - $width_new) / 2); 
                imagecopyresampled($dst_source, $src_img, 0, 0, $w_point, 0, $max_w, $max_h, $width_new, $height_new);
            } 
                    
            imagejpeg($dst_source, $dst_path, $quality);
     	    
            if($dst_source)imagedestroy($dst_source);
            if($src_img)imagedestroy($src_img);
     	    
            return $dst_path;
        
        }
    }

    # ini установки для валидации размера файла
    private function setIniSettings()
    {                
        #максимально допустимый размер данных, отправляемых методом POST
        $this->options['post_max_size_byte'] = intval(ini_get('post_max_size')) * 1024 * 1024;   
        # Максимальный размер закачиваемого файла.
        $this->options['upload_max_filesize_byte'] = intval(ini_get('upload_max_filesize')) * 1024 * 1024;  
        #Максимально разрешенное количество одновременно закачиваемых файлов
        $this->options['max_file_uploads'] = ini_get('max_file_uploads'); 

    }    



    # Проверяем папку на существование, если нет, рекурсивно создаем
	public function checkDirectory($dir = null) {

		$uploadDir = trim($dir);
		$finalDir = $dir;

		if (!file_exists($finalDir)) {
			mkdir($finalDir, 0755, true);

		} else if (!is_writable($finalDir)) {
			chmod($finalDir, 0755);
		}
	}



/* -------------- Общие вспомогательные функции --------------  */

    
    # Проверяем необходимость создания новой папки
    private function getFolderPath($src_image, $prefix)
    {	    
	    $path_info = pathinfo($src_image);
	    $dst_image = $path_info['dirname'].'/'.$prefix.$path_info['filename'].'.jpg';

        if($this->options['file_resize'][0] > 0 ||  $this->options['file_resize'][1] > 0) {
			# проверяем и создаем при необходимости директорию
			$this->checkDirectory($path_info['dirname'].'/'.$this->options['file_resize'][0].'x'.$this->options['file_resize'][1].'/');         
            $dst_image = $path_info['dirname'].'/'.$this->options['file_resize'][0].'x'.$this->options['file_resize'][1].'/'.$prefix.$path_info['filename'].'.jpg';          
            
        }
        return $dst_image; 	    
    }


    # Транскрипция строки в латиницу
    public function wordTranscript($word)
    {
        $trans = array("а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d","е"=>"e", 
                       "ё"=>"yo","ж"=>"j","з"=>"z","и"=>"i","й"=>"i","к"=>"k",
                       "л"=>"l", "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
                       "с"=>"s","т"=>"t", "у"=>"u","ф"=>"f","х"=>"h","ц"=>"c",
                       "ч"=>"ch", "ш"=>"sh","щ"=>"sh","ы"=>"y","э"=>"e","ю"=>"yu",
                       "я"=>"ya",
                       "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g","Д"=>"d","Е"=>"e", 
                       "Ё"=>"yo","Ж"=>"j","З"=>"z","И"=>"i","Й"=>"i","К"=>"k", 
                       "Л"=>"l","М"=>"m","Н"=>"n","О"=>"o","П"=>"p", "Р"=>"r",
                       "С"=>"s","Т"=>"t","У"=>"u","Ф"=>"f", "Х"=>"h","Ц"=>"c",
                       "Ч"=>"ch","Ш"=>"sh","Щ"=>"sh", "Ы"=>"y","Э"=>"e","Ю"=>"yu",
                       "Я"=>"ya","ь"=>"","Ь"=>"","ъ"=>"","Ъ"=>"",
                       " "=>"-","!"=>"","("=>"",")"=>"","+"=>"","\""=>"",","=>"","«"=>"","»"=>"","."=>"-"
                       );        
        
        $word = mb_strtolower(strtr($word, $trans));
        $word = str_replace('---', '-', $word);
        $word = str_replace('--', '-', $word);
        
        return $word;
    }



  // NOT USE

    public function checkFormat($file, $format)
    {
	    if(preg_match('{'.$format.'/(.*)}is', $file['type'], $type)) {
		    return $type;
	    }
    }




    

}


?>