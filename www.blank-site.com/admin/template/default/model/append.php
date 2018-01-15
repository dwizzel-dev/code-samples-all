<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	append model

*/

$arrOutput['append']['debug'] = '';

if(SHOW_DEBUG){
	$arrOutput['append']['debug'] .= '<pre class="phperr"><b>PHPERRORS</b><code>'.recursiveShow($oReg->get('phperr')->getAll(), '<br>', '').'</code></pre>';
	$arrOutput['append']['debug'] .= '<pre><b>OUTPUT</b><code>'.recursiveShow($arrOutput, '<br>', '').'</code></pre>';
	$arrOutput['append']['debug'] .= '<pre><b>REQUEST</b><code>'.recursiveShow($oReg->get('req')->getVars(), '<br>', '').'</code></pre>';
	$arrOutput['append']['debug'] .= '<pre><b>GLOBALS</b><code>'.recursiveShow($oGlob->getVars(), '<br>', '').'</code></pre>';
	$arrOutput['append']['debug'] .= '<pre><b>QUERIES</b><code>'.recursiveShow($oReg->get('db')->getQueries(), '<br>', '').'</code></pre>';
	$arrOutput['append']['debug'] .= '<pre><b>SESSIONS</b><code>'.recursiveShow($oReg->get('sess')->getVars(), '<br>', '').'</code></pre>';
	if(isset($_COOKIE)){
		$arrOutput['append']['debug'] .= '<pre><b>COOKIES</b><code>'.recursiveShow($_COOKIE, '<br>', '').'</code></pre>';
		}
	}

//END