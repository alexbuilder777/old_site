(function ($) {
  'use strict';
    
/************************************************** Main Component*/  
  window.core = {
    name: 'NelbitCore',
    version: '1.1.1',
    saveForm: saveForm,
    closeSideBar: closeSideBar,
    select: setSelectMenu,
    files : {
	    
    },
    setOptions : function(element, attr = null) {
	  
	  if(!attr) {
		var attr = 'param'; 
	  }
 
	  var param = element.attr(attr).replace(/\'/g, '"');
	  param.replace(/\r|\n/g, '')
	  param = JSON.parse(param);

      param['value'] = element.val();
	      
	  return param;
    }
  };



  $.fn.serializeFormJSON = function () {

      var o = {};
      var a = this.serializeArray();
      $.each(a, function () {
          if (o[this.name]) {
              if (!o[this.name].push) {
                  o[this.name] = [o[this.name]];
              }
              o[this.name].push(this.value || '');
          } else {
              o[this.name] = this.value || '';
          }
      });
      return o;
  };




/************************************************** Call Modal Window*/

  $('body').delegate( 'button[sb-param]', "click", function() { 

    var param = core['setOptions']($(this), 'sb-param'); 

    if(typeof param['server-action'] != 'undefined') {

      var wrap_from_load = param['call-id']+'-cntr';

	  var element_check = $("div").is('#'+param['call-id']); // проверка существования элементы	
	  if(!element_check) { 
	    $('#sidebar').append('<div id="'+wrap_from_load+'" class="sb-blok"></div>');
	  }  
	 console.log($('#'+wrap_from_load).html());
	  $('#'+wrap_from_load).load(param['server-action'], function(){ 
	   // $('#'+param['call-id']).css({"display" : "block"});

        $('#sidebar').css({"display" : "block"});
        $(this).find(".selectmenu").each(function () { 
	 	    setSelectMenu($(this));  
	    }); 
	  }); 
	} 
        
  });

  $('body').delegate( '.sb-close', 'click', function(){
	closeSideBar($(this));  	
  });
  
  function closeSideBar(element) { 
    element.closest('.sb-blok').remove();

    if($('#sidebar').html().length == 0) {
	  $('#sidebar').css({"display" : "none"});
    }	  
  }  

/************************************************** Call Modal Window*/

  $('body').delegate( 'button[modal-set]', "click", function() { 

    var param = core['setOptions']($(this), 'modal-set'); 

    if(typeof param['server-action'] != 'undefined') {

      var wrap_from_load = param['call-id']+'-cntr';

	  var element_check = $("div").is('#'+param['call-id']); // проверка существования элементы	
	  if(!element_check) { 
	    $('#ajax-load').append('<div id="'+wrap_from_load+'"></div>');
	  }  
	  
	  $('#'+wrap_from_load).load(param['server-action'], function(){ 
	    $('#'+param['call-id']).css({"display" : "block"});

        $(this).find(".selectmenu").each(function () { 
	 	    setSelectMenu($(this));  
	    }); 
	  }); 
	} 
        
  });


  
  $('body').delegate( '.modal-white__close-link', 'click', function(){
    $(this).closest('.modal-white').css({'display' : 'none'});	
  });


/*
  $('[modal]').on('click', function(){

	  var param = $(this).attr('modal'); 
	  param = JSON.parse(param);

	  var cntr = $(this).closest('.box').append('<div id="'+$(this).attr('id')+'-cntr" class="modal fade"  role="dialog"></div>');
	  $('#'+$(this).attr('id')+'-cntr').load(param['link']);
	  $('#'+$(this).attr('id')+'-cntr').modal('show', {closeExisting: false});
	  return false;
  });
*/




  
/************************************************** Set listening for button click ajaxForm	*/
  
  function saveForm( button, param ){
     
    var currentBlok = button.closest('.sb-blok');  
    var currentForm = button.closest('form'); 
    
    var options = { 
      beforeSerialize:  function(form) { 
      }, 
      beforeSubmit: function(arr, $form, options) { 
        currentForm.html('<p><img src="/img/svg/ring.svg" class="ajaxRing" /> <br> Выполняю.. </p>');               
      }, 
      success: function(response, status, xhr){ 
	    currentBlok.remove();    
        window[param['model']][param['callback']](currentForm, response, button);
      },
      error: function(xhr, status, error){
        alert('Ошибка');
        console.log(xhr);
  
      }
    };
  
    currentForm.ajaxForm(options);
    currentForm.submit();
  }    



  /*
   * BUTTON Set listening for button click ajaxForm	
   */ 
  $('body').delegate( '.button-ajax-form', "click", function() { 

      var param = core['setOptions']($(this));  

      core['saveForm']($(this), param);

      return false;      
  });


  $('body').delegate( 'button[listenAction="saveAjaxForm"]', "click", function() {
      var modelName = $(this).attr('model');
      core['saveForm']($(this), modelName); console.log($(this));
  });
    
  $('div[load-ajax]').each(   function() {		  
    load_ajax($(this), 'data-post');
  });





  function load_ajax(element, data_alias = null) {
    
    if(!data_alias) { var data_alias =  'param';}
    
    var data = {};
    if(typeof element.attr(data_alias) != 'undefined') {
      data = core['setOptions'](element, data_alias);	  
    }
 	  
    element.load(element.attr('load-ajax'), data);  
  }




  $('body').delegate( 'button[data-dismiss$="-close"]', "click", function() { 
      $(this).closest('.modal').modal('hide');
  });
    




  /*
   * SELECT Set listening for button click ajaxForm	
   */
   
  function setSelectMenu(element) {
   element.selectmenu({
      width: 100+'%',
      change: function( event, data ) { 
          
        if(data.item.optgroup.length != 0) {

          var param = JSON.parse(data.item.value);        
          
          window[param['controller']]['select'][param['callback']](param['actionParam']);
	     
        
          // set list NULL   
  	     /*
  	      $(this).val("");
  	      $(this).selectmenu("refresh");
  	       
  	      var targetDiv = 
  	      '<div class="modal fade" id="addSelectMenu-cntr" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true"></div>';
  	     	
  	      $("#ajaxLoad").prepend(targetDiv);//
  	     	
  	      $("#addSelectMenu-cntr").load(data.item.value);
          $("#addSelectMenu-cntr").modal('show', {closeExisting: false});
         */ 
        }
      }
    });  
	  
  }   







  /*
   * Editable Form Data
   */


    $(document).on('focus',"input.input-editable_noactive, input.input-editable_active", function() {
      $(this).removeClass( "input-editable_noactive" ).addClass( "input-editable_active" );
    }).on('blur',"input.input-editable_noactive, input.input-editable_active", function() {
	    
  	  var param = core['setOptions']($(this));  	  
  	  
  	  $('#ajax-load').css({'display' : 'block'});
  	  $("#ajax-load").load('/basemaps/editSerializeDataLinear/', param, function(response){
  	  	$(this).html(response);
  	  });
      $(this).removeClass( "input-editable_active" ).addClass( "input-editable_noactive" );
    });





  /*
   *  SEARCH INPUT Set listening to input that in name have string 	searchlist
   */
  window.pressTime = 0;

  $('body').delegate( ".getJsonlist", "keyup", function(event) {

      if(pressTime == 0) {
	    
	    var input = $(this); 
	    var param = core['setOptions'](input);

/*	    
	    if(typeof param['controller'] == 'undefined') { // if dont set param controller, set data from closest select typelist		 
		    var paramSelect = input.closest('div.row').find('.selectmenu').val();
		    paramSelect = JSON.parse(paramSelect);
		    
		    param['controller'] = paramSelect['controller'];		    
	    }
*/	    
	    var data = {}; 

	    data['string'] = event['currentTarget']['value']; 
        
        if(data['string'].length > 0) {
          $.ajax({
              url: '/'+param['controller']+'/'+param['action'],
              type:'POST',
              data: data,
              success: function( jsonData, textStatus ) { 
                  setTimeout(makeContainer, 500, input, event, jsonData['response'][param['controller']]);
              }
          });
          pressTime = 1;
        }

      }  

  });


  function makeContainer(input, event, data) {

    var posTop = input.offset().top + input.outerHeight(); //alert(input.offset().top);
    var posLeft = input.offset().left;

    var container_name = input.attr('name')+'_Cntr';     
    $('#sidebar').append('<div id="'+container_name+'" class="input-jsonlist-result"></div>');
    var cntr = $("#"+container_name);
  
  
    if(event.key != 'Backspace') {
        var inputValue = input.val();
    } else {
        var inputValue = input.val();
        var inputValue = inputValue.substring(0, inputValue.length - 1);
    }
  
    if(inputValue.length >= 1){   

      cntr.css({ 
          'top': posTop+'px',	
          'left': posLeft+'px',	            
          'width': Math.round(input.outerWidth())+'px', 
          'position': 'absolute', 
          'z-index': '301' 
      }); 
      cntr.html('<p><img src="/img/svg/ring.svg" class="ajaxRing" /><br> Идет поиск...</p>');


      window.activeSelectList = '<ul class="input-jsonlist-result__ul">'; 
      $.each(data, function(i, value) { 
	   
	    var param = JSON.parse(input.attr('param'));  
	    param['record_id'] = i; 
	    param['value'] = value;   

	    if(typeof param['controller'] === 'undefined') { // if dont set param controller, set data from closest select typelist
		    
		    var paramSelect = input.closest('div.row').find('.selectmenu').val();
		    paramSelect = JSON.parse(paramSelect);
		    
		    param['controller'] = paramSelect['controller'];
		    
	    }
	      
        activeSelectList += 
          '<li class="input-jsonlist-result__li">'+
          '<a href="#" class="input-jsonlist-result__link" param=\''+JSON.stringify(param)+'\'</a>'+value+
          '</li>';
      });
      activeSelectList += '</ul>';
     
      cntr.html(activeSelectList);
      cntr.show();
    
    } else {
      cntr.hide();
    }  
    window.pressTime = 0;
  }


    $('body').delegate('.input-jsonlist-result__link', 'click', function() {  
	  var param = JSON.parse($(this).attr('param')); 
      window[param['controller']][param['callback']](param, $(this));
      $('.input-jsonlist-result').remove();
    });


  $('body').delegate( ".input-jsonlist-result", "mouseleave", function(event) {
    $(this).remove();	  
  });	  





/*

  $('body').delegate( "input[name$='getJsonlist']", "keypress", function(e) { 
      if(pressTime == 0) {
        var input = $(this);

        $.ajax({
            url: $(this).attr('action'),
            type:'POST',
            data: $(this).val(),
            dataType: 'json',
            success: function( json ) {console.log(json);
                setTimeout(setContainer, 1000, input, e, json);
            }
        });
        
        pressTime = 1;
         
      }
  });


  function setContainer(input, e, data) {
  
    var positionInp = input.offset();  //console.log(positionInp);
    var posTop = positionInp.top + input.outerHeight();
    var posLeft = positionInp.left;
    var container_name = input.attr('name')+'_Cntr';
     
    input.closest('div').append('<div id="'+container_name+'" class="input-jsonlist-result"></div>');
    var cntr = $("#"+container_name);
  
  
    if(e.key != 'Backspace') {
        var stringVal = input.val();
    } else {
        var stringVal = input.val();
        var stringVal = stringVal.substring(0, stringVal.length - 1);
    }
  
    if(stringVal.length >= 1){  //"top": input.outerHeight()+"px", 
        //console.log(Math.round(input.outerWidth()));
        var top = Math.round(input.outerHeight()) + 10;
        top += 'px';
        cntr.css({ 
            'top': top,	            
            'width': Math.round(input.outerWidth())+'px', 
            'position': 'absolute', 
            'z-index': '10001' 
        }); 
        cntr.html('<p><img src="/img/svg/ring.svg" class="ajaxRing" /><br> Идет поиск...</p>');

      window.activeSelectList = '<ul class="input-jsonlist-result__ul">';
      $.each(data, function(i, value) { 
          //cntr.append($('<li>').text(value).attr('value', i));
        activeSelectList += 
          '<li class="input-jsonlist-result__li">'+
          '<a href="#" class="input-jsonlist-result__link" param=\'{ "pattern_id" : "'+i+'" }\' core-controller="'+input.attr('core-controller')+
          '" callback="'+input.attr('callback')+'">'+value+'</a>'+
          '</li>';
      });
      activeSelectList += '</ul>';
      
      cntr.html(activeSelectList);
      cntr.show();
    
    } else {
      cntr.hide();
    }  
    window.pressTime = 0;
  }





*/
 /*    
    $('body').delegate( "input[name$='searchlist']", "keypress", function(e) {
        if(pressTime == 0) {
            setTimeout(findRow, 1000, $(this), e);
            pressTime = 1;
        }
    });



        
    function findRow(input, e) {
   
        var positionInp = input.offset(); //console.log(positionInp);
        var posTop = positionInp.top + input.outerHeight();
        var posLeft = positionInp.left;
        var container_name = input.attr('name')+'_Cntr';
         
        input.closest('div').append('<div id="'+container_name+'" class="find-result"></div>');
        var cntr = $("#"+container_name);
    
    
        if(e.key != 'Backspace') {
            var stringVal = input.val();
        } else {
            var stringVal = input.val();
            var stringVal = stringVal.substring(0, stringVal.length - 1);
        }
    
        if(stringVal.length >= 1){  //"top": input.outerHeight()+"px", 
            //console.log(Math.round(input.outerWidth()));
            var top = Math.round(input.outerHeight()) + 10;
            top += 'px';
            cntr.css({ 
	            'top': top,	            
	            'width': Math.round(input.outerWidth())+'px', 
	            'position': 'absolute', 
	            'z-index': '10001' 
	        }); 
            cntr.html('<p><img src="/img/svg/ring.svg" class="ajaxRing" /><br> Идет поиск...</p>');
                              
            var action = input.attr('action');
         
            cntr.load(action,{ "string": stringVal, "conteynerName": container_name, "inputName": input.attr('name') });
            cntr.show();
       
        } else {
            cntr.hide();
        }  
        pressTime = 0;
    }

*/
/*
    $("select").selectmenu({
    	width: 100+'%'
    });
*/
    
  $('#sub-menu a').on( 'click',  function(){
  	var target = $(this).attr('data-target');
  	
  	if(typeof target != "undefined") {
  	  var target = $(this).attr('data-target').replace(/#/,'');
  	  var targetDiv = '<div class="modal fade" id="'+target+'" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true"></div>';
  	  $('#ajaxPrepend').prepend(targetDiv);//
  	  
  	  $("#"+target).load($(this).attr('href'));
  	  $("#"+target).modal('show');
      return false;
  	}
  });

    
/************************************************** LINKS LISTENING */

  function listenChildrenLinks(target) {
      target.delegate( "a", "click", function() {
          parent_target.load($(this).attr('href'));
          return false;
      });
  }

  $(document).on('click', '.link-confirm', function(){

	var param = core['setOptions']($(this));

	var link_current = $(this);
	if (confirm(param['confirm-msg'])) { 

      $('#ajax-load').load(link_current.attr('href'), param, function(rez){
	      var up_cntr = link_current.closest('div[load-ajax]');
          up_cntr.load(up_cntr.attr('load-ajax'));	  
          $('#ajax-load').html(rez);   
      });
	} 	


	return false;  
  });


  $(document).on('click', '.hide-tool-open-link', function(){

	$(this).closest('div').find('.hide-tool-body').toggle(); 
	//element.slideToggle('fast');
	return false;  
  });



/************************************************** Catalogs Component*/


	window.catalogs = {
      addCatalogCallback: function(form) { // from find input add contract
          console.log(form);
          form.closest('.modal').modal('hide');
          $('div[load-ajax]').each( function() {
            $(this).load($(this).attr('load-ajax'));
          }); 
      }
    };



/************************************************** Users Component*/


	window.users = {
      upList: function(form) { // from find input add contract

          form.closest('.modal-white').css({'display' : 'none'});
          $('div[load-ajax]').each( function() {
            $(this).load($(this).attr('load-ajax'));
          }); 
      }
    };


/************************************************** Cities Component*/


	window.cities = {
      onClickGetList: function(param) { // from find input add contract

	    var inputID = $("#"+param['form']).find('input[name="city_id"]');  // set value to parent_id
	    var inputName = $("#"+param['form']).find('input[name="city_name"]');  // set value to name
	    inputID.val(param['record_id']);
	    inputName.val(param['value']);       
      }
    };




/************************************************** Patterns Component*/


	window.patterns = {
      onClickGetList: function(param) { // from find input add contract

	    var inputID = $("#"+param['form']).find('input[name="pattern_id"]');  // set value to parent_id
	    var inputName = $("#"+param['form']).find('input[name="pattern_name"]');  // set value to name
	    inputID.val(param['record_id']);
	    inputName.val(param['value']);
	    
	    var row = inputID.closest('div.row');
	    row.append('<div id="getPatternRoles">');

	    $('#getPatternRoles').load('/patterns/getPatternRoles/'+param['record_id'], function(rez) {

          $(this).html(rez);
          
          $(this).find(".selectmenu").each(function () {	   	    
	   	    core['select']($(this));   
	      });
	      
        });        
      },
      select: {
	    selectOptGroupItem: function(param) { // from select type contragents
		    console.log(param);
		  alert(param['url']);
		  $ 
	    }  
      }
    };


/************************************************** Companies Component*/


	window.companies = {
      onClickGetList: function(param, link) { // from find input add contract
 
        var alias = link.closest('.inputs-groups').attr('id');
	    var inputsGroupsItems = link.closest('.inputs-groups').find('.inputs-groups-items');

        var inputsAdd = '<input name="contragents[groups]['+alias+']['+param['record_id']+'][id]" type="hidden" value="'+param['record_id']+'">'+
                        '<input name="contragents[groups]['+alias+']['+param['record_id']+'][controller]" type="hidden" value="'+param['controller']+'">'+
                        '<label class="inputs-groups-items__label">'+param['value']+ 
                           '<i class="inputs-groups-items__close fa fa-times" aria-hidden="true"></i>'+
                        '</label>';
        inputsGroupsItems.append(inputsAdd);                 
	    
	   // this.appendID++;  
      },    
      select: {
	    selectOptGroupItem: function(param) { // from select type contragents
		 // alert(param['url']);  
	    } 
	     
      }
    };



/************************************************** Entrepreneurs Component*/


	window.entrepreneurs = {
      onClickGetList: function(param, link) { // from find input add contract
 
        var alias = link.closest('.inputs-groups').attr('id');
	    var inputsGroupsItems = link.closest('.inputs-groups').find('.inputs-groups-items');

        var inputsAdd = '<input name="contragents[groups]['+alias+']['+param['record_id']+'][id]" type="hidden" value="'+param['record_id']+'">'+
                        '<input name="contragents[groups]['+alias+']['+param['record_id']+'][controller]" type="hidden" value="'+param['controller']+'">'+
                        '<label class="inputs-groups-items__label">'+param['value']+ 
                           '<i class="inputs-groups-items__close fa fa-times" aria-hidden="true"></i>'+
                        '</label>';
        inputsGroupsItems.append(inputsAdd);                 
	    
	   // this.appendID++;  
      },    
      select: {
	    selectOptGroupItem: function(param) { // from select type contragents
		 // alert(param['url']);  
	    } 
	     
      }
    };



/************************************************** Nomenclatures Component*/


	window.nomenclatures = {
	  addCallback: function(form, response) { window.location.href = response['location_href']; },	
      onClickGetList: function(param, link) { // from find input add contract

	    var inputID = $("#"+param['form']).find('input[name="nmcl_id"]');  // set value to parent_id
	    var inputName = $("#"+param['form']).find('input[name="nmcl_name"]');  // set value to name
	    inputID.val(param['record_id']);
	    inputName.val(param['value']);
	     
      },    
      select: {
	    selectOptGroupItem: function(param) { // from select type contragents
		 // alert(param['url']);  
	    } 
	     
      }
    };


/************************************************** Remains Component*/


	window.remains = {
      afterSave : function(currentForm, response, button) {
	    closeSideBar(button); 
      }
    };


/************************************************** Contents Component*/


	window.contents = {
	  addContent: function(form, response) { 
		form.closest('.modal').modal('hide');
		load_ajax($('#box-contents'));
      }
    };


/************************************************** Tabs*/



    $.fn.setTabs = function(id){ 

	    $("#"+id).children("div").each(function(){ 
		    $(this).css('display','none');
	    });
	    
	    
      //  $.cookie("ul_choice_select", "catalogs");
        if($.cookie(id) != "undefined") {
            
            setTimeout(getTab , 100);
            
            function getTab() {
                var coocID = $.cookie(id);
                $("a[href='"+coocID+"']").click();
            }					   

            
          //  $("a[href='#catalogs']").click();
            
            
        }
	    
	    var sel_div = $(".choice_selected").children("a").attr("href");
	    $(sel_div).css('display','block');

        
        $("#"+id+" .tabs-custom__ul li").on("click", function(){
            eachTabs($(this));
            return false;
        });


        function eachTabs(link){
        
            var link_id = link.children("a").attr("href");
        
	        var parent_ul = link.closest("ul");
	        
	        parent_ul.children("li").each(function(){
	            var li_href = $(this).children("a").attr("href");
	            if(li_href == link_id){
		            $(this).attr("class","choice_selected");
		            $.cookie(id, link_id);
	            } else {
		            $(this).attr("class","");
	            }
	        });


	        link.closest("div").children("div").each(function(){
	            if(link_id == "#"+$(this).attr("id")) {
		            $(this).css('display','block');
		        } else {
		            $(this).css('display','none');			        
		        }	        
	        });	        	            
	        return false;
	        
        }

                
      
    };

/************************************************** Use Pligins*/
    $("#setTabs").setTabs('setTabs');
   // $('.img-round').roundImage();


/**************** Call Tooltip */

 // $('[data-toggle="tooltip"]').tooltip();   



  
  $(".selectmenu, .form-ajax select, .form-with-label select").selectmenu({
      width: 100+'%',
      change: function( event, data ) { 
          
        if(data.item.optgroup.length != 0) {

          var param = JSON.parse(data.item.value);        
          
          window[param['controller']]['select'][param['callback']](param['actionParam']);
	     
        
          // set list NULL   
  	      $(this).val("");
  	      $(this).selectmenu("refresh");
  	       
  	     // var targetDiv = 
  	     // '<div class="modal fade" id="addSelectMenu-cntr" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true"></div>';
  	     	
  	    //  $("#ajaxLoad").prepend(targetDiv);//
  	     	
  	    //  $("#addSelectMenu-cntr").load(data.item.value);
        //  $("#addSelectMenu-cntr").modal('show', {closeExisting: false});
          

        }
      }
  });








/*
        $(".dex_select").click(function() {
            parent_cont = $(this).parents("tr");
            parent_cont.find("div.load_dex").load($(this).attr("href")).modal();
            return false;
        });

//----------------------------------------------- если кликнули на добавить или редактировать подпункт


        $('.partAdd').on('click', function() {
	        
            var parent_id = $(this).attr('parent');
            var level = parseInt($(this).attr('level')) + 1;
            
            $("#basic-modal-content").load($(this).attr("href"), { parent_id: parent_id, level: level});
            
            $("#basic-modal-content").modal({
                position: ['10%','30%'],
                minHeight: '550',
                minWidth: 650,    
            });	        	        
	        
        });    

        $('.partEdit').on('click', function() {
	        
            var parent_id = $(this).attr('parent');
            var level = parseInt($(this).attr('level')) + 1;
            
            $("#basic-modal-content").load($(this).attr("href"), { parent_id: parent_id, level: level, text: $(this).closest('li').children('.textPart').text(), lft: $(this).attr('lft')});
            
            $("#basic-modal-content").modal({
                position: ['20%','30%'],
                minHeight: '450',
                minWidth: 550,    
            });	        
	        
	        
        }); 

	
	$("#menu_list a").on('click', function(event){

        if($(this).attr("class") == "partDelete") {    
            if (confirm("Вы уверены, удалить пункт?")) {
	    
                var lft = $(this).attr('lft');
                var rght = $(this).attr('rght');

                $("#basic-modal-content").load($(this).attr("href"), { lft: lft, rght: rght});
                $("#partsConteyner").load("/<?=ADMIN?>/patterns/getParts/<?=$data->id?>");
                $.modal.close();
	    
                return false;
            } 
            return false;
            }
               
        else if($(this).attr("class") == "partAdd55" || $(this).attr("class") == "partEdit88") {     
            
            var parent_id = $(this).attr('parent');
            var level = parseInt($(this).attr('level')) + 1;
            
            $("#basic-modal-content").load($(this).attr("href"), { parent_id: parent_id, level: level});
            
            $("#basic-modal-content").modal({
                position: ['20%','30%'],
                minHeight: '250',
                minWidth: 550,    
            });
        }
                                         
        return false;
        
   });

//----------------------------------------------- Х если кликнули на добавить или редактировать подпункт Х


	
          var i=0; // первая цифра
        //  var c=1;// конечная цифра
 
    $("#menu_list li").each(function () {          

           var parent = $(this).parents('ul');
          // var c=1;
           
                if(parent.length == 1){	            //-----------------нумеруем самые первые ul
          
                $(this).parent('ul').attr('id',i);
                $(this).parent('ul').attr('class','');            
                
                i++;
                window.par = i;             
              //  par++;      
	            $(this).prepend('<span><b>'+i+'.</b></span> ');                          
               
                }                                    //-----------------ХХХнумеруем самые первые ul
          
                else {
          
          
          if($(this).prev().length == 1)      //------------------- если это не первый элемент li
          {
       //   c++;
          c = parseInt($(this).prev().attr("class")) +1;
	 //     par = par+'.'+c;
	    //  $(this).attr('class',c);
	    //  var test = $(this).parent('ul').attr('id') + 1;
	    window.par = $(this).parents('ul').attr('id')+'.'+c;
	    
          $(this).prepend('<span><b>'+$(this).parents('ul').attr('id')+'.'+c+'.</b></span> '); 

           // var test = parseInt(c) +1; 
            $(this).attr('class',c);          
          	          
          }
          else{                              //------------------- ХХХесли это не первый элемент li

            c = 1;
            
            
         //  window.par = $(this).parents('ul').attr('id');
          //    window.par = $(this).parents('ul').attr('id')+'.'+$(this).parent('ul').attr('class');     
            $(this).parent('ul').attr('id',par);
            $(this).parent('ul').attr('class',c);
            
            //          alert($(this).parent('ul').attr("id")); 
            
          //  var test = parseInt(c) +1; 
            $(this).attr('class',parseInt(c));
            
         //   window.par_prev = par;
            window.par = par+'.'+c;        

        //  par_c = par+'.'+c;
          $(this).prepend('<span><b>'+par+'.</b></span> '); 
	        }
          }                      

 });

 


  
    $("#hide_ul_ul").click(function() {
        $("#menu_list ul ul").slideToggle("");
        return false;
    }); 
  
  
    $( "#menu_list li" ).each(function( index ) {
        var ids = $(this).children("a").attr("class");
        $(this).attr("id", ids);
    });  


    $("ul ul,#menu_list").sortable({

  	            
        start: function(event, ui) {
            var id = ui.item.attr('id');
            ui.item.startPos = ui.item.index();
        },

	    
	    update: function(event, ui) {
	    
           ui.item.newPos = ui.item.index();
                                     
           if(ui.item.startPos < ui.item.index()){
               var id = ui.item.attr('id');
               delta = ui.item.index() - ui.item.startPos;
	    
               $.ajax({
                   url: "/parts/movedown/"+id+"/"+delta,
                   type:"GET",
               });
        
           } else {
               var id = ui.item.attr('id');
               delta = ui.item.startPos - ui.item.index();
	    
               $.ajax({
                   url: "/parts/moveup/"+id+"/"+delta,
                   type:"GET",
               });
	    
           }
	 
  	   }
	    
    });
    
    
    
    
    $( "#sortable" ).disableSelection();
    
 */   









})(jQuery);