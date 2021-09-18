
<div class="blok-grey">
<?php
	 $i = 0;
	 foreach($data['response']['contents'] as $record) : 
	 
	 $cover = unserialize($record['cover']);
	 
	 $close_tag = '';
	 
	 if( $i%3 == 0 & $i != 0) :
	   echo '</div><div class="blok-grey">';
	 endif;
	 $i++;
 ?>
  <div class="blok-grey-item">	
    <div class="blok-grey-title">
      <?=$this->Html->link($record['name'], '/contents/'.$record['alias'].'/'.$record['id'])?>  
    </div>
 
    <div class="blok-grey-img">
      <?=$this->ImageCrop->image(['input' => '/upload'.$cover['path'], 'width' => '500', 'height' => '340'])?>	  	  

      <?php
	      /*
	      $img = $this->ImageCrop->image(['input' => $record['img'], 'width' => '500', 'height' => '340']);
	      echo $this->Html->link($img , '/contents/'.$record['alias'].'/'.$record['id']);
	      */
	      ?>	  	  
    </div>	  		
    <div class="blok-grey-text">	
      <?=$record['text_short']?>
    </div>    	       	  	

  </div>
	
<?php endforeach; ?>
</div>