<?php
namespace App\Controller;

use App\Controller\AppController;

class UserfilesController extends AppController
{

    public function files($path = null)
    {
	    if(!$path){ 
		    $this->render(false);
		    return null;
		}
	    
	    $this->render(false);
	    $this->viewBuilder()->setLayout(false);
	   
	    $path = str_replace('userfiles/files/', USERFILES_PATH, $this->request->getPath());

        if(file_exists($path)) { 
	       // $exp = substr(strrchr(basename($path), '.'), 1);
	      
            $type = mime_content_type($path);
            header('Content-Type:'.$type);
            header('Content-Length: ' . filesize($path));
            echo readfile($path);
            exit();
        } else {
	        exit();
        }

    }

}
