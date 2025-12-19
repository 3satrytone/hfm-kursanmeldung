;(function($){
	var sendFormStep3 = false;
	var slng = 0;
	var lclng = [];
	lclng = [{
			'wait' : 'Bitte warten, dieser Vorgang kann einige Minuten dauern.',
			'cancel': 'abbrechen',
			'more': 'weiter'
		},
		{
			'wait' : 'Please wait, this process may take a few minutes.',
			'cancel': 'cancel',
			'more': 'next'
		}
	];
	checkSelectVal = function(val, checkArr){
		var checked = false;
		if(typeof(checkArr) != 'undefined' && checkArr.length>0){
			$.each(checkArr, function(){
				if(this == val) checked = true;
			});
		}else{
			if(val != '') checked = true;
		}
		return checked;
	}

	boxTrigger = function(){
		//Zeigt oder versteckt Matrikelnummer-Input in form2
		var trigger = $('[data-trtype]');
		trigger.click(function() {
			target = $('[data-trtrg="'+$(this).data('trsrc')+'"]');
			var type = $(this).data('trtype');
			var trsel = $(this).data('trsel');
			var trval = $(this).data('trval');
			if(trval) trval = trval.split(',');
			switch(trsel){
				case "checked":
					if($(this).is(':checked')){
						switch(type){
							case "slide":
								target.slideDown();
								break;
						}

					}
					else{
						switch(type){
							case "slide":
								target.slideUp();
								break;
						}
					}
					break;
				case "selected":
					if(checkSelectVal($(this).find('option:selected').val(),trval)){
						switch(type){
							case "slide":
								target.slideDown();
								break;
						}

					}
					else{
						switch(type){
							case "slide":
								target.slideUp();
								break;
						}
					}
					break;
				case "johide":
					target.toggleClass('joHide');
					break;
			}
		});
	}

	changeRoomPrices = function(){
		var hotel = $('[data-trsrc="hotel"] option:selected');
		var room = $('[data-trsrc="room"] option:selected');
		var hotelId = hotel.val();
		var roomId = room.val();

		if(hotelId == ''){
			$('[data-trsrc="room"]').val('');
			$('.roomwith.joCol.joHide').hide();
		}
		$('.hotelfee-row').hide();
		$('#hotelfee-'+hotelId).show();
		$('.hotelfee-price').hide();		
		$('#hotelfee-'+hotelId+'-'+roomId).show();
	}

	changeFee = function(){
		var _this = $('[data-trsrc="matrikel"]');
		var enRFnorm = $('#enrollmentfee').data('feeanmeldung');
		var enRFerm = $('#enrollmentfee').data('feeanmeldungerm');
		var enRFpass = $('#enrollmentfee').data('feepassivgeb');
		var enRFpasserm = $('#enrollmentfee').data('feepassivgeberm');
		var addRFnorm = $('#additionalfee').data('feeaktivengeb');
		var addRFerm = $('#additionalfee').data('feeaktivengeberm');
		
		var _typ = 0;
		var _enrfee = 0;
		var _addfee = 0;
		
		_typ = $('[data-trsrc="tnsel"]').val();
		
		
		
		if(_this.is(':checked')){
			_enrfee = (_typ == 1) ? enRFpasserm : enRFerm;
			_addfee = addRFerm;
			$('.no-registration').hide();
				$('.formhandler-button[type="submit"]').show();
			if(_typ == 1 && enRFpasserm == '0,00'){
				// keine Anmeldung nötig
				$('.no-registration').show();
				$('.formhandler-button[type="submit"]').hide();
			}
		}else{
			$('.no-registration').hide();
			$('.formhandler-button[type="submit"]').show();
			_enrfee = (_typ == 1) ? enRFpass : enRFnorm;
			_addfee = addRFnorm;
		}
		
		if(_typ == 1){
			$('.additionalfee').slideUp();
			$('.gebuehrcom').slideUp();
		}else{
			$('.additionalfee').slideDown();
			$('.gebuehrcom').slideDown();			
		}
		
		$('#enrollmentfee').text(_enrfee+' EUR');
		$('#additionalfee').text(_addfee+' EUR');
	}

	changeStipendium = function(){
		if($(this).is(':checked')){
			$('#enrollmentfee').hide();
			$('#additionalfee').hide();
			$('.stipendiumhint').show();
		}else{
			$('#enrollmentfee').show();
			$('#additionalfee').show();
			$('.stipendiumhint').hide();
		}
	}

	function sendSearch(ev){
        if(ev.keyCode == 13 || ev.keycode == 13){
            sendStr = $(this).attr('name') + '=' + $(this).val();
            var href = window.location.href;
            var urlArr = [];
            $.each(href.split('&'),function(c,q){
                var i = q.split('=');
                if(i[0].toString().indexOf('paginateSearch')<0){
                    if(i[0].toString().indexOf('currentPage')<0){
                        urlArr.push(i[0].toString()+'='+i[1].toString());
                    }else{
                        urlArr.push(i[0].toString()+'=0');
                    }
                }
            });
            url = urlArr.join('&');
            window.location.href = url +'&' +sendStr;
        }
    }

	$(document).ready(function() {
		var dp = null;
		var setAge = null;
		boxTrigger();
		// Preise aktualisieren wenn Raumauswahl geändert
		$('[data-trsrc="room"]').unbind('change',changeRoomPrices);
		$('[data-trsrc="room"]').bind('change',changeRoomPrices);

		$('[data-trsrc="hotel"]').unbind('change',changeRoomPrices);
		$('[data-trsrc="hotel"]').bind('change',changeRoomPrices);

		$('[data-trsrc="matrikel"]').unbind('change',changeFee);
		$('[data-trsrc="matrikel"]').bind('change',changeFee);

		$('.studentship input[type="checkbox"]').unbind('change',changeStipendium);
		$('.studentship input[type="checkbox"]').bind('change',changeStipendium);

		$('[data-trsrc="tnsel"]').unbind('change',changeFee);
		$('[data-trsrc="tnsel"]').bind('change',changeFee);
		
		$('[data-trsrc="tnsel"]').trigger('click');
		$('[data-trsrc="zahlart"]').trigger('click');
		$('[data-trsrc="hotel"]').trigger('click');
		$('[data-trsrc="room"]').trigger('click');
		$('[data-trsrc="duosel"]').trigger('click');
		$('[data-trsrc="duosel"]').trigger('click');
		$('[data-trsrc="matrikel"]').trigger('click');
		$('[data-trsrc="matrikel"]').trigger('click');
		
		if($('[data-slang="1"]').length == 1) slng=1;
		
		changeRoomPrices();

	    $( ".joInpDatepicker" ).datepicker($.datepicker.regional[ "de" ]);
		joToolTipInit = function(){
	    	$(function() {
			    $('input').tooltip();
			  });
	    };
		
		var dpSel;
		function initDP(){
			if(typeof(dpSel) != "undefined"){
				dpSel.removeClass('calendarclass');
				dpSel.removeClass('hasDatepicker');
				dpSel.removeAttr('id');
				dpSel.unbind();
				dpSel = null;
			}
			dpSel = $( ".joInpDatepicker").datepicker({
				changeMonth: true,
				changeYear: true,
				yearRange: "-100:+0",
				onChangeMonthYear : function(selYear, selMonth, dpObj){
					var nd = getDate(dpObj.selectedDay,dpObj.selectedMonth,dpObj.selectedYear);
					$(this).val(nd);
				},
				onClose : function(selectedDate,dpObj){
					var nd = getDate(dpObj.selectedDay,dpObj.selectedMonth,dpObj.selectedYear);
					$(this).val(nd);
				}
			},
			$.datepicker.regional[ "de" ]);
		}
		initDP();
		
	    joCheckAge = function(selectedDate){
	       	if(setAge == null || selectedDate != setAge){
	       		setAge = selectedDate;
		       	var alter = true;
	  			d = new Date();
	  			dateArr = selectedDate.split('.');
	  			cd = new Date(parseInt(dateArr[2]),parseInt(dateArr[1])-1,parseInt(dateArr[0]));
	  			yDiff = d.getYear()-cd.getYear();
	  			if(yDiff>36 || yDiff<17) alter=false;

	  			if(yDiff == 17){
	  				if(d.getMonth() <= cd.getMonth()){
	  					alter=false;
	  					if(d.getMonth() == cd.getMonth() && d.getDate() >= cd.getDate()) alter=true;
	  				}
	  			}
	  			if(yDiff == 36){
	  				if(d.getMonth() >= cd.getMonth()){
	  					alter=false;
	  					if(d.getMonth() == cd.getMonth() && d.getDate() < cd.getDate()) alter=true;
	  				}
	  			}
	  			var title = $('.joAgeWarningTitle').html();
	  			var message = $('.joAgeWarning').html();
	  			if(!alter)$.toaster({ 'message' : message, 'title' : title, priority : 'danger', 'settings': {'donotdismiss':['success','danger']}});
	  		}
	    }
	    checkAge = function(){
	    	var selectedDate = $(this).val();
	    	// check if format tt.mm.jjjj
	    	$(this).removeClass("f3-form-error");
	    	var dateReg = /^\d{2}[.]\d{2}[.]\d{4}$/
	    	formatRight = selectedDate.match(dateReg);
	    	if(formatRight == null)$(this).addClass("f3-form-error");
	    	if(selectedDate != ''){
	    		joCheckAge(selectedDate);
	    	}
	    }
	    getDate = function(selectedDay,selectedMonth,selectedYear){
	    	var nd = new Date(selectedYear,selectedMonth,selectedDay);
	    	var dd = nd.getDate();
			var mm = nd.getMonth()+1; //January is 0!
			var yyyy = nd.getFullYear();
			if(dd<10){
			    dd='0'+dd;
			} 
			if(mm<10){
			    mm='0'+mm;
			} 
			var today = dd+'.'+mm+'.'+yyyy;
			return today;
	    }
	    dp = $( ".joInpDatepickerYM" ).datepicker({
			changeMonth: true,
      		changeYear: true,
      		yearRange: "-100:+0",
      		onChangeMonthYear : function(selYear, selMonth, dpObj){
      			var nd = getDate(dpObj.selectedDay,dpObj.selectedMonth,dpObj.selectedYear);
      			$(this).val(nd);
      		},
      		onClose : function(selectedDate,dpObj){
      			var nd = getDate(dpObj.selectedDay,dpObj.selectedMonth,dpObj.selectedYear);
      			$(this).val(nd);
      			joCheckAge(nd);
      		},
      		onSelect : function(selectedDate){
      			joCheckAge(selectedDate);
      		}},
      		$.datepicker.regional[ "de" ]);
		$('.birthday input.joInpDatepickerYM').unbind('change',checkAge);
		$('.birthday input.joInpDatepickerYM').bind('change',checkAge);

		$('.joKursGrouped .joSearch').unbind('keyup',sendSearch);
        $('.joKursGrouped .joSearch').bind('keyup',sendSearch);

        $('#step3form').submit(function( event ) {			
			if(sendFormStep3){
				event.preventDefault();
			}else{
				sendFormStep3 = true;
			}
			$('#popup-orderfinal').dialog('open');
			var oLayer = $(this).find('input[type="submit"]').data('layer');
			if(typeof(oLayer) != "undefined" && oLayer == "invoice"){
				initJoLightbox();
			}
		});

        $('#step4paymentsel').submit(function( event ) {
			var oLayer = $(this).find('select').val();
			if(typeof(oLayer) != "undefined" && oLayer == 6){
				initJoLightbox();
			}
		});

	    joToolTipInit();

	    function changeProfSelection(){
	    	var name = $(this).attr('name');
	    	name = name.replace('anmeldestatus', 'anmeldestatuschanged')
	    	$(this).attr('name',name);
	    	$(this).closest('form').submit();
	    }

	    $('select.prof-sel').bind('change',changeProfSelection);

	    /* Lightbox */
		initJoLightbox = function(){
			var lbOvLay = null;
			if($('.joLbOvLy').length>0){
				lbOvLay = $('.joLbOvLy');
			}else{
				lbOvLay = $('<div class="joLbOvLy"><div class="joLbOvWrap"><img title="Loading" alt="Loading" src="/typo3conf/ext/jo_kursanmeldung/Resources/Public/images/ajax-loader.gif" width="46" height="46"><div class="joLbOvCnt">' + lclng[slng]['wait'] + '</div></div></div>');
				lbOvLay.hide();
				$('body').append(lbOvLay);
			}
			lbOvLay.fadeIn();
		}
		
		function toggleAddText(){
			var _this = $(this);
			var _addText = $('.additional-text');
			if(_addText.length > 0){
				$.each(_addText, function(i,n){
					if(_this.val() == $(this).data('selval')){
						_addText.fadeIn();
					}else{
						_addText.fadeOut();
					}
				});
			}
		}
		
		function initPaymentAddText(){
			var _selPayment = $('select[data-trsrc="zahlart"]');
			_selPayment.unbind('change', toggleAddText);
			_selPayment.bind('change', toggleAddText);
		}
		initPaymentAddText();
		
		function openDialog(ev){
			ev.preventDefault();
			
			var btnGoOn = 'weiter';
			if(typeof(lclng[slng]['more']) != "undefined") btnClose = lclng[slng]['more'];
			if(typeof(dlgButtons) != "undefined" && typeof(dlgButtons['goon']) != "undefined"){
				btnGoOn = dlgButtons['goon'];
			}
			
			var btnClose = 'abbrechen';
			if(typeof(lclng[slng]['cancel']) != "undefined") btnClose = lclng[slng]['cancel'];
			if(typeof(dlgButtons) != "undefined" && typeof(dlgButtons['close']) != "undefined"){
				btnClose = dlgButtons['close'];
			}
			
			var _this = $(this);
			$( "#dialog-confirm" ).dialog({
				resizable: false,
				height: "auto",
				width: 400,
				modal: true,
				buttons: [
					{
						text: btnGoOn,
						click: function() {
							window.location.href = _this.attr('href');
							$( this ).dialog( "close" );
						}
					},
					{
						text:	btnClose,
						click:	function() {
							$( this ).dialog( "close" );
						}
					}
				]
			});
		}
		
		$('.joKursanmeldungKurs.jo-only-passive a').unbind('click',openDialog);
        $('.joKursanmeldungKurs.jo-only-passive a').bind('click',openDialog);
		var maxEnsembleMember = 3;
		
		function removeClone(){
			$(this).unbind('click',removeClone);
			$(this).closest('.aufklappen-entn').remove();
			if($('.aufklappen-enconf .aufklappen-entn').length >= maxEnsembleMember){
				$('#addmoreid .addmore').hide();
			}else{
				$('#addmoreid .addmore').show();
			}
			initDP();
		}
		function addClone(){
			// nur drei Ensemblemitglieder erwünscht
			if($('.aufklappen-enconf .aufklappen-entn').length < maxEnsembleMember){
				var clone = $("#addmoreid").clone().insertBefore("#addmoreid");
				clone.attr('id', 'addmoreid' + new Date().getTime() );
				clone.find('i').removeClass('fa-plus-square-o');
				clone.find('i').addClass('fa-minus-square-o');
				$("#addmoreid").find('input').val('');
				clone.find('i').bind('click',removeClone);
				initDP();
				
				if($('.aufklappen-enconf .aufklappen-entn').length >= maxEnsembleMember){
					$('#addmoreid .addmore').hide();
				}else{
					$('#addmoreid .addmore').show();
				}
			}
		}
		
		$('.aufklappen-entn .addmore .fa-minus-square-o').unbind('click',removeClone);
		$('.aufklappen-entn .addmore .fa-minus-square-o').bind('click',removeClone);
		
		$('#addmoreid .addmore').unbind('click',addClone);
		$('#addmoreid .addmore').bind('click',addClone);
		
		/* toggle Slider start */
		var slide = $('[data-trtype="slide"]');
		
		function checkSlideEl(el){
			$.each(el, function(){
				var _this = $(this);
				var type = _this.prop("nodeName");
				switch(type){
					case 'SPAN':
						break;
					default:
						if(_this.data('trsrc')){
							if(_this.is(':checked')){
								$(_this.data('trsrc')).show();
							}else{
								$(_this.data('trsrc')).hide();
							}
						}
				}
			});
		}
		
		function toggleSlider(){
			var _this = $(this);
			var target = _this.data('trsrc');
			var trsel = _this.data('trsel');
			var _target = $(target);
			if(trsel == 'closest'){
				_target = _this.closest('.closest').find(target);
			}
			var type = _this.prop("nodeName");
			switch(type){
				case 'SPAN':
					if(_target.is(':visible')){
						_target.slideUp();
					}else{
						_target.slideDown();
					}
					break;
				default:	
					if(_this.is(':checked')){
						_target.slideDown();
					}else{
						_target.slideUp();
					}
			}
		}
		
		slide.unbind('click',toggleSlider);
		slide.bind('click',toggleSlider);
		checkSlideEl(slide);
		/* toggle Slider end */
		
		/* show after load start */
		var sal = $('[data-action="show-after-load"]');
		sal.hide();
		sal.delay(1000).slideDown();
	
		/* show after load end */
		
		$('#popup-orderfinal').dialog({
			autoOpen: false,
			modal: true
		});
	});
 })(jQuery); 