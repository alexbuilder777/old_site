<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="yandex-verification" content="d4e255157745be27" />
    <title> МосЭкоПро: <?= $this->fetch('title') ?> </title>
    <link rel="icon" type="image/png" href="/img/favicon.png" />
    <link rel="apple-touch-icon" href="/img/apple-touch-favicon.png"/>


    <?= $this->Html->css('/css/jquery/jquery-ui.min') ?>
    
    <?= $this->Html->css('/fonts/iconfont/material-icons.css') ?>

    <?= $this->Html->css('/css/style.css') ?>
    <?= $this->Html->css('/css/bootstrap-grid.css') ?>
    <?= $this->Html->css('/css/_tabs.css') ?>
    <?= $this->Html->css('/css/_button') ?>
    <?= $this->Html->css('/css/_form') ?>
    <?= $this->Html->css('/css/_sidebar') ?>
    <?= $this->Html->css('/css/_table') ?>
    <?= $this->Html->css('/css/_gallery') ?>
    
    
    <?= $this->Html->script('/js/jquery/jquery-3.2.0.min.js') ?>
    <?= $this->Html->script('/js/jquery/jquery-ui.min') ?>
    <?= $this->Html->script('/js/jquery.roundimage.js') ?>
    <?= $this->Html->script('/js/jquery.cookie.js') ?>
    <?= $this->Html->script('/js/jquery.form.min.js') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
    
    <link href="https://fonts.googleapis.com/css?family=Raleway:500i|Roboto:300,400,700|Roboto+Mono" rel="stylesheet">    
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> 
    <meta name="mailru-domain" content="G4rlSWGz44we34Ac" />    
</head>
<body>

  <header>
	  
	<div class="contact_bar">
		
	</div>  
	  
	<div class="top-bar">  

      <div class="left_bar"> 
	    <i class="material-icons">menu</i> <i class="material-icons" style="padding-left: 10px;">search</i>  
      </div>

      <div class="top-bar-contact">

        <div class="top-bar-contact__item"> 	       
	      <span> 
	         <span class="top-bar-contact-info"> <i class="material-icons top-bar-contact-icon">phone</i> +7 (925) 859-60-73 </span>
	      </span>   
	    </div> 

        <div class="top-bar-contact__item"> 	      
	      <span>  
	         <span class="top-bar-contact-info"> <i class="material-icons top-bar-contact-icon">location_on</i> г. Москва, Земляной Вал, д. 25 </span> 
	      </span>
	    </div>    

 
      </div>

	  <div class="top-bar-logo">  
		  
	    <?=$this->Html->link($this->Html->image('/img/capro_pro_logo.png', ['class' => 'logo']), '/', ['escape' => false]);?> 
	   
	  </div>
	 

	<nav>
		<?=$this->cell('TopMenu');?>
    </nav>

    
    </div>



  </header> 
  
  <div class="content" style="margin-top: 60px;">
	  
	<div class="content-center__second">
      <?= $this->fetch('content') ?>		
	</div>	  

  </div>  

    
  <footer>
	<?= $this->Flash->render() ?>  
  </footer>
    
    <?= $this->Html->script('/js/nelbit-client-core.js') ?>    
</body>
</html>
