<?php
namespace App\Component\Uploaded;

use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

class UploadedComponent
{
    public $data = [];
    public $srcPath;
    public $targetPath;
    public $url;
    private $clientFilename;
    private $clientMediaType;
    private $clientUploadOptions = [
	    'overwrite' => false
    ];


    /**
	 * Загрузка и обработка файла не сервере
	 * 
	 * @param array $options  
	 *  $options['url'] - url для загрузки файла со внешнего ресурса
	 *  $options['targetPath'] - абсолютный путь на сервере включая название файла, куда нужно залить файл
	 *  $options['response_format'] - формат ответа [ json or array ]
	 *  $options['request_data'] - данные о файле, передаются через POST, например описание id модели и прочее
	 *  $options['request_data'] - данные о файле, передаются через POST, например описание id модели и прочее
     *
     * @param array  $file - $_FILE 
     *
     * @return object $this
     */
    public function upload($options, $file = null)
    {
	    if(isset($options['targetPath'])) $this->targetPath = $options['targetPath'];
	    if(isset($options['url'])) $this->url = $options['url'];
	    $this->clientUploadOptions = array_merge($this->clientUploadOptions, $options);

	    /* если это $_FILES данные */
	    if($file) {
		    $this->data = $this->__setFiles($file, $options);
	    /* если файл лежит на сервере */
		} elseif(isset($options['srcPath'])) {
		    $this->data = $this->__setLocalData($options);	
		} 
		/* если это внешний ресурс */
		else {
			$this->data = $this->__setUrlData();
		}
		
		return $this;
    }

    /* Load data from $_FILES */
    public function __setFiles($file, $options)
    {   
	    // Name transliteration
		$this->clientFilename = $this->__translit(pathinfo($this->targetPath, PATHINFO_BASENAME), $file['tmp_name']);
		// IF in folder isset file with such name
		if(!$this->clientUploadOptions['overwrite']) $this->clientFilename = $this->__renameFile($this->clientFilename, $options);
		
		$this->targetPath = str_replace(pathinfo($this->targetPath, PATHINFO_BASENAME), $this->clientFilename, $this->targetPath);
	
	    $file = new \App\Component\Uploaded\UploadedFile($file['tmp_name'], $file['size'], $file['error'], $this->clientFilename, $file['type']);	    
	
	    $this->__checkDirectory(pathinfo($this->targetPath, PATHINFO_DIRNAME));
	    $file->moveTo($this->targetPath);		    

		return $file;
    }


    /* Load data By URL */   
    public function __setUrlData()
    {
        $error = 0;
        
	    /* определяем папку для заливки */
	    $dst_path = pathinfo($this->targetPath, PATHINFO_DIRNAME);
        $this->__checkDirectory($dst_path);
	    /* создаем временный файл */
        $tmpFile = tempnam($dst_path, 'file_');

        if ( file_exists($tmpFile) ) {
            
            $handle = fopen($tmpFile, "w");
            fwrite($handle, file_get_contents($this->url));
            fclose($handle);
		    
		    $this->clientFilename = $this->__translit(pathinfo($this->targetPath, PATHINFO_BASENAME), $tmpFile);
		    if(!$this->clientUploadOptions['overwrite']) $this->clientFilename = $this->__renameFile($this->clientFilename);
		    $this->targetPath = str_replace(pathinfo($this->targetPath, PATHINFO_BASENAME), $this->clientFilename, $this->targetPath);
		    
            
            // здесь мы чего-нибудь делаем        
            unlink($tmpFile);
			    
    	    $src_img = @file_get_contents($this->url);
    	    if(empty($src_img)) $error = 4;
			
            $new_file = fopen( $this->targetPath, "w" );
            fwrite( $new_file, $src_img );
            fclose($new_file); 
            
            $file = new UploadedFile($this->targetPath, filesize($this->targetPath), $error, $this->clientFilename, mime_content_type($this->targetPath));
		    
            return $file;                			    		    
        
        } else {
	        //throw new Exception('Файл не был создан.');
	        return null;
        }
    }

    /* Load data By URL */   
    public function __setLocalData( $options )
    {
	    $error = 0;

	    /* определяем папку для заливки */	    
	    $dst_path = pathinfo($this->targetPath, PATHINFO_DIRNAME);
        $this->__checkDirectory($dst_path);
 
	    /* создаем временный файл */
        $tmpFile = tempnam($dst_path, 'file_');                
                              
        $handle = fopen($tmpFile, "w");
        $src_img = @file_get_contents($options['srcPath']);
        if(empty($src_img)) $error = 4;
        
        fwrite($handle, $src_img);
        fclose($handle);
		
		$this->clientFilename = $this->__translit(pathinfo($this->targetPath, PATHINFO_BASENAME), $tmpFile);
		if(!$this->clientUploadOptions['overwrite']) $this->clientFilename = $this->__renameFile($this->clientFilename);
		$this->targetPath = str_replace(pathinfo($this->targetPath, PATHINFO_BASENAME), $this->clientFilename, $this->targetPath);		
     
        unlink($tmpFile);
		
        $new_file = fopen( $this->targetPath, "w" );
        fwrite( $new_file, $src_img );
        fclose($new_file); 
                
        $file = new UploadedFile($this->targetPath, filesize($this->targetPath), $error, $this->clientFilename, mime_content_type($this->targetPath));
		
        return $file;

    }


    public function getFiles()
    {
	    return $files;
    }

	public function __getImagetype($file)
	{
		if (exif_imagetype($file) == IMAGETYPE_JPEG) {
			return 'image/jpeg';
		} elseif (exif_imagetype($file) == IMAGETYPE_PNG) {
			return 'image/png';
		} elseif (exif_imagetype($file) == IMAGETYPE_GIF) {
		    return 'image/gif';
		}
		return false;		
	}

    /**
	 * Проверяем папку на существование, если нет, рекурсивно создаем
	 *
	 * @param string $srcPath Path to which to move the uploaded file.
	 * @return void
	*/    
	public function __checkDirectory($srcPath = null)
	{
		if (!file_exists($srcPath)) { mkdir($srcPath, 0755, true); }
		elseif (!is_writable($srcPath)) { chmod($srcPath, 0755); }
	}

	/** Транслит
	 *
	 *
	 */	
	function __translit($basename, $tmp_name = null)
	{
		$converter = array(
	        'а' => 'a',   'б' => 'b',   'в' => 'v',
	        'г' => 'g',   'д' => 'd',   'е' => 'e',
	        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
	        'и' => 'i',   'й' => 'y',   'к' => 'k',
	        'л' => 'l',   'м' => 'm',   'н' => 'n',
	        'о' => 'o',   'п' => 'p',   'р' => 'r',
	        'с' => 's',   'т' => 't',   'у' => 'u',
	        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
	        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
	        'ь' => '-',  'ы' => 'y',   'ъ' => '-',
	        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

	        'А' => 'A',   'Б' => 'B',   'В' => 'V',
	        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
	        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
	        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
	        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
	        'О' => 'O',   'П' => 'P',   'Р' => 'R',
	        'С' => 'S',   'Т' => 'T',   'У' => 'U',
	        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
	        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
	        'Ь' => '-',  'Ы' => 'Y',   'Ъ' => '-',
	        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
			' '=>'-','!'=>'',')'=>'','('=>'','+'=>'','\''=>'',','=>'','«'=>'','»'=>'','.'=>'-'
	    );

        // получаем имя и расширение, не используется pathinfo из за некорректной работы с кириллицей (зависимость от локали)
        preg_match('/(\..[^\.]*)$/', $basename, $ext);      

        //если у файла нет расширения, проверяем на картинку
        if(!isset($ext[0]) || !$this->checkMimeType($ext[0])) $ext[0] = $this->getMime($tmp_name);

        $filename = mb_ereg_replace('(\..[^\.]*)$', '', $basename, 'im');

	    $filename = strtr($filename, $converter);
	    $filename = mb_ereg_replace('(-)+', '-', $filename, 'm');
	    return trim($filename, '-').$ext[0];
	}

	/** 
	 * добавляем цифры к названию файла, если параметр перезаписи FALSE
	 *
	 */	
    public function __renameFile( $fileName = null, $options = [] )
    {
	   // if(!$fileName) $fileName = $this->clientFilename;

        preg_match('/(\..[^\.]*)$/', $fileName, $ext);

        $ext_alias = '';
        if(isset($ext[0])) $ext_alias = $ext[0];
      
        $fileName = str_replace($ext_alias, '', $fileName);

        $path = pathinfo($this->targetPath, PATHINFO_DIRNAME).'/';

	    for($i = 1; ; $i++) {
	        $reName = $fileName.'-'.$i.$ext_alias;
	        
	        $reNameConvert = '';
	        
	        if(isset($options['file_info']['request_data']['convertType'])) { 
		        $reNameConvert = $fileName.'-'.$i. '.' .explode('/', $options['file_info']['request_data']['convertType'])[1];
		    }    
	        else { 
		        $reNameConvert = $reName;
		    }    

		    if(!file_exists($path.$reName) && !file_exists($path.$reNameConvert)) { 			    			    
			    $this->clientFilename = $reName;
			    return $reName; 			    
		    }
	    }
    }
    
    private function getMime($fileName)
    {
	    $mime_type = mime_content_type($fileName);

        switch($mime_type)
        {
            case 'image/gif':
                return '.gif'; break;
            case 'image/png':
                return '.png'; break;
            case 'image/jpeg':
                return '.jpg'; break;
            default:
                return false;
                break;
        }	    
	    
    }

    function checkMimeType($ext) {

        $mime_types = [

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            //'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'dot' => 'application/msword',
            'rtf' => 'application/rtf',
            'docx' =>     'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'dotx' =>     'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'docm' =>     'application/vnd.ms-word.document.macroEnabled.12',
            'dotm' =>     'application/vnd.ms-word.template.macroEnabled.12',            

            'xls' =>      'application/vnd.ms-excel',
            'xlt' =>      'application/vnd.ms-excel',
            'xla' =>      'application/vnd.ms-excel',
            
            'xlsx' =>     'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xltx' =>     'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'xlsm' =>     'application/vnd.ms-excel.sheet.macroEnabled.12',
            'xltm' =>     'application/vnd.ms-excel.template.macroEnabled.12',
            'xlam' =>     'application/vnd.ms-excel.addin.macroEnabled.12',
            'xlsb' =>     'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            
            'ppt' =>      'application/vnd.ms-powerpoint',
            'pot' =>      'application/vnd.ms-powerpoint',
            'pps' =>      'application/vnd.ms-powerpoint',
            'ppa' =>      'application/vnd.ms-powerpoint',

            'pptx' =>     'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'potx' =>     'application/vnd.openxmlformats-officedocument.presentationml.template',
            'ppsx' =>     'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'ppam' =>      'application/vnd.ms-powerpoint.addin.macroEnabled.12',
            'pptm' =>     'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
            'potm' =>     'application/vnd.ms-powerpoint.template.macroEnabled.12',
            'ppsm' =>     'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
            
            'mdb' =>      'application/vnd.ms-access',

            // open office
            'odt' =>      'application/vnd.oasis.opendocument.text',
            'ods' =>      'application/vnd.oasis.opendocument.spreadsheet'

        ];
        
        $ext = str_replace('.', '', $ext);
        if(array_key_exists(strtolower($ext), $mime_types)) {
            return true;
        } else {
            throw new InvalidArgumentException(
                'Файлы с расширением '.$ext.' не разрешены для загрузки'
            );	        
        }

    }



}
