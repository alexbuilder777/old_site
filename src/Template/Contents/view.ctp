<?$this->assign('title', $data['name'])?>

<div class="content-page">
    <div class="content-page__title"><?=$data['name']?></div>
    <div class="content-text"><?=$data['text']?></div>
<?php
  if(!empty($data['images'])) :	
?>
        <div class="photoalbum">
<?php

  $images = $data['images'];
  foreach($images as $img) :
  
	 // echo $this->Html->image('/'.FTP_FOLDER.$img['path'], [ 'style' => 'width: 200px;' ]);
	 ?>
	 <div class="photoalbum__item"><?$img['lft']?>
<!--       <span class="example-image-link0" href="<?=DOMAIN.$img['uri_small']?>" data-lightbox="example-set0" data-title="<?=$data['name']?>"> -->
      <img class="gallery-base__image" src="<?=DOMAIN.$img['uri_small']?>" alt="<?=$data['name']?>" /></span>
      <div class="photoalbum__item__title"><?=$img['title']?></div>
      </div>
<?php 
  endforeach;	
?>
        </div>
<?php   endif; ?>

</div> 