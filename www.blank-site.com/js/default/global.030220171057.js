/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	function javascript globals to this site


*/

//----------------------------------------------------------------------------------------------

jQuery(document).ready(function($){
	//le menu pour responsive
	/*
	$('#menu-opener').click(function(e){
		e.preventDefault();
		if($('#menu-listing').showMenu()){
			$(this).css('opacity', '1.0');
		}else{
			$(this).css('opacity', '0.5');
			}
		});	
	*/		
	//selon la taille du menu responsive
	/*	
	if($('BODY').innerWidth() <= 640){
		//le animate du menu
		$('#menu-opener').animate( 
			{
				opacity: 0.5,
				left: 0,
				}, 
			{
				queue:false,
				duration:600, 
				complete:function(){
					
					}
				}	
			);	
		}
	*/
	//menu opener for mobile
	$('#top-menu-mobile').click(function(){
		var iOpened = parseInt($('#menu-mobile').attr('opened'));
		if(iOpened){	
			$('#menu-mobile').animate({
				height: '0px'
				},{
				queue:false,
				duration:250, 
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
				duration:250, 
				complete:function(){
					$(this).attr('opened', 1);
					}
				});
			
			}
		});
	//le arrow to the top
	$(window).scroll(function(){
		checkForScroll('scroll');
		});
	$(window).resize(function(){
		checkForScroll('resize');
		});	
	checkForScroll('resize');	
	});
	
//
function checkForScroll(strType){
	if(strType == 'resize'){
		//positionne dans le main container	
		$('#scroller-up').css({'right': parseInt(($(window).width() - $('.main-container').width())/2) + 10});		
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
			}
	}else{
		if($('#scroller-up').hasClass('showing')){	
			$('#scroller-up').removeClass('showing');			
			$('#scroller-up').unbind();
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
	
	
