<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	default controller for logout
@note:	cette page est une redirection vers home une fois deconnecté
		pas besoin des array de base pour la view et autres
*/

//on logout et redirige vers home
$oReg->get('login')->doLogout();
//redirect
Header('Location: '.$oGlob->getArray('links','home'));
exit();




//END





