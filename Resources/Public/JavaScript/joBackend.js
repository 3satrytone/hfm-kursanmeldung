;(function($){
    $.fn.joDialog = function(options){
        var defaults = {
            dialogName: 'joDialog',
            dialogSel: '#joDialog',
            dialog: null
        }
        var _this = this,
            $this = $(this);
        _this.opts = $.extend(defaults, options);
        
        _this.removeDialog = function(ev){
            if(ev != null && $(ev.target).closest(_this.opts.dialogSel).length>0 &&
               $(ev.target).is(_this.opts.dialogSel)) {
                $(_this.opts.dialogSel).remove();
            }
            if(ev == null){
                $(_this.opts.dialogSel).remove();
            }     
        }
        _this.buildDialog = function(){
            $bg = $('<div id="'+_this.opts.dialogName+'">');
            $content = $('<div class="'+_this.opts.dialogName+'-content">');
            $content.hide();
            $closeBtn = $('<div class="'+_this.opts.dialogName+'-close" title="schließen">');
            $iframe = $('<iframe id="'+_this.opts.dialogName+'-iframe" frameborder="0" border="0" cellspacing="0">');
            $iframe.attr('src', _this.getSrc());
            $('body').append($bg.append($content.append($closeBtn).append($iframe)));
            _this.opts.dialog = $(_this.opts.dialogSel);

            $('body').unbind('click',_this.removeDialog);
            $('body').bind('click',_this.removeDialog);

            $('.'+_this.opts.dialogName+'-close').unbind('click',function(){_this.removeDialog(null)});
            $('.'+_this.opts.dialogName+'-close').bind('click',function(){_this.removeDialog(null)});
        }
        _this.getSrc = function(){
            var attr = $this.attr('href');
            var src = null;
            if(typeof(attr) != 'undefined'){
                src = attr;
            }
            return src;
        }
        _this.showDialog = function(ev){
            ev.preventDefault();            
            _this.removeDialog(null);
            _this.buildDialog();
            _this.opts.dialog.show();
            $('.'+_this.opts.dialogName+'-content').delay(300).fadeIn(500);
        }
        _this.init = function(){            
            $this.unbind('click',_this.showDialog);
            $this.bind('click',_this.showDialog);
        }
        _this.init();
    }
})(jQuery);
(function($) {
    var updateFilter;
    $( document ).ready(function() {
        
        $('.copyLink').unbind("click", copyToClipboard);
        $('.copyLink').bind("click", copyToClipboard);

        $('.joKursGrouped .joCursor').unbind('click',toggleKuTnGrouped);
        $('.joKursGrouped .joCursor').bind('click',toggleKuTnGrouped);
     
        $('.joKursGrouped .f3-widget-paginator a').unbind('click',triggerPaginator);
        $('.joKursGrouped .f3-widget-paginator a').bind('click',triggerPaginator);

        $('.joKursGrouped .joSearch').unbind('keyup',sendSearch);
        $('.joKursGrouped .joSearch').bind('keyup',sendSearch);

        $('.joMailing .filter input:radio').unbind('change',function(){$("#searchInput").trigger('keyup');});
        $('.joMailing .filter input:radio').bind('change',function(){$("#searchInput").trigger('keyup');});

        $('.joMailing .filter select#anmeldestatus').unbind('change',function(){$("#searchInput").trigger('keyup');});
        $('.joMailing .filter select#anmeldestatus').bind('change',function(){$("#searchInput").trigger('keyup');});
        
        $('#selectAll').unbind('click',checkUser);
        $('#selectAll').bind('click',checkUser);

        var checkForUpdate = true;
        $( "#kursanmeldung" ).submit(function( event ) {
            if(checkForUpdate){
                event.preventDefault();
                var $form = $( this ),
                term = $form.serialize(),
                url = $form.attr( "action" )+'&tx_jokursanmeldung_web_jokursanmeldungjokursanmeldungbe%5Baction%5D=checkclass';

                // Send the data using post
                var posting = $.post(url,term,null,'json');

                // Put the results in a div
                posting.done(function( data ) {
                    if(data.check[0] == true || data.check == 'noupdate'){
                        $( "#kursanmeldung" ).submit();
                    }else{
                        Check = confirm("ACHTUNG Keine Plätze mehr frei!\naktive Teinahmer: "+data.check[1]+"\npassive Teinahmer: "+data.check[2]+"\nWollen Sie den Kurs dennoch ändern?");
                        if (Check == true) {
                          $( "#kursanmeldung" ).submit();
                        }else{
                            checkForUpdate = true;
                        }
                    }
                })            
                .fail(function() {
                    alert('Fehler beim XHR Post! Probieren Sie es erneut oder wenden Sie sich an den Support.');
                });
                checkForUpdate = false;
            }

        });
		
        $("#searchInput").keyup(function () {
            $('.joMailing .joScrollBox table input').removeAttr("checked");
            var name = $('.joMailing input[name="filter"]:checked').val();
            var bezahlt = $('.joMailing input[name="bezahlt"]:checked').val();
			var bezahltag = $('.joMailing input[name="bezahltag"]:checked').val();
            var status = $('.joMailing #anmeldestatus').val();
			var dv = $('.joMailing input[name="dv"]:checked').val();

            //split the current value of searchInput
            var data = this.value.split(" ");
            //create a jquery object of the rows
            var jo = $("#fbody").find("tr");
            if (this.value == "" && name == '' && bezahlt == '' && bezahltag == '' && status== '' && dv == '') {
                jo.show();
				jo.find('td').css({'color':'black'});
                return;
            }
            //hide all the rows
            jo.hide();
            //Recusively filter the jquery object to get results.
            jo.filter(function (i, v) {
				// 1,2,4 alle korrekt
				var bitStat = 7;
				var bitCheck = 0;
				var filterStat = false;
				var $t = $(this);
				var $td = $t.find('td');
				$td.css({'color':'black'});
                if(name != ''){
                    $t = $t.find('td.'+name);
                }
                for (var d = 0; d < data.length; ++d) {
					if(status>0){
                        if(!$(this).find('td.status').hasClass('status_'+status)){
                            return false;
                        }
                    }
                    if ($t.is(":contains('" + data[d] + "')")) {
						$.each($td, function(){
							if($(this).is(":contains('" + data[d] + "')"))$(this).css({'color':'red'});
						});						
						if(bezahlt != ''){
							if($(this).find('td.bezahlt').is(":contains('" + bezahlt + "')")){
								bitCheck += 1;
							}
                        }else{
							bitCheck += 1;
						}
						if(bezahltag != ''){
							if($(this).find('td.bezahltag').is(":contains('" + bezahltag + "')")){
								bitCheck += 2;
							}
						}else{
							bitCheck += 2;
						}
						if(dv != ''){
							if($(this).find('td.dv').is(":contains('" + dv + "')")){
								bitCheck += 4;
                            }
                        }else{
							bitCheck += 4;
						}
                    }
                }
				if(bitStat == bitCheck)filterStat = true;
                return filterStat;
            })
            //show the rows that match.
            .show();
			
        }).focus(function () {
            this.value = "";
            $(this).css({
                "color": "black"
            });
            $(this).unbind('focus');
        }).css({
            "color": "#C0C0C0"
        });

        $('#exportmodul').joDialog();


        if(typeof($('#optgroup').multiSelect)=='function'){
            var reorderTableFields = function (selObj,addsel){
                var addsel = addsel || true;
                var ulEl = $('#sortableTableFields');
                $.each(selObj,function(ind,sel){
                    vEl = $('#optgroup option[value="'+sel+'"]');
                    if(addsel && ulEl.find('li[data-fieldname="'+sel+'"]').length < 1){
                        li = ulEl.find('li[data-fieldname="default"]').clone();
                        li.attr('data-fieldname',sel);
                        li.find('span').text(vEl.text());
                        li.find('input').val(sel);
                        li.show();
                        ulEl.append(li);
                    }else{
                        ulEl.find('li[data-fieldname="'+sel+'"]').remove();
                    }
                });
            }
            var msTable = $('#optgroup').multiSelect({ 
                selectableOptgroup: true,
                selectableHeader: '<input type="text" class="search-input" autocomplete="off" placeholder="Suchwort">',
                selectionHeader: '<input type="text" class="search-input" autocomplete="off" placeholder="Suchwort">',
                afterInit: function(ms){
                    var that = this,
                        $selectableSearch = that.$selectableUl.prev(),
                        $selectionSearch = that.$selectionUl.prev(),
                        selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                        selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                    .on('keydown', function(e){
                      if (e.which === 40){
                        that.$selectableUl.focus();
                        return false;
                      }
                    });

                    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                    .on('keydown', function(e){
                      if (e.which == 40){
                        that.$selectionUl.focus();
                        return false;
                      }
                    });
                },
                afterSelect: function(value){
                    this.qs1.cache();
                    this.qs2.cache();
                    reorderTableFields(value);
                },
                afterDeselect: function(value){
                    this.qs1.cache();
                    this.qs2.cache();
                    reorderTableFields(value,false);
                }
            });
        }
        if(typeof($('#tabs').tabs)=='function'){
            $( "#tabs" ).tabs();
            if(typeof(exportListUid) == 'undefined'){
                $( "#tabs" ).tabs("disable",1);
            }
        }
        if(typeof($('.sortable').sortable)=='function'){
            $( ".sortable" ).sortable();
            $( ".sortable" ).disableSelection();
        }

        function setStatus(e){
            var _this = $(this);
            var ajaxUrl = TYPO3.settings.ajaxUrls['KursanmeldungTeilnehmerController::callAjax'];
            jQuery.ajax({
                url:       ajaxUrl, // If called in a Backend context, replace with "ajax.php".
                type:      'GET',
                dataType:  'html',
                data: {
                    itemuid: _this.data('uid'),
                    statuid: _this.val(),
                    action: 'ajaxupdate', 
                },
                success: function(result) {                    
                    $('a[data-uid="' + _this.data('uid') + '"].anmeldestatus').text(_this.find(':selected').text());
                    top.TYPO3.Notification.success('Aktualisiert.', 'Kursanmeldung wurde erfolgreich aktualisiert.');
                }
            }).fail(function() {
                top.TYPO3.Notification.info('ACHTUNG!', 'Änderung konnte nicht gespeichert werden.');
            }).always(function(result) {
                _this.closest('td').find('.anmeldestatus').show();
                _this.parent().remove();
            });
        }
        function rebuildStatus(){
            var _this = $(this);
            _this.closest('td').find('.anmeldestatus').show();
            _this.parent().remove();
        }
        function buildStatus(e){
            e.preventDefault();
            var _this = $(this);
            var _prnt = _this.parent();
            var href=_this.attr('href');
            if(_prnt.find('.selbox').length == 0){
                _this.hide();
                var selbox = $('<div>',{class: 'selbox'}).html($('.statusArr').html());
                selbox.find('select').data('uid',_this.data('uid'));
                selbox.find('select').find("option:contains('"+ _this.text() +"')").attr('selected', 'selected');
                selbox.find('select').unbind('change',setStatus);
                selbox.find('select').bind('change',setStatus);
                selbox.find('select').unbind( 'blur', rebuildStatus);
                selbox.find('select').bind( 'blur', rebuildStatus);
                _prnt.append(selbox);
            }
        }
        var editStatus = $('.anmeldestatus');
        editStatus.unbind( 'click', buildStatus);
        editStatus.bind( 'click', buildStatus);

        /* such formulare */
        function clearSearch(e){
            var _this = $(this);
            var inpSearch = _this.parent().find('input[type="text"]');
            setClickPos();  
            inpSearch.val('');
            var e = $.Event('keyup');
            e.keyCode = 13;
            inpSearch.trigger(e);
        }
        $('.joKursGrouped button.clear').unbind('click',clearSearch);
        $('.joKursGrouped button.clear').bind('click',clearSearch);

        function clearSearchAll(e){
            setClickPos();
            e.preventDefault();
            var _this = $(this);
            var inpSearch = $('input.joSearch');
            inpSearch.val('');
            var e = $.Event('keyup');
            e.keyCode = 13;
            inpSearch.trigger(e);
        }
        $('div.clearAllSearchFields').unbind('click',clearSearchAll);
        $('div.clearAllSearchFields').bind('click',clearSearchAll);

        /* datepicker */
		if(typeof $.datepicker != "undefined"){
			var dp = $( 'input[data-widget="datepicker"]' ).datepicker({format: 'dd.mm.yyyy'}).on('changeDate', function(e){ $(this).val($(this).val() + ' 00:00' );});
		}
        /* init Page function 
         * like open Panels and ScrollPos 
         */
        
        // openPanels if set
        loadOpenPanels();

        headerSorting();

        // Scrollpos
        setSessionScrollPos();
        if($('.tx_jokursanmeldung_anmeldungen').length > 0){
            $('a').bind('click', setClickPos);
        }

        // filteroptions
        var moreFilter = $('.joMoreFilter');
        updateFilter = $('.joFilterUpdate');
        checkClickOutside = function(e){
            if(($(e.target) != moreFilter && $(e.target).closest('.joMoreFilter').length == 0) && $(e.target).closest('.joFilterOptions').length == 0){
                moreFilter.removeClass('active');
                $('.joFilterOptions').hide();
            }
        }
        joMoreFilter = function(){
            var _this = $(this);
            _this.toggleClass('active');
            _this.parent().find('.joFilterOptions').toggle();
            if(_this.hasClass('active')){
                _this.parent().find('.joFilterUpdate').show();
            }else{
                _this.parent().find('.joFilterUpdate').hide();
            }
        }
        submitTnForm = function(){
            setClickPos();
            $('#listTeilnehmer').submit();
        }
        $('body').unbind('click',checkClickOutside);
        $('body').bind('click',checkClickOutside);

        moreFilter.unbind('click',joMoreFilter);
        moreFilter.bind('click',joMoreFilter);

        updateFilter.unbind('click',submitTnForm);
        updateFilter.bind('click',submitTnForm);
		
		changeMailType = function(){
			var joPagesInvoice = $('.joPagesInvoice');
			if($(this).val() == 'ZulassungR'){
				joPagesInvoice.slideDown();
			}else{
				if(joPagesInvoice.is(":visible")){
					joPagesInvoice.slideUp();
				}
			}
		}
		
		$('.joTyp select').unbind('change',changeMailType);
        $('.joTyp select').bind('change',changeMailType);
			
		$('button.copyButton').click(function(ev){
			ev.preventDefault();
			$(this).siblings('input.linkToCopy').select();      
			document.execCommand("copy");
		});
		
		function addFile(ev){
			var _this = $(this);
			if(typeof(_this.data('target')) != "undefined"){
				$(_this.data('target')).fadeIn();
				if(_this.data('target') == '.ensemble #addmoreid'){
					_this.parent().find('input[type="hidden"]').val(0);
				}
			}else{
				$('#add-new-download .downloadtmpl').fadeIn();
			}
			_this.hide();
		}
		var plusfile = $('.fa-plus-square-o');
		plusfile.unbind('click', addFile);
		plusfile.bind('click', addFile);
		
		function toggleFile(ev){
			var _this = $(this);
			var _inp = _this.parent().find('input[type="hidden"]');
			if(_inp.length > 0){
				if(_this.hasClass('delete')){
					_inp.val(0);
				}else{
					_inp.val(1);
				}
			}
			_this.toggleClass('delete');
		}
		var minusfile = $('.joDownloads .fa-minus-circle');
		minusfile.unbind('click', toggleFile);
		minusfile.bind('click', toggleFile);
		
		function deleteEnsembleFn(){
			var _this = $(this);
			var _inp = _this.parent().find('input[type="hidden"].del-hidden');
			if(_inp.length > 0){
				if(_this.hasClass('delete')){
					_inp.val(0);
				}else{
					_inp.val(1);
				}
			}
			_this.toggleClass('delete');
		}
		var deleteEnsemble = $('.deleteEnsemble .fa-minus-square-o');
		deleteEnsemble.unbind('click', deleteEnsembleFn);
		deleteEnsemble.bind('click', deleteEnsembleFn);
		
		function toggleMember(){
			if($(this).is(':checked')){
				$('.ensemble-menber').slideDown();
			}else{
				$('.ensemble-menber').slideUp();
			}
		}
		
		$('.ensemble .enfirstn input[type="checkbox"]').unbind('change', toggleMember);
		$('.ensemble .enfirstn input[type="checkbox"]').bind('change', toggleMember);
    });

    var openPanel = [];

    function sortByHeader(){
        var sort = $(this).data('sort');
        var field = $(this).closest('th').data('field');
        var table = $(this).closest('table').data('table');
        var href = window.location.href;
        var urlArr = [];
        $.each(href.split('&'),function(c,q){
            var i = q.split('=');
            if(i[0].toString().indexOf(table)<0){
                urlArr.push(i[0].toString()+'='+i[1].toString());
            }
        });
        url = urlArr.join('&');
        sendStr = table + '=' + field + ' ' + sort;
        window.location.href = url +'&' +sendStr;
    }
    function headerSorting(){
        $('.sorting-evt').unbind('click', sortByHeader);
        $('.sorting-evt').bind('click', sortByHeader);
    }

    function checkUser(){
        $('.joMailing .joScrollBox table td input').removeAttr("checked");
        if($(this).is(':checked')){
            $('.joMailing .joScrollBox table tr:visible input').prop( "checked", true );
        }
    }

    function sendSearch(ev){
        if(ev.keyCode == 13 || ev.keycode == 13){
            $('#listTeilnehmer').submit();
        }else{
            $(this).parent().find('.joFilterUpdate').show();
        }
    }

    function triggerPaginator(ev){
        ev.preventDefault();
        var sendStr = $.param($('input.paginateHide').serializeArray());
    
        var href = $(this).attr('href');
        var urlArr = [];
        $.each(href.split('&'),function(c,q){
            var i = q.split('=');
            if(i[0].toString().indexOf('paginateHide')<0){
                urlArr.push(i[0].toString()+'='+i[1].toString());
            }
        });
        url = urlArr.join('&');
        window.location.href = url +'&' +sendStr;
    }

    function setSessionScrollPos(){
        if($('#listTeilnehmer').length>0){
            var pos = parseInt(window.sessionStorage.getItem('scrollTop'));
            $('#typo3-docbody').scrollTop(pos);
        }
    }
    function setClickPos(){
        window.sessionStorage.setItem('scrollTop',$('#typo3-docbody').scrollTop());
    }

    function loadOpenPanels(){
        if(window.sessionStorage.getItem('openPanel') != null && window.sessionStorage.getItem('openPanel').length > 0){
            var actPanels = window.sessionStorage.getItem('openPanel').split(',');
            $.each(actPanels, function(i,n){
                $('input[data-uid=' + n + ']').parent().removeClass('joHide');
            });
        }
    }

    function setOpenPanels(){
        openPanel = [];
        $.each($('.paginateHide[value=1]'),function(i,n){
            openPanel.push($(this).data('uid'));
        });
        window.sessionStorage.setItem('openPanel',openPanel)
    }

    function toggleKuTnGrouped(ev){
        var tgEl = $(this).parent().next('.joToggle');
        tgEl.toggleClass('joHide');
        var hiEl = tgEl.find('input[type="hidden"]');
        if(tgEl.hasClass('joHide')){
            hiEl.val('');
        }else{
            hiEl.val(1);
        }
        setOpenPanels();
    }

    function copyToClipboard(event) {
        event.preventDefault();
        elem = $(this);
          // create hidden text element, if it doesn't already exist
        var targetId = "_hiddenCopyText_";
        
        // must use a temporary form element for the selection and copy
        target = document.getElementById(targetId);
        if (!target) {
            var target = document.createElement("textarea");
            target.style.position = "absolute";
            target.style.left = "-9999px";
            target.style.top = "0";
            target.id = targetId;
            document.body.appendChild(target);
        }
        target.textContent = elem.attr('href');

        // select the content
        var currentFocus = document.activeElement;
        target.focus();
        target.setSelectionRange(0, target.value.length);
        
        // copy the selection
        var succeed;
        try {
              succeed = document.execCommand("copy");
        } catch(e) {
            succeed = false;
        }
        // restore original focus
        if (currentFocus && typeof currentFocus.focus === "function") {
            currentFocus.focus();
        }
        target.textContent = "";
        return succeed;
    }
	
})(jQuery);