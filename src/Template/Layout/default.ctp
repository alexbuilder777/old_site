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


    <div class="header">
        
        <div class="header__logo">
	        <a href="/" class="header__logo__img"></a>
        </div>
        
        <div class="header__logo__top-navigation">
	        <?=$this->cell('TopMenu');?>
	    </div>    


    <div class="header__contact">

        <div class="header__contact__item__phone"><img width="15px"; src="/svg/smartphone-call.svg">
	        <span  class="header__contact__item__phone__phone"><span>+7 (925) - 859 - 60 - 73</span> </span>
	        
        </div> 

        <div class="header__contact__item__place"><img width="16px"; src="/svg/location-point.svg"><span  class="header__contact__item__place__place"><span>г. Москва</span> </span>	      
	   
	    
	
	    </div>    

    </div>


        	    
	</div>    







  
  <div class="content">

      <?= $this->fetch('content') ?>			  

  </div>  

    
  <footer>
	<?= $this->Flash->render() ?>  
  </footer>
   
</body>
</html>
