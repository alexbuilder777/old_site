<?php
namespace Api\Controller;

use Cake\ORM\TableRegistry;
use Api\Controller\AppController;
use App\Traits\CurlTrait;
use App\Traits\ApiTrait;

class CategoryOptionsValuesController extends AppController
{
    use CurlTrait;
    use ApiTrait;

    public function initialize()
    {
        parent::initialize();
    }
    
    public function beforeRender(\Cake\Event\Event $event){
	    
	    $this->viewBuilder()->setLayout(false);	    
    }

    public function index(){}

    
}