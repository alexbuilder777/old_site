<?$this->assign('title', $data['name'])?>
<link rel="stylesheet" href="/plugins/lightbox/dist/css/lightbox.min.css">



<?php
	
	$path = '';
	$cover = '';	
	
  if(!empty($data['images'])) :
  
  $gallery_html = '<div class="photoalbum">';	
?>
        
<?php
	
	$images = unserialize($data['images']);
	foreach($images as $img) :
		if($img['cover']) :
		    $path = 'http://'.$img['domain'].'/'.$img['path_from_webroot'].$img['path'];
			$cover = $this->Files->show('http://'.$img['domain'].'/'.$img['path_from_webroot'].$img['path'], 'small');

	 $gallery_html .= '<div class="photoalbum__item">
      <a class="example-image-link" href="'.$path.'" data-lightbox="example-set" data-title="'.$data['name'].'"><img class="gallery-base__image" src="'.$cover.'" alt="'.$data['name'].'" /></a>      
      </div>';

		endif;
	endforeach;
	
	$gallery_html .= '</div>';
?>
        
<?php   endif; ?>



<div class="news-view">

    <div class="news-view__main">
	
        <div class="news-view__main__date"><?=$this->Time->format($data['date'], 'd MMMM YYYY')?></div>
        <div class="news-view__main__title"> <?=$data['name']?></div>
        
        
           
        <div class="news-view__main__text">
	       
            <?php
            if($data['cover']) echo $this->Html->image($data['cover']['uri_medium'], ['class' => 'news-view__main__img-image', 'alt' => $data['name']]);	
            ?>
      
            <?=$data['text']?>
        </div>
    
    </div>
    
    <div class="news-view__extended">
	    
	    <?foreach($extended_list as $data):?>
	    
            <div class="news-view__extended__item">
	        
                <a class="news-view__extended__date"><?=$this->Time->format($data['date'], 'd MMMM YYYY')?></a>
                <div class="news-view__extended__title"> <?=$data['name']?></div>
                
                <a href="/news/view/<?=$data['id']?>/<?=$data['alias']?>.html" class="news-view__extended__img-cnt" style="background-image: url('<?=$data["cover"]["uri_small"]?>');"></a>
            
            </div>	    
	    <?endforeach;?>
	    
    </div>
 
</div> 

 
 
</div> 
 
<script src="/plugins/lightbox/dist/js/lightbox-plus-jquery.min.js"></script>