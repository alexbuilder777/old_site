<?$this->assign('title', 'экологические проекты')?>

<div class="content_blocks">
	<div class="content_blocks__title">Выполняемые работы</div>
    
    <div class="content_blocks__items">

<? foreach($contents as $content): ?>
	<a href="/contents/<?=$content['alias']?>/<?=$content['id']?>" class="content_blocks__item">
		<div class="content_blocks__item__cover" style="background-image: url('<?=$content['cover']?>');"></div>
	    <div class="content_blocks__item__title"><?=$content['name']?></div>
		<div class="content_blocks__item__lead"><?=$content['text_short']?></div>
	</a>
<? endforeach; ?>
    
    </div>
    
</div>


<?=$this->cell('NewsGen');?>	  





<script>
  //$('.cover-round-blok__item__image').roundImage();	
</script>	