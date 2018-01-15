<?php
/**
@auth:	Dwizzel
@date:	07-06-2012
@info:	include in between <head>  there  </head>.

*/

?>
<!-- TITLE -->
<title><?php echo ucfirst($arrOutput['meta']['title']).' | '.META_TITLE;?></title>

<!-- START META -->

<!-- CONTENT -->
<meta http-equiv="content-language" content="<?php echo $arrOutput['meta']['lang']?>" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Security-Policy" content="default-src * 'unsafe-inline' 'unsafe-eval';">

<!-- META -->
<meta name="robots" content="index, follow, all" />
<meta name="language" content="<?php echo $arrOutput['meta']['lang']?>">
<meta name="copyright" content="<?php echo htmlSafeTag(META_COPYRIGHT);?>">
<meta name="distribution" content="global">
<meta name="googlebot" content="noodp">
<meta name="rating" content="general">
<meta name="title" content="<?php echo htmlSafeTag($arrOutput['meta']['title'].' | '.META_TITLE);?>" />
<meta name="description" content="<?php echo htmlSafeTag($arrOutput['meta']['description']);?>" />
<meta name="keywords" content="<?php echo htmlSafeTag($arrOutput['meta']['keywords']);?>" />
<meta name="author" content="<?php echo htmlSafeTag(META_CREATOR); ?>" />

<!-- OPEN GRAPH -->
<meta property="og:type" content="article">
<meta property="og:locale" content="<?php echo $arrOutput['meta']['lang']?>">
<meta property="og:url" content="<?php echo htmlSafeTag($arrOutput['meta']['canonical']);?>">
<meta property="og:title" content="<?php echo htmlSafeTag($arrOutput['meta']['title'].' | '.META_TITLE);?>">
<meta property="og:description" content="<?php echo htmlSafeTag($arrOutput['meta']['description']);?>">
<?php
if(isset($arrOutput['meta']['image'])){
	echo '<meta property="og:image" content="'.htmlSafeTag($arrOutput['meta']['image']).'">'.EOL;	
	}
?>

<!-- TWITTER -->
<meta property="twitter:title" content="<?php echo htmlSafeTag($arrOutput['meta']['title'].' | '.META_TITLE);?>">
<meta property="twitter:description" content="<?php echo htmlSafeTag($arrOutput['meta']['description']);?>">
<?php
if(isset($arrOutput['meta']['image'])){
	echo '<meta property="twitter:image" content="'.htmlSafeTag($arrOutput['meta']['image']).'">'.EOL;	
	}
?>

<!-- OTHERS -->
<link rel="copyright" href="">
<?php
if(isset($arrOutput['meta']['canonical'])){
	echo '<link rel="canonical" href="'.$arrOutput['meta']['canonical'].'">'.EOL;	
	}
?>

<!-- GEO -->
<meta name="geo.region" content="CA-QC" />
<meta name="geo.placename" content="Montreal" />
<meta name="geo.position" content="45.5088889;-73.5541667" />
<meta name="ICBM" content="45.5088889, -73.5541667" />

<!-- DC -->
<meta name="DC.Title" content="<?php echo htmlSafeTag($arrOutput['meta']['title'].' | '.META_TITLE);?>" />
<meta name="DC.Subject" content="<?php echo htmlSafeTag($arrOutput['meta']['title'].' | '.META_TITLE);?>" />
<meta name="DC.Description" content="<?php echo htmlSafeTag($arrOutput['meta']['description']);?>" />
<meta name="DC.Creator" content="<?php echo htmlSafeTag(META_CREATOR); ?>" />
<meta name="DC.Date" content="<?php echo htmlSafeTag(META_DATE); ?>" />
<meta name="DC.Type" content="text" />
<meta name="DC.Format" content="text/html" />

<!-- TYPE -->
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="identifier-url" content="<?php echo htmlSafeTag(META_URL_IDENTIFIER); ?>" />
<?php
if(isset($arrOutput['meta']['alternate']) && is_array($arrOutput['meta']['alternate'])){
	echo '<!-- ALT LANG -->'.EOL;
	foreach($arrOutput['meta']['alternate'] as $k=>$v){	
		echo '<link rel="alternate" hreflang="'.$k.'" href="'.$v.'" />'.EOL;
		}
	}
?>

<!-- END META -->

