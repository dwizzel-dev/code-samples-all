<?php
/**
@auth:	Dwizzel
@date:	00-00-0000
@info:	append model

*/

$arrOutput['append']['debug'] = '';

if(SHOW_DEBUG){
	$arrOutput['append']['debug'] .= '<pre class="phperr"><b>PHPERRORS</b><code>'.recursiveShow($oReg->get('phperr')->getAll(), '<br>', '').'</code></pre>';
	$arrOutput['append']['debug'] .= '<pre><div class="opener"><b>MEMORY</b></div><code><br />'.format2Dec(((memory_get_peak_usage()/1024)/1000)).' Mo'.'</code></pre>';
	$arrOutput['append']['debug'] .= '<pre><div class="opener"><b>OUTPUT</b></div><code>'.recursiveShow($arrOutput, '<br>', '').'</code></pre>';
	$arrOutput['append']['debug'] .= '<pre><div class="opener"><b>REQUEST</b></div><code>'.recursiveShow($oReg->get('req')->getVars(), '<br>', '').'</code></pre>';
	$arrOutput['append']['debug'] .= '<pre><div class="opener"><b>GLOBALS</b></div><code>'.recursiveShow($oGlob->getVars(), '<br>', '').'</code></pre>';
	$arrOutput['append']['debug'] .= '<pre><div class="opener"><b>QUERIES</b></div><code>'.recursiveShow($oReg->get('db')->getQueries(), '<br>', '').'</code></pre>';
	$arrOutput['append']['debug'] .= '<pre><div class="opener"><b>SESSIONS</b></div><code>'.recursiveShow($oReg->get('sess')->getVars(), '<br>', '').'</code></pre>';
	if(isset($_COOKIE)){
		$arrOutput['append']['debug'] .= '<pre><div class="opener"><b>COOKIES</b></div><code>'.recursiveShow($_COOKIE, '<br>', '').'</code></pre>';
		}
	}

//END