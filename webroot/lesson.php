<?

class App
{
	public static $test = 'huilo';
	
	function outInJson($data)
	{
		return json_encode($data);
	}
}



class Cats extends App
 
{
	public $organizationName = 'funny cats';
	
	public static $color = 'red'; 
	
    function kissCats($catName)
    {
	    echo 'В организации' . $this->organizationName . 'Поцеловали кота'. $catName;
	
    }
    function touchCats($catName)
    {
	    echo 'Погладили кота'. $catName;
    }
    function kickCats($catName)
    {
	    echo 'Пнули кота'. $catName; 
    } 
    
    function showCollor()
    {
	    echo $this->color;
    }
    
    function showTest()
    
    {
	    echo self::$color;
    }
    	
}


$show = new Cats;
$show->showTest();




$catNames = ['huy' => 'Фрося', 'Фроня', 'Хуеня', 'huyonya' => ['ghjkjghj'], 'Диня', 'Люся'];


// print_r($catNames);

/* $catsObject = new Cats();

$willback = $catsObject->color;

echo $willback;

/*
foreach($catNames as $key => $catName){
	echo '<p>' . $catsObject->kissCats($catName) . '</p>';
	echo '<p>' . $catsObject->touchCats($catName) . '</p>';
	echo '<p>' . $catsObject->kickCats($catName) . '</p>';
}



/*
cake.php 

У нас интернет магазин. С сайта поступают заказы. Создать класс ордерс и что бы заказы сохранялись в базу данных. 
*/
