<ul class="top-navigation">
<?php
	 foreach($data as $nav) : ?>
        <li class="top-navigation-item">        
          <?=$this->Html->link($nav['name'].'', $nav['uri'], ['class' => 'top-navigation-link', 'escape' => false])?>     
        </li>	
<?php endforeach; ?>
</ul>