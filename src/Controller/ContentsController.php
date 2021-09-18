<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use App\Traits\ApiTrait;

class ContentsController extends AppController
{
use ApiTrait;

    public function initialize()
    {
        parent::initialize();

    }


    public function view($alias, $content_id = null)
    {

     //   $categories = TableRegistry::get('Files');
     //   $categories->recover();

	    if($this->Contents->exists(['id' => $content_id, 'flag' => 'on'])) {
		    
            $records = $this->Contents->get($content_id, [ 'contain' => [
                'Images' => ['sort' => ['Images.lft' => 'DESC']]
            ]])->toArray();
            //$this->Contents->recover();
            $this->set('data', $records);
        } else {
	        $this->viewBuilder()->setLayout('Error/404');
        }
         
    }



}

