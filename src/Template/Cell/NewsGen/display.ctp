<?$this->assign('title', 'Новости - экологии Москвы')?>

<div class="news__gen-page">
<div class="blok-double-title" style="padding-left: 60px;">
	  Новости 
</div>
  
  
<div class="news-main">  
<?php foreach($data as $record) : ?>

<?php
/*
	$images = unserialize($record['images']);
	
	$cover = null;
	if($images) {
	foreach($images as $img) {
		if($img['cover']) {
			$cover = $this->Files->show('http://'.$img['domain'].'/'.$img['path_from_webroot'].$img['path'], 'small');
		}
	}
	}
*/
?>

  <div class="news-main__item">
  
  <div class="news-main__item__cover" style="background-image: url('<?=(isset($record['cover']['uri_small']) ? $record['cover']['uri_small'] : '')?>');">	 
<?php
	//if($cover) echo $this->Html->link($this->Html->image($cover, [ 'class' => 'news__small-img' ]), '/news/view/'.$record['id'].'/'.$record['alias'].'.html', [ 'class' => 'news-main__link', 'escape' => false ]);
?>	
</div>
    <div class="news-main__item-title">
      <?=$this->Html->link($record['name'], '/news/view/'.$record['id'].'/'.$record['alias'].'.html', [ 'class' => 'news-main__link' ]);?>     	    
	</div>
	
	<span class="news-main__item-date"><?=$this->Time->format($record['date'], 'd MMM YY')?> </span>      	
 
	  <span class="news-main__item-body">
	    
        <div class="news-main__item-text">
	      <?=$record['text_short']?>
	    </div>    	    
      </span>	  
	
	      
   	  	

  </div>	
<?php endforeach; ?>
</div>