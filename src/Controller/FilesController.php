<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use App\Controller\BasemapsController as Basemap;

/**
 * Batches Controller
 *
 * @property \App\Model\Table\BatchesTable $Batches
 */
class FilesController extends AppController
{

    public  $data;
    public  $response; 
    public  $responseFiles;
    public  $responseFormat;
    private $uploadedFile;
    private $targetDirPath;
    private $www_root;
    private $requestFiles;
    private $url;

    public $error, $files, $file_info;
    private $options = [

    	'module_alias' => null,

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
  		'file_types' => false, #Разрешенные пользовательские MIME
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
    



    
    function getFiles( $where = [] )
    {
	    debug($this->request->getData());
	    return $this->Files->find()->where($where)->order([ 'id' => 'DESC' ]);
	    
	    $this->render(false);
    }

    function getList( $where = [] )
    {
	    parse_str($this->request->getData('data'), $data);
	   
	    if(isset($data['where'])){
	        $files = $this->Files->find()->where($data['where'])->order([ 'lft' => 'DESC' ])->toArray();
	        $this->__outputJSON(['Files' => $files]);
	    }
	    
	    $this->render(false);
    }

    public function uploadSingle()
    {
        //debug($this->request->data);
    }


/* -------------- Функции загрузки --------------  */

    /**
     * Загрузка файла на сервер.
     *
     * @param array $data => [
           'files'          => [], - files info like tmp, name, mime and etc
           'www_root'       => 'string' - path to upload folder,
           'responseFormat' => 'string' - json or array
           
        ] Information about file(s).
     *
     * @return JSON data uploaded files array.
     * @throws \RuntimeException in cases when no stream is available or can be
     *     created.
     */
    public function upload($data = null)
    { 
	    $this->data_default = [
		    'files' => $_FILES,
		    'type' => 'form',
		    'www_root' => USERFILES_PATH,
		    'relativePath' => '/upload_files/',
		    'url' => null,
		    'responseFormat' => 'JSON',
		    'convertType' => 'image/jpeg',
		    'overwrite' => false,
		    'responseFiles' => [],
		    'crop_sizes' => [
			    'medium' => '1020x574',
			    'small' => '380x214'
		    ]		    
	    ];
	    $this->data = (is_array($data)) ? array_merge($this->data_default, $data) : $this->data_default;
	    
	    // Set TargetPath and Folder
	    $this->__checkDirectory($this->data['www_root'].$this->data['relativePath']);
	    
	    if($this->data['type'] == 'form'){
		    $this->handleFormData();
	    }
       
/*
        if ($this->url) { 
	              
            if( $this->uploadedFile = $uploadsComponent->upload(['url' => $this->url, 'targetPath' => $this->data['targetPath']]) ) {
                
                if(!$this->uploadedFile->data->getError()) {
	                $this->responseFiles['uploadResponse'] = $this->__setResponseObject($this->uploadedFile);
                } else {
	                
                }
                

            }
        
        // если указан локальный путь к файлу на сервере   	        
        } elseif (isset($this->data['srcPath'])) {
	              
            if( $this->uploadedFile = $uploadsComponent->upload($this->data) ) {
                 
                if(!$this->uploadedFile->data->getError()) {
	                $this->responseFiles['uploadResponse'] = $this->__setResponseObject($this->uploadedFile);
                } else {
	                echo $this->uploadedFile->data->uploadedErrors[$this->uploadedFile->data->getError()]."\r\n";
	              
                }


                $file = $this->Files->save($this->Files->patchEntity($this->Files->newEntity($this->responseFiles['uploadResponse']), $this->data));//debug($file);
                $this->responseFiles['uploadResponse']['record_id'] = $file['id'];
                
                if ($this->responseFormat == 'JSON') {
                    $this->responseJson($this->responseFiles);
                } else {
	                return $this->responseFiles['uploadResponse'];
                }
            
            
            
            }
        } else {
	        debug('Нет данных');
        }	
*/    
    }

    public function handleFormData()
    {	    
	    $uploadsComponent = new \App\Component\Uploaded\UploadedComponent();
	    
		// If it single file 
		if(!isset($this->data['files'][0]) && $this->data['files']) {

            $this->data['targetPath'] = $this->data['www_root'].$this->data['relativePath'].$this->data['files']['name'];
            
            $this->uploadedFile = $uploadsComponent->upload($this->data, $this->data['files']);            
            if($this->uploadedFile->data->isMoved()) $this->data['responseFiles'][] = $this->__setResponseObject($this->uploadedFile, null);
            $this->__saveRecords();
               
        } else {
	        foreach ($this->data['files'] as $file) {
		        
		        $this->data['targetPath'] = $this->data['www_root'].$this->data['relativePath'].$file['name'];

                $this->uploadedFile = $uploadsComponent->upload($this->data, $file);            
                if($this->uploadedFile->data->isMoved()){ 
	                $this->data['responseFiles'][] = $this->__setResponseObject($this->uploadedFile);
	            }    

	        }
	        $this->__saveRecords();
        }	    
    }

    private function __saveRecords()
    {
	    //debug($this->data['responseFiles']);
	    foreach($this->data['responseFiles'] as $file){
		    $this->responseFiles = $this->Files->save($this->Files->newEntity($file));
        
            $this->__outputJSON(['Files' => $this->responseFiles]);
	    }
    }


    // Set response as JSON
    public function __outputJSON($data, $error = NULL) {
	    header('Content-Type: application/json; charset=UTF-8');

	    $this->render(false);
	    $this->viewBuilder()->setLayout(false);
    
	    if(!$error) { 
		    $response = $data;
            die(json_encode( $response, JSON_NUMERIC_CHECK ));
        } else { 
	        $response['error'] = $error;
            die(json_encode( $response, JSON_NUMERIC_CHECK ));	        
        }
    }
    

    public function __setResponseObject(&$fileObj = null)
    {
	    $fileData = [];
	    $targetPath = $fileObj->targetPath;

	    $fileinfo = pathinfo($targetPath);
	
	    $fileData['basename'] = $fileinfo['basename'];
	    $fileData['ext'] = $fileinfo['extension'];
	    $fileData['mime'] = $this->uploadedFile->data->getClientMediaType();

	    $fileData['size'] = $this->uploadedFile->data->getSize();
	    
        # определяем размеры исходного изображения
        list( $width, $height ) = getimagesize($targetPath);
        
        //Пересохраняем для очистки лишних метаданных	   
        // Если ширина изоражение больше 1400px, обрезаем его 
        if($width > 1400) {
	        exec('convert "'.$targetPath.'" -resize 1400 -quality 100 -auto-orient "'.$targetPath.'"', $test, $out);
	        list( $width, $height ) = getimagesize($targetPath);
	    } else {
		    exec('convert "'.$targetPath.'" -resize '.$width.' -quality 100 -auto-orient "'.$targetPath.'"', $test, $out);
	    }

	    $fileData['width'] = $width;
	    $fileData['height'] = $height;
        
        $fileData['domain'] = DOMAIN;
        $fileData['path'] = $targetPath;
	    $fileData['uri'] = str_replace(USERFILES_PATH, '/userfiles/files/', $targetPath);	    

        // если исходный формат не равен типу возвращаемого файла 
        if( stristr($fileData['mime'], 'image/') && $fileData['mime'] != $this->data['convertType'] ) {        

	        $fileData['path'] = $this->__resizeImage( $targetPath, null, null, $this->data['convertType']);

	        $fileData['uri'] = str_replace( USERFILES_PATH, '/userfiles/files/', $fileData['path']);
			
	        $fileinfo = pathinfo($fileData['path']);
		
	        $fileData['basename'] = $fileinfo['basename'];
	        $fileData['ext'] = $fileinfo['extension'];
	        $fileData['mime'] = 'image/' .$fileinfo['extension'];

	        unlink($targetPath);
	        $targetPath = $fileData['path'];

	            
        }
	    

        // Нарезаем нужные размеры
        if(isset($this->data['crop_sizes'])) 
        {
		    foreach($this->data['crop_sizes'] as $size_alias => $size)
		    {
			    $size_list = explode( 'x', $size);
	            $fileData['uri_'.$size_alias] = 
	                $this->__resizeImage( $targetPath, [ $size_list[0], $size_list[1] ], $size_alias.'__',  $this->data['convertType']);
	            $fileData['uri_'.$size_alias] = str_replace( USERFILES_PATH, '/userfiles/files/', $fileData['uri_'.$size_alias]);
		    }
	    }


	    $fileData['date_created'] = date('Y-m-d H:s:i');

	    $fileData['title'] = (isset($this->data['title'])) ? $this->data['title'] : '';
	    $fileData['dscr'] = (isset($this->data['dscr'])) ? $this->data['dscr'] : '';
	    
	    $fileData['label'] = (isset($this->data['label'])) ? $this->data['label'] : 'content';
	    $fileData['main'] = (isset($this->data['main'])) ? $this->data['main'] : 'none';
		$fileData['parent_id'] = (isset($this->data['parent_id'])) ? $this->data['parent_id'] : null;
	    
	    $fileData['user_id'] = $this->request->getSession()->read('Auth.User.id');
	    
	    $fileData['token'] = (isset($this->data['token'])) ? $this->data['token'] : '';
	    $fileData['model_alias'] = (isset($this->data['model_alias'])) ? $this->data['model_alias'] : '';
	    $fileData['record_id'] = (isset($this->data['record_id'])) ? $this->data['record_id'] : '';
	    $fileData['data_s_path'] = (isset($this->data['data_s_path']) && !empty($this->data['data_s_path'])) ? $this->data['data_s_path'] : null;

	    $fileData['flag'] = 'on';
          //debug($fileData);    
	    return $fileData;


    }

/* -------------- Обработка изображений --------------  */

    # Уменьшаем изображение по центру
    /*
	 * Param
	 * @ $src_img - абсолютный путь к исходному изображению
	 * @ $resize_wh - массив с новыми значенимями ширины и высоты нового изображения
	 * @ $convertType - указание формата сжатия ( image/png, image/jpeg, image/gif )
	 * @ $prefix - префикс размера к файлу
	 *
	 *   Возвращает абсолютный путь к созданному файлу
     */
    public function __resizeImage( $src_img = null, $resize_wh = [], $prefix = null,  $convertType = 'image/jpeg' )
    {
        # определяем mime исходного изображения
        $imageType = $this->__getImagetype($src_img); 

        # определяем новую папку с учетом ресайза ( path / new_width x new_height / basename )
        $changeMime = ($convertType != $imageType || pathinfo($src_img)['extension'] != str_replace('image/', '', $imageType)) ? $convertType : null;
        $dst_path = $this->__getFolderPath($src_img, $prefix, $resize_wh, str_replace('image/', '', $changeMime));

        # определяем координаты положения и размеры исходного изображения
        list( $src_w, $src_h ) = getimagesize($src_img);
        
        
        if($resize_wh) { 

	        # Если высота не задана
	        if($resize_wh[1] == 0) {
		        $width_ratio = $src_h / $src_w ;
		        $resize_wh[1] = round($resize_wh[0] * $width_ratio);
		    }

	        list($max_w, $max_h) = $resize_wh;
	    } else { 
		    list($max_w, $max_h) = getimagesize($src_img);
		}    

        # создаем новый ресурс для создаваемого изображения
        $dst_source = imagecreatetruecolor($max_w, $max_h);

        # создаем ресурс из исходного изображения
        switch($imageType)
        {
            case 'image/png':  
                $src_img = imagecreatefrompng($src_img); 
                # сохраняем прозрачность        
                imagealphablending( $dst_source, false );
                imagesavealpha( $dst_source, true );                
                break;
            case 'image/jpeg': $src_img = imagecreatefromjpeg($src_img); break;                
            case 'image/gif':  $src_img = imagecreatefromgif($src_img); break;                
            default:
                return false;
                break;
        }

        # если и ширина и высота оригинала меньше заданных
        if ($max_w > $src_w && $max_h > $src_h ) {
	        
            # пересоздаем новый ресурс для создаваемого изображения
            $dst_source = imagecreatetruecolor($src_w, $src_h);	        
            imagealphablending( $dst_source, false );
            imagesavealpha( $dst_source, true );
            
            # копируем изображение
            imagecopyresampled($dst_source, $src_img, 0, 0, 0, 0, $src_w, $src_h, $src_w, $src_h);
                       
        }    
        # если только ширина оригинала меньше
        elseif ($max_w > $src_w) {

            # определяем новые ширину и высоту
			$percent = $max_h/$src_h * 100;
            
            $width_new = $src_w/100 *$percent;
            $height_new = $src_h/100 *$percent;

            # пересоздаем новый ресурс для создаваемого изображения
            $dst_source = imagecreatetruecolor($width_new, $max_h);	        
            imagealphablending( $dst_source, false );
            imagesavealpha( $dst_source, true );
            
            # точка смещения по y
            //$h_point = (($src_h - $max_h) / 2);

            # делаем ширину максимально возможной, ширину ставим по оригиналу
            imagecopyresampled($dst_source, $src_img, 0, 0, 0, 0, $width_new, $height_new, $src_w, $src_h);

        }
        # если только высота оригинала меньше
        elseif ($max_h > $src_h) {
	        
            # пересоздаем новый ресурс для создаваемого изображения
            $dst_source = imagecreatetruecolor($max_w, $src_h);	        
            imagealphablending( $dst_source, false );
            imagesavealpha( $dst_source, true );

            # точка смещения по x
            $w_point = (($src_w - $max_w) / 2);

            # делаем ширину максимально возможной, ширину ставим по оригиналу
            imagecopyresampled($dst_source, $src_img, 0, 0, $w_point, 0, $max_w, $src_h, $max_w, $src_h);

        }        


        # если хватает и ширины и высоты
        else {
	       // ini_set('memory_limit', '928MB');
	        $src_scale = $src_w/$src_h;
	        $dst_scale = $max_w/$max_h;
	        
	        # если ширина оригинала больше высоты, кадрируем по x
	        if($src_scale > 1) {

                # если при ориентире на высоту нам хватает ширины
                if($src_scale > $dst_scale):
           
                    # определяем новые ширину и высоту
				    $percent = $max_h/$src_h * 100;
                    
                    $width_new = round($src_w/100 *$percent);
                    $height_new = round($src_h/100 *$percent);
 
                    # пересоздаем новый ресурс для создаваемого изображения
                    $dst_source = imagecreatetruecolor($max_w, $max_h);	        
                    imagealphablending( $dst_source, false );
                    imagesavealpha( $dst_source, true );
       			    
                    $w_point = ($width_new - $max_w) / 2;
                    imagecopyresampled($dst_source, $src_img, 0, 0, $w_point, 0, ++$width_new, ++$height_new, $src_w, $src_h);   
                                  
                # если НЕ хватает ширины
                else:

				    $percent = $max_w/$src_w * 100;
                    
                    $width_new = round($src_w/100 *$percent);
                    $height_new = round($src_h/100 *$percent);				    
				   
                    # пересоздаем новый ресурс для создаваемого изображения
                    $dst_source = imagecreatetruecolor($max_w, $max_h);	        
                    imagealphablending( $dst_source, false );
                    imagesavealpha( $dst_source, true );
       			    
                    $h_point = ($height_new - $max_h) / 2;
                    imagecopyresampled($dst_source, $src_img, 0, 0, 0, $h_point, $width_new, $height_new, $src_w, $src_h); 
                                
                endif;
                
	        
	        } else {
                # определяем новые ширину и высоту

				$percent = $max_h/$src_h * 100;
                
                $width_new = $src_w/100 *$percent;
                $height_new = $src_h/100 *$percent;	

                # пересоздаем новый ресурс для создаваемого изображения
                $dst_source = imagecreatetruecolor($width_new, $max_h);	        
                imagealphablending( $dst_source, false );
                imagesavealpha( $dst_source, true );
        
                $h_point = (($src_h - $height_new) / 2);
                imagecopyresampled($dst_source, $src_img, 0, 0, 0, 0, ++$width_new, ++$height_new, $src_w, $src_h);		        

	        }
	        
        }
        
        ini_set('max_execution_time', 70);
        # записываем ресурс в новый файл
        if($convertType == 'original') $convertType = $imageType;       
        switch(strtolower($convertType))
        {
            case 'image/gif':
                imagegif($dst_source, $dst_path, null); break;
            case 'image/png':
                imagepng($dst_source, $dst_path, 9); break;
            case 'image/jpeg':
                imagejpeg($dst_source, $dst_path, 100); break;
            case 'image/jpg':
                imagejpeg($dst_source, $dst_path, 100); break;
            default:
                return false;
                break;
        }

        # очищаем память
        if($dst_source)imagedestroy($dst_source);
        if($src_img)imagedestroy($src_img);

        return $dst_path;
   
    }


	public function __getImagetype($file)
	{
/*
		if (exif_imagetype($file) == IMAGETYPE_JPEG) {
			return 'image/jpeg';
		} elseif (exif_imagetype($file) == IMAGETYPE_PNG) {
			return 'image/png';
		} elseif (exif_imagetype($file) == IMAGETYPE_GIF) {
		    return 'image/gif';
		}	
*/
		if(in_array(mime_content_type($file), ['image/jpeg', 'image/png', 'image/gif'])) return mime_content_type($file);
		return false;		
	}

    # ini установки для валидации размера файла
    private function __setIniSettings()
    {
        #максимально допустимый размер данных, отправляемых методом POST
        $this->options['post_max_size_byte'] = intval(ini_get('post_max_size')) * 1024 * 1024;

        # Максимальный размер закачиваемого файла.
        $this->options['upload_max_filesize_byte'] = intval(ini_get('upload_max_filesize')) * 1024 * 1024;

        #Максимально разрешенное количество одновременно закачиваемых файлов
        $this->options['max_file_uploads'] = ini_get('max_file_uploads');
    }

    # Проверяем папку на существование, если нет, рекурсивно создаем
	public function __checkDirectory($dir = null)
	{
		$uploadDir = trim($dir);
		$finalDir = $dir;

		if (!file_exists($finalDir)) { mkdir($finalDir, 0755, true); }
		elseif (!is_writable($finalDir)) { chmod($finalDir, 0755); }
	}

/* -------------- Общие вспомогательные функции --------------  */

    # Проверяем необходимость создания новой папки
    private function __getFolderPath($src_image, $prefix, $resize_wh = null, $changeMime = null)
    {
	    $path_info = pathinfo($src_image);
	    
	    $file_name = $path_info['basename'];
	    if($changeMime) $file_name = $path_info['filename']. '.' .$changeMime;
	    
	    $dst_image = $path_info['dirname'].'/'.$prefix.$file_name;
	    	    
        $sub_folder = '';
        if($resize_wh) $sub_folder = $resize_wh[0].'x'.$resize_wh[1].'/';
        {
			# проверяем и создаем при необходимости директорию
			$this->__checkDirectory($path_info['dirname'].'/' .$sub_folder);
            $dst_image = $path_info['dirname'].'/'.$sub_folder.$prefix.$file_name;
        }
        return $dst_image;
    }









    # Ручное переопределение параметров загрузки и обработки изображений
    public function __setUploadOption( $key, $value = null ) { $this->options[$key] = $value; }




	# Обновление записи с model_id
	public function __updateModuleId($token=NULL, $record_id, $module_alias)
	{
		if (empty($token) or !$this->mBox->db->update([
			"COLUMN" => [ 'module_id'=> $record_id ],
    		"TABLE" => [ 'files' ],
    		"WHERE" => [ 'token'=> $token ]
    	]))
		{ return FALSE; }
		else {
			$this->__updateCover($token, $record_id, $module_alias);
		}

		return TRUE;
	}

    public function __updateCover($token, $record_id, $module_alias)
    {
		# если указан параметр одиночной обложки, проверяем есть ли такие записи в данной группе по label
        $records = $this->mBox->db->select([
			"FROM" => ["TABLE"=>"files", "AS"=>"f"],
			"WHERE" => ['module_alias' => $module_alias, 'module_id' => $record_id, 'main' => 'single'],
			"ORDER" => ["id" => 'DESC']
		]);

		#группируем по парамтеру label
		$groups = [];
		if(!empty($records)) {
		    foreach($records as $record) {
		    	$groups[$record['label']][] = $record;
		    }

		    #получаем ID которые нужно переназначить ( main => none )
		    $ids = [];
		    foreach($groups as $group) {
		    	$first = array_shift($group);
		    	foreach($group as $record) {
		    	    $ids[$record['id']] = $record['id'];
		    	}
		    }
		    if (!$this->mBox->db->update([
		    	"COLUMN" => [ 'main'=> 'none' ],
    	    	"TABLE" => [ 'files' ],
    	    	"WHERE" => [ '@id'=> $ids ]
    	    ]))
		    { return FALSE; }
		}

    }


	# Валидация данных
	function valid($data = null)
	{
        # получаем схему таблицы
        $schema = $this->__getTableSchema('mbox_files');

        if(!isset($data['id'])) { unset($schema['id']); }

        foreach($data as $col_name => $col_value) {
	        if(isset($schema[$col_name])) {
		        foreach($schema[$col_name] as $rule) {
		            $col_type = str_replace( ['enum', 'varchar', 'datetime'] ,['string','string', 'timestamp'] ,$rule['DATA_TYPE'] );
		            $nullable = ($rule['IS_NULLABLE'] == 'NO') ? true : false;
		            $this->mBox->setValid($col_name, $col_value, $col_type, 'valid_'.$col_name, $nullable); break;
		        }
	        }
        }

		# проверка ошибок
		if ($this->mBox->getMessageAjaxQ('error') ) { return FALSE; }
		if (empty($this->mBox->validColumn)) { $this->mBox->addMessageAjaxQ($this->mBox->lang->get('failed_column')); return FALSE; }
		return $this->mBox->validColumn;
	}

/*
    public function showRecordFiles($id, $model, $label = null)
    {
        $files = $this->Files->find()
            ->where([ 'Files.record_id' => $id ]);
        if($label) $files->andWhere([ 'Files.label' => $label ]); 
        
        $this->set(compact('id', 'model'));   
    }
*/


/* API.. */

    // Check Permissions and upload
    public function apiUpload()
    {
	    $this->viewBuilder()->setLayout(false);
	    if($this->request->getData('files')){	
		    
		    parse_str($this->request->getData('data'), $data);

		    if(is_string($data['data'])) $data['data'] = json_decode($data['data'], true);

			$rootRecord = $this->getRootRecord($data['data']['post_data']['record_id'], $data['data']['post_data']['className']);
			if(isset($rootRecord['id'])) {
				$data = $data['data'];
				if(isset($data['post_data'])) $data = $data['post_data'];
				$data['files'] = $this->request->getData('files');
				$data['parent_id'] = $rootRecord['id'];

				$this->upload($data);
			}
	    }        
        
        $this->render(false);
    }

    function getRootRecord($record_id, $className)
	{
		$rootRecord = $this->Files
			->find()
			->where([
				'label' => 'root',
				'record_id' => $record_id,
				'model_alias' => $className
			])
			->first();

		if(!$rootRecord) {

			$entity = $this->Files->newEntity([
				'label' => 'root',
				'record_id' => $record_id,
				'model_alias' => $className
			]);
			if($this->Files->save($entity)) return $this->getRootRecord($record_id, $className);

		} else {
			return $rootRecord;
		}

	}


    function editApi()
    {
	    if($this->request->getData()){

		    parse_str($this->request->getData('data'), $data);
		 
		    $entity = $this->Files->newEntity($data);//debug($entity);
		    $entity = $this->Files->save($entity);
		    $this->__outputJSON($entity); 
		}
		
		$this->render(false);    
    }


    public function list()
    {

        if($this->request->getData()){
	        debug($this->request->getData());
        }

    }


    /**
	 * Sortable rows in edit basemap record  
     */


/* ..API */





}


?>
