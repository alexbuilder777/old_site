<?php
namespace App\Controller;

use App\Controller\AppController;

class NewsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        //$this->viewBuilder()->setLayout('second');
    }

    public function index()
    {
        $records = $this->News
            ->find()
            ->order([ 'date' => 'DESC' ])
            ->where(['News.flag' => 'on'])
            ->contain(['Files', 'Cover'])
            ->toArray();
        $this->set('data', $records);
    }



    public function view($entity_id)
    {
        $data = $this->News->get($entity_id, ['contain' => ['Cover']])->toArray();
        $extended_list = $this->News->find()
            ->limit(5)
            ->where(['News.id <>' => $entity_id, 'News.flag' => 'on'])
            ->order(['date' => 'DESC'])
            ->contain(['Cover'])
            ->toArray();
        $this->set(compact('data', 'extended_list'));
    }





}

