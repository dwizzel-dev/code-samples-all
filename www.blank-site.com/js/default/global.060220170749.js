/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	function javascript globals to this site


*/

//----------------------------------------------------------------------------------------------

jQuery(document).ready(function($){
	//menu opener for mobile
	$('#top-menu-mobile').click(function(e){
		openMenuMobile(false);
		});
	//le arrow to the top
	$(window).scroll(function(){
		checkForScroll('scroll');
		});
	$(window).resize(function(){
		checkForScroll('resize');
		});	
	//check en partant si est deja bas	
	checkForScroll('resize');
	//le open video sur click
	$('.video-mask').click(function(e){
		var url = $(this).attr('id');
		$(this).html('<iframe width="100%" height="100%" src="' + url + '?autoplay=1&rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>');
		});
	//le carousseel des exercices
	if($('.carousel').length != 0){
		initCarousel();
		}
	});

	
function initCarousel(){
	//on va chercker la largeur total du caroussel
	var iCarouselCount = 0;
	//on check tout les carousel et on leur donne un id different
	$('.carousel').each(function(){
		iCarouselCount++;
		//le count du panel
		$(this).attr('carousel-num', iCarouselCount);
		//le count de block dans chaque carousel
		var iCount = 0;
		var iPanelWidth = 0;
		var iWidth = 0;
		$(this).find('.container .block').each(function(){
			//la largeure totale
			iPanelWidth = $(this).width();
			iWidth += $(this).width();
			//le count du panel
			$(this).attr('panel-num', iCount++);
			});
		//masked	
		$(this).find('.container .block[panel-num="0"] .video-mask').addClass('focused');	
		//largeur totale
		$(this).find('.container').css({width:iWidth + 'px'});
		//les vars a garder
		$(this).attr('panel-current', 0);
		$(this).attr('panel-total', iCount);
		$(this).attr('panel-width',  iPanelWidth);	
		//le height du carousel pour les fleche
		var iHeight = parseInt((parseInt($(this).find('.container .block .img').height()) - parseInt($(this).find('.cleft-arrow > IMG').height())))/2;
		$(this).find('.cleft-arrow, .cright-arrow').css({'padding-top':iHeight + 'px'});
		//on set le parent
		$(this).find('.cleft-arrow, .cright-arrow').data('parent', $(this));
		//la fleche gauche
		$(this).find('.cleft-arrow').click(function(e){
			//le parent	
			var parent = $(this).data('parent');
			//interval si le aotuchange est parti
			clearInterval(parent.data('autotimer'));
			//move
			parent.trigger('changePanel', ['left']);
			});
		//la fleche droite	
		$(this).find('.cright-arrow').click(function(e){
			//le parent
			var parent = $(this).data('parent');
			//interval si le aotuchange est parti
			clearInterval(parent.data('autotimer'));
			//move
			parent.trigger('changePanel', ['right']);
			});
		//sur le mouse over on trig un auto avance	
		$(this).find('.cright-arrow').hover(
			function(e){
				var parent = $(this).data('parent');
				parent.trigger('autoavance', [true, 'right']);
				},
			function(e){
				var parent = $(this).data('parent');
				parent.trigger('autoavance', [false, 'right']);
				}
			);
		$(this).find('.cleft-arrow').hover(
			function(e){
				var parent = $(this).data('parent');
				parent.trigger('autoavance', [true, 'left']);
				},
			function(e){
				var parent = $(this).data('parent');
				parent.trigger('autoavance', [false, 'left']);
				}
			);	
		//un auto avance
		$(this).on('autoavance', function(obj, active, direction){
			//on set le timer
			if(active){
				$(this).data('autotimer', setInterval($(this).trigger.bind($(this), 'changePanel', [direction]), 900));	
			}else{
				clearInterval($(this).data('autotimer'));
				}
			});
		//change automatiuement de panel
		$(this).on('changePanel', function(obj, direction){
			var iNewPanel = parseInt($(this).attr('panel-current'));
			if(direction == 'left'){
				iNewPanel--;
			}else if(direction == 'right'){
				iNewPanel++;
				}
			var iPanelWidth = parseInt($(this).attr('panel-width'));	
			var iPanelTotal = parseInt($(this).attr('panel-total'));
			//les width de base
			var iContainer = parseInt($(this).find('.container').width());
			var iInnerContainer = parseInt($(this).find('.innered').width());
			//check si dernier panel
			if(iNewPanel >= iPanelTotal){
				//on arrete le timer
				clearInterval($(this).data('autotimer'));
				//on replace
				iNewPanel -= 1;
				}
			if(iNewPanel < 0){
				//on arrete le timer
				clearInterval($(this).data('autotimer'));
				//on replace
				iNewPanel = 0;
				}	
			//on va centrer le pannel
			var iNewPos = (iNewPanel * iPanelWidth) - ((iInnerContainer - iPanelWidth)/2);
			// on check si va depasser sinon on le place au dernier uniquement
			if(iNewPos < 0){
				iNewPos	= 0;
				}
			if(iNewPos > (iContainer - iInnerContainer)){
				iNewPos	= (iContainer - iInnerContainer) ;
				}
			//le focus block bg
			$(this).find('.container .block .video-mask').removeClass('focused');
			$(this).find('.container .block[panel-num="' + iNewPanel + '"] .video-mask').addClass('focused');		
			//anime le tout
			$(this).attr('panel-current', iNewPanel);
			$(this).find('.container').animate({
				right: iNewPos + 'px'
				},{
				queue:false,
				duration:600,
				});	
			});
		});
	//sur le resize du caontainer on doit replacer les trucs
	$(window).resize(function(){
		$('.carousel').trigger('changePanel', ['rescale']);
		});		
			
	}
	
	
function openMenuMobile(bFromBottom){
	var iOpened = parseInt($('#menu-mobile').attr('opened'));
	if(bFromBottom && iOpened){
		iOpened = 0;
		}
	if(iOpened){	
		$('#menu-mobile').animate({
			height: '0px'
			},{
			queue:false,
			duration:500, 
			complete:function(){
				$(this).attr('opened', 0);
				$(this).css({'display':'none'});
				}
			});
	}else{
		$('#menu-mobile').css({'display':'block'});
		$('#menu-mobile').animate({
			height: $('#menu-mobile .inner-menu').height() + 'px'
			},{
			queue:false,
			duration:500, 
			complete:function(){
				$(this).attr('opened', 1);
				}
			});
		
		}
		
	}
	
//
function checkForScroll(strType){
	if(strType == 'resize'){
		//positionne dans le main container	
		$('#scroller-up').css({'right': parseInt(($(window).width() - $('.main-container').width())/2) + 10});		
		$('#menu-up').css({'right': parseInt(($(window).width() - $('.main-container').width())/2) + 10 + $('#scroller-up').width()});		
		}
	if($(window).scrollTop() > $('#header').height()){
		if(!$('#scroller-up').hasClass('showing')){	
			//positionne dans le main container	
			$('#scroller-up').css({
				'right': parseInt(($(window).width() - $('.main-container').width())/2) + 20
				});		
			$('#scroller-up').addClass('showing');
			$('#scroller-up').click(function(){
				$(window).scrollTop(0);	
				});	
			//positionne dans le main container	
			$('#menu-up').css({
				'right': parseInt(($(window).width() - $('.main-container').width())/2) + 20 + $('#scroller-up').width()
				});		
			$('#menu-up').addClass('showing');
			$('#menu-up').click(function(){
				openMenuMobile(true);
				$(window).scrollTop(0);	
				});		
			}
	}else{
		if($('#scroller-up').hasClass('showing')){	
			//scoller
			$('#scroller-up').removeClass('showing');			
			$('#scroller-up').unbind();
			//menu
			$('#menu-up').removeClass('showing');			
			$('#menu-up').unbind();
			}
		}
	};
	
function debug(str){
	console.log(str);
	}
	
	
/*	
$.fn.showMenu = function(){
    if($(this).css('display') == 'none'){
		$(this).css('display', 'block');
		return true;
	}else{
		$(this).css('display', 'none');	
		return false;	
		}
	};
*/
	
//----------------------------------------------------------------------------------------------	
	
function formatJavascript(str){
	return String(str).replace(/"/g, '&quot;');
	}	

//----------------------------------------------------------------------------------------------
	
function createCookie(name, value, days) {
    var expires;
    if(days){
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = '; expires=' + date.toGMTString();
    }else{
        expires = '';
		}
    //document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
    document.cookie = encodeURIComponent(name) + '=' + value + expires + '; path=/';
	};

//----------------------------------------------------------------------------------------------
	
function readCookie(name) {
    var nameEQ = encodeURIComponent(name) + '=';
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++){
        var c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0){
			//return decodeURIComponent(c.substring(nameEQ.length, c.length));
			return c.substring(nameEQ.length, c.length);
			}
		}
	return null;
	};

//----------------------------------------------------------------------------------------------	

function eraseCookie(name) {
    createCookie(name, '', -1);
	};

//---------------------------------------------------------------------

function DispatchButtAction(el, type){
	//el = element clique
	//type = type de de model de page or view mvc 
	//	
	switch(el[0].id){
		case 'butt-print-exercise':
			printPage();
			break;
		case 'butt-save-exercise':
			//on change le butt
			saveExercise();
			break;
		case 'butt-share-exercise':
			sharePage();
			break;
		case 'butt-email-exercise':
			emailPage();
			break;
		default:
			break;
		}
	};
	

//---------------------------------------------------------------------	

function checkSavedExercise(){
	var cookPrefix = 'ex.';	
	//on va chercher le exrecise id dela page
	var exId = $('#form-exercise-settings input[name="id"]').val();	
	//le form et les infos
	var arrInfos = false;
	//check si avait autres exercice avant 
	var strCookie = readCookie(cookPrefix + exId); 	
	//check si a des infos
	if(typeof(strCookie) == 'string' && strCookie != ''){
		try{
			arrInfos = JSON.parse(strCookie);
		}catch(err){
			debug(err);
			}
		}
	//on fill le form
	if(typeof(arrInfos) == 'object'){
		for(var o in arrInfos){
			$('#form-exercise-settings input[name="' + o + '"]').val(decodeURIComponent(arrInfos[o]));
			}
		}
	
	};

//---------------------------------------------------------------------	
	
function saveExercise(){
	var cookPrefix = 'ex.';	
	//on va chercher le exrecise id dela page
	var exId = $('#form-exercise-settings input[name="id"]').val();			
	//object en string open 
	var strObj = '';
	//on va chercher les nouvelles infos
	$('#form-exercise-settings input[type=hidden]').each(function(){
		var val = $(this)[0].value;
		strObj += '"' + $(this)[0].name + '":"' + encodeURIComponent(val) + '",';
		});
	$('#form-exercise-settings input[type=text]').each(function(){
		var val = $(this)[0].value;
		val = val.substr(0,20);
		strObj += '"' + $(this)[0].name + '":"' + encodeURIComponent(val) + '",';
		});	
	//close obj
	if(strObj != ''){
		strObj = strObj.substr(0, (strObj.length - 1));
		}
	//save the index
	saveExerciseIndex(exId);		
	//we will save the exercise in a cookie
	createCookie(cookPrefix + exId, '{' + strObj + '}', 365);
	};


//---------------------------------------------------------------------	
	
function saveExerciseIndex(exId){
	//on va chercher le precedent index 
	//et on garde seulement les 10 derniers exercices
	var cookPrefix = 'ex.';	
	var iNumKeepEx = 9;	
	//le form et les infos
	var arrIndex = false;
	//check si avait autres exercice avant 
	var strCookie = readCookie(cookPrefix); 	
	//check si a des infos
	if(typeof(strCookie) == 'string' && strCookie != ''){
		try{
			arrIndex = JSON.parse(strCookie);
		}catch(err){
			debug(err);
			}
		}
	//
	var strObj = '';
	//on fill avec les precedent
	if(typeof(arrIndex) == 'object'){
		var arrKeepEx = [];
		var arrRemoveEx = [];
		//on garde seulement les dernier
		var iCmptEx = Object.keys(arrIndex).length;
		for(var o in arrIndex){
			if((iCmptEx - iNumKeepEx) > 0){
				arrRemoveEx.push(arrIndex[o]);
			}else{
				if(o != '_' + exId){
					arrKeepEx.push(o);
					}
				}
			iCmptEx--;		
			}
		//	
		//on delete les autres
		if(arrRemoveEx.length > 0){
			for(var o in arrRemoveEx){
				eraseCookie(arrRemoveEx[o]);
				}
			}
		//on rajoute le nouvel index juste ceux que l'on garde selon le max
		for(var i=0;i<arrKeepEx.length;i++){	
			strObj += '"' + arrKeepEx[i] + '":"' + arrIndex[arrKeepEx[i]] + '",';
			}
		}
	//le nouveau	
	strObj += '"_' + exId + '":"' + cookPrefix + exId + '"';	
	//on le remet dans le cookie
	createCookie(cookPrefix, '{' + strObj + '}', 365);
	};


//---------------------------------------------------------------------	
	
function checkSavedExercises(){
	//on va chercher le precedent index 
	var cookPrefix = 'ex.';	
	//le form et les infos
	var arrIndex = false;
	//check si avait autres exercice avant 
	var strCookie = readCookie(cookPrefix); 	
	//check si a des infos
	if(typeof(strCookie) == 'string' && strCookie != ''){
		try{
			arrIndex = JSON.parse(strCookie);
		}catch(err){
			debug(err);
			}
		}
	//on fill avec les precedent
	var arrAllEx = [];	
	if(typeof(arrIndex) == 'object'){
		//on rajoute le nouvel index
		for(var o in arrIndex){
			//on val lire le cookie
			var strExCockie = readCookie(arrIndex[o]);
			var arrEx = false;
			//check si a des infos
			if(typeof(strExCockie) == 'string' && strExCockie != ''){
				try{
					arrEx = JSON.parse(strExCockie);
					if(typeof(arrEx) == 'object'){
						arrAllEx[o] = arrEx;	
						}
				}catch(err){
					debug(err);
					}
				}
			}
		}
	//si il y a alors on affiche
	showSavedExercises(arrAllEx);
	};


//---------------------------------------------------------------------	
	
function showSavedExercises(arrEx){
	//le container
	var oContainer = $('.saved-exercises-listing');	
	//minor check	
	if(oContainer.length){
		var strOutput = '';
		var arrCbIds = [];
		for(var o in arrEx){
			var cbId = 'cb' + arrEx[o].id;
			arrCbIds.push(cbId);
			//ordre inverse
			strOutput = '<li><input type="checkbox" id="' + cbId + '" value="' + arrEx[o].id + '"><a href="' + decodeURIComponent(arrEx[o].li) + '">' + decodeURIComponent(arrEx[o].na) + '</a><br /><span class="date">' + showDate(arrEx[o].ti) + '</span></li>' + strOutput;	
			}
		strOutput = '<ul>' + strOutput + '</ul>';
		//le bouton delete
		if(arrCbIds.length){
			strOutput += '<button id="butt-delete-exercise" class="btn blue">delete</button>';
			}
		oContainer.html(strOutput);
		//action butt
		if(arrCbIds.length){
			$('#butt-delete-exercise').data('ids', arrCbIds);	
			$('#butt-delete-exercise').click(function(e){
				e.preventDefault();	
				var arrIds = $(this).data('ids');	
				var arr = [];
				//check si son coche
				for(var o in arrIds){
					if($('#' + arrIds[o]).prop('checked')){
						arr.push($('#' + arrIds[o]).val());
						}
					}
				if(arr.length){
					deleteExercises(arr);
					}
				});
			}
		}
	};


//---------------------------------------------------------------------	
	
function deleteExercises(arrEx){
	var cookPrefix = 'ex.';	
	var arrExIds = [];
	for(var o in arrEx){
		eraseCookie(cookPrefix + arrEx[o]);
		arrExIds['_' + arrEx[o]] = '';
		}
	//on enleve de l'index
	var arrIndex = false;
	//check si avait autres exercice avant 
	var strCookie = readCookie(cookPrefix); 	
	//check si a des infos
	if(typeof(strCookie) == 'string' && strCookie != ''){
		try{
			arrIndex = JSON.parse(strCookie);
		}catch(err){
			debug(err);
			}
		}
	//
	var strObj = '';
	//on fill avec les precedent
	if(typeof(arrIndex) == 'object'){
		//on rajoute le nouvel index
		for(var o in arrIndex){
			if(typeof(arrExIds[o]) != 'string'){
				strObj += '"' + o + '":"' + arrIndex[o] + '",';
				}
			}
		}
	//close obj
	if(strObj != ''){
		strObj = strObj.substr(0, (strObj.length - 1));
		}
	//on le remet dans le cookie
	createCookie(cookPrefix, '{' + strObj + '}', 365);
	//on refresh le listing
	checkSavedExercises();	
	}


//---------------------------------------------------------------------
	
function showDate(timestamp){
	var sep = '-';	
	var d = new Date(parseInt(timestamp) * 1000);
	var day = d.getDate();
	var month = d.getMonth();
	var year = d.getFullYear();
	var hours = d.getHours();
	var minutes = d.getMinutes();
	//adjust
	month += 1;
	hours += 1;
	//formatage	
	if(day.toString().length == 1){
		day = '0' + day;
		}
	if(month.toString().length == 1){
		month = '0' + month;
		}	
	if(hours.toString().length == 1){
		hours = '0' + hours;
		}
	if(minutes.toString().length == 1){
		minutes = '0' + minutes;
		}	
	//output
	return day + sep + month + sep + year + ' ' + hours + ':' + minutes;		
	}


//---------------------------------------------------------------------	
	
function printPage(){
	window.print();	
	};
	

//---------------------------------------------------------------------	

function sharePage(){
	};
	

//---------------------------------------------------------------------	

function emailPage(){
	};	
	
	
