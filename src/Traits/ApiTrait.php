<?php
namespace App\Traits;	

use Cake\ORM\TableRegistry;

trait ApiTrait
{

	// Авторизация Api пользователя
	private function apiAuth()
	{
        parse_str($this->request->getData('auth'), $auth_request);
        parse_str($this->request->getData('data'), $data);

       // if(!$this->request->getData('data')) $this->__outputJSON(['error' => 'Нет данных для записи']);

        // Находим пользователя и сравниваем хэш
        $user = TableRegistry::get('ApiUsers')->find()->where(['api_key_public' => $auth_request['api_key_public']])->first();
        if($user){
	        $token = md5(json_encode($data, JSON_UNESCAPED_UNICODE).$user['api_key_private']);
	        if($token == $auth_request['token']) return true;
	        else $this->__outputJSON(['error' => 'Ключ API не совпадает или не установлен']);
        } else {
	        $this->__outputJSON(['error' => 'Пользователь не найден']);
        }

/*
		if ( $data = json_decode(file_get_contents('php://input')) ){}
*/

	}

    function get($className = null, $options = [ 'response_format' => 'json' ])
    {
	    $this->render(false);
	    if(!$className) return null;
	   
	    $modelName = $this->name;
	   
	    if($model = $this->$modelName->exists(['className' => $className ])) {
		    
		    $this->__outputJSON( $this->$modelName->find()->where(['className' => $className])->first()->toArray() );
		    
	    } else {
		    return null;
	    }
    }


    public function list($last_id = 0)
    {
	    $this->apiAuth();
	    	
	    $params_default = [
		    'limit' => 5,
		    'between' => 0
	    ];
	        
	    $modelName = $this->name;
	    $this->__outputJSON( $this->$modelName->find()->where(['id >' => $last_id])->limit(50)->order(['lft' => 'DESC'])->toArray() );

	    $this->render(false);
    }    
    
}
?>