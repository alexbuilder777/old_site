<div class="content-page">

  <DIV id="setTabs" class="tabs-custom">


    <ul class="tabs-custom__ul">
      <li class="choice_selected"><a href="#profnastil"> <i class="tabs-custom__ul__icon material-icons">done_all</i>  Профнастил</a></li>
      <li><a href="#metallShtaketnik"> <i class="tabs-custom__ul__icon material-icons">done_all</i> Металлический штакетник</a></li>
      <li><a href="#setkarabica"> <i class="tabs-custom__ul__icon material-icons">done_all</i> Сетка рабица</a></li>    
    </ul>


	<DIV id="profnastil" style="background-color: #f6f7f8;">	
      <table width="100%" border="0">
      <tr><td style="width: 500px;">
        <?= $this->Form->create(null ,[
	            'url' => [ 'action' => 'getPrice' ],
	            'class' => 'formCalc', 
	            'id' => 'profnastil',
	            'templates' => [ 'inputContainer' => '{{content}}', 'formGroup' => '{{input}}']
	            ]) ?>
        <?=$this->Form->hidden('filter.catalog_id',array('value' => 1))?>
        <?=$this->Form->hidden('goalsPrice',array('value' => '7000'))?>
        <?=$this->Form->hidden('countPrice',array('value' => '4000'))?>

      <div class="box-input">
        <label class="box-input__label">Общая длина</label>
        <?= $this->Form->input('width', ['class' => 'box-input__text', 'type' => 'text', 'value' => '0.00', 'placeholder' => 'Введите длину' ]) ?>
        <span class="box-input__info"></span>
      </div>
      
      <div class="box-input">
	    <label class="box-input__label">Высота забора (профнастила)</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('filter.custom_height', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> 'Выбрать',
	                'options' => [ '150' => '150 cм', '180' => '180 cм', '200' => '200 cм' ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div> 


      <div class="box-input">
	    <label class="box-input__label">Выбрать полимер:</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('filter.custom_depth', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> 'Выбрать',
	                'options' => [                         
	                    '1' => 'С полимерным покрытием',
                        '2' => 'Двустороннее покрытие',
                        '3' => 'Без покрытия (оцинковка)' ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div> 

      <div class="box-input">
	    <label class="box-input__label">Покраска:</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('filter.custom_painting', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> 'Выберите вариант покраски',
	                'options' => [                         
                        '1' => 'Грунтовка',
                        '2' => 'Хаммерайт'  
                        ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div> 


      <div class="box-input">
	    <label class="box-input__label">Кол-во горизонталей (лаги):</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('filter.custom_lagi', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> false,
	                'options' => [                         
                        '2' => 'Лаги в два ряда',
                        '3' => 'Лаги в три ряда'   
                        ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div>


      <div class="box-input">
	    <label class="box-input__label">Количество ворот:</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('goals', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> 'Без ворот',
	                'options' => [                         
                        '1' => '1 шт.',
                        '2' => '2 шт.',
                        '3' => '3 шт.' 
                        ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div>                

      <div class="box-input">
	    <label class="box-input__label">Количество калиток:</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('count_wickets', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> 'Без калитки',
	                'options' => [                         
                        '1' => '1 шт.',
                        '2' => '2 шт.',
                        '3' => '3 шт.',
                        '4' => '4 шт.',
                        '5' => '5 шт.'
                        ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div> 

                
                
        <?= $this->Form->button(__('Рассчитать'), [ 'class' => 'btn btn-sea','div' => false ]); ?>
        <?= $this->Form->end() ?>
        </td>
        <td valign="top" style="padding-left: 30px;padding-top: 10px;">
            <div class="inContent"></div>
        </td>
        </tr>
        </table>
        <div id="output-profnastil" style="display: none;"></div>
	</DIV>	







	<DIV id="metallShtaketnik" style="background-color: #f6f7f8;">	
            <table width="100%" border="0">
            <tr><td style="width: 500px;">

        <?= $this->Form->create(null ,[
	            'class' => 'formCalc',
	            'id' => 'metal', 
	            'templates' => [ 'inputContainer' => '{{content}}', 'formGroup' => '{{input}}']
	            ]) ?>
        <?=$this->Form->hidden('filter.catalog_id',array('value' => 2))?>
        <?=$this->Form->hidden('goalsPrice',array('value' => '7000'))?>
        <?=$this->Form->hidden('countPrice',array('value' => '4000'))?>


      <div class="box-input">
        <label class="box-input__label">Общая длина</label>
        <?= $this->Form->input('width', ['class' => 'box-input__text', 'type' => 'text', 'value' => '0.00', 'placeholder' => 'Введите длину' ]) ?>
        <span class="box-input__info"></span>
      </div>

      <div class="box-input">
	    <label class="box-input__label">Высота забора</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('filter.custom_height', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> 'Выбрать',
	                'options' => [ '150' => '150 cм', '180' => '180 cм', '200' => '200 cм' ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div> 

      <div class="box-input">
	    <label class="box-input__label">Полимер:</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('filter.custom_polimer', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> 'Выбрать',
	                'options' => [ 
                        '0' => 'Односторонний',
                        '1' => 'Двусторонний'
	                ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div> 


      <div class="box-input">
	    <label class="box-input__label">Покраска:</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('filter.custom_painting', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> 'Выберите вариант покраски',
	                'options' => [                         
                        '1' => 'Грунтовка',
                        '2' => 'Хаммерайт'  
                        ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div> 


      <div class="box-input">
	    <label class="box-input__label">Кол-во горизонталей (лаги):</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('filter.custom_lagi', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> false,
	                'options' => [                         
                        '2' => 'Лаги в два ряда',
                        '3' => 'Лаги в три ряда'   
                        ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div>


      <div class="box-input">
	    <label class="box-input__label">Количество ворот:</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('goals', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> 'Без ворот',
	                'options' => [                         
                        '1' => '1 шт.',
                        '2' => '2 шт.',
                        '3' => '3 шт.' 
                        ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div>                

      <div class="box-input">
	    <label class="box-input__label">Количество калиток:</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('count_wickets', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> 'Без калитки',
	                'options' => [                         
                        '1' => '1 шт.',
                        '2' => '2 шт.',
                        '3' => '3 шт.',
                        '4' => '4 шт.',
                        '5' => '5 шт.'
                        ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div> 

                
                
        <?= $this->Form->button(__('Рассчитать'), ['class' => 'btn btn-sea','div' => false ]); ?>
        <?= $this->Form->end() ?>
        </td>
        <td valign="top" style="padding-left: 30px;padding-top: 10px;">
            <div class="inContent"></div>
        </td>
        </tr>
        </table>

	</DIV>	







	<DIV id="setkarabica" style="background-color: #f6f7f8;">	
            <table width="100%" border="0">
            <tr><td style="width: 500px;">


        <?= $this->Form->create(null ,[
	            'class' => 'formCalc', 
	            'templates' => [ 'inputContainer' => '{{content}}', 'formGroup' => '{{input}}']
	            ]) ?>
        <?=$this->Form->hidden('filter.catalog_id',array('value' => 3))?>
        <?=$this->Form->hidden('goalsPrice',array('value' => '4200'))?>
        <?=$this->Form->hidden('countPrice',array('value' => '2700'))?>




      <div class="box-input">
        <label class="box-input__label">Общая длина</label>
        <?= $this->Form->input('width', ['class' => 'box-input__text', 'type' => 'text', 'value' => '0.00', 'placeholder' => 'Введите длину' ]) ?>
        <span class="box-input__info"></span>
      </div>

      <div class="box-input">
	    <label class="box-input__label">Высота забора</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('filter.custom_height', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> 'Выбрать',
	                'options' => [ '150' => '150 cм', '180' => '180 cм', '200' => '200 cм' ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div> 




      <div class="box-input">
	    <label class="box-input__label">Покраска:</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('filter.custom_painting', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> 'Выберите вариант покраски',
	                'options' => [                         
                        '1' => 'Грунтовка'
                        ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div> 


      <div class="box-input">
	    <label class="box-input__label">Протяжка арматуры:</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('filter.custom_lagi', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> false,
	                'options' => [                         
                        '1' => 'Один ряд',
                        '2' => 'Два ряда' 
                        ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div>



      <div class="box-input">
	    <label class="box-input__label">Количество ворот:</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('goals', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> 'Без ворот',
	                'options' => [                         
                        '1' => '1 шт.',
                        '2' => '2 шт.',
                        '3' => '3 шт.' 
                        ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div>                

      <div class="box-input">
	    <label class="box-input__label">Количество калиток:</label>
	    <span class="box-input__select-wrap">  
            <?= $this->Form->input('count_wickets', [
	                'class' => 'selectmenu box-input__select', 
	                'type' => 'select', 
	                'empty'=> 'Без калитки',
	                'options' => [                         
                        '1' => '1 шт.',
                        '2' => '2 шт.',
                        '3' => '3 шт.',
                        '4' => '4 шт.',
                        '5' => '5 шт.'
                        ] 
	            ]) ?>
        </span>
        <span class="box-input__info"></span>
      </div> 

                
                
        <?= $this->Form->button(__('Рассчитать'), ['class' => 'btn btn-sea','div' => false ]); ?>
        <?= $this->Form->end() ?>
        </td>
        <td valign="top" style="padding-left: 30px;padding-top: 10px;">
            <div class="inContent"></div>
        </td>
        </tr>
        </table>

	</DIV>




	
  </DIV>


</div>

<script> 
$(document).ready(function(){



    $('.formCalc button').on('click', function(e){
//alert($(".formCalc").attr('action'));
      var data = $(this).closest('form').serialize();
      var target = $(this).closest('tr').find('.inContent'); 

      $.ajax({
          url: $(".formCalc").attr('action'),
          target: $(this).closest('tr').find('.inContent'),
          type:'POST',
          data: data,
          success: function( response, textStatus ) { 
              target.html(response); //console.log($(this).closest('td').closest('tr').html());
          }
      });
      
      return false;
                
    });


/*

    var options = {
            beforeSerialize:  function() {  
          alert('67');    
            }, 
            beforeSubmit: function(arr, $form, options) { 
                outC = $form.closest('tr').find('.inContent');                  
            },
            url: '/calculations/getPrice/',
            target:        "#outputProfnastil",         	    
            success: function(){
              //  $("#popup_module").popModule('<b>Данные обновлены</b>');
              var html = $("#outputProfnastil").html();
              outC.html(html);
       	       
            }    
        }; 
alert('88');
    
    $(".formCalc").ajaxForm(options);
*/

    
  //  $("#list_choice").maximafTabs('list_choice');
    

});
</script>        
