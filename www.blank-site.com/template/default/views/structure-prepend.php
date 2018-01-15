<?php
/**
@auth:	Dwizzel
@date:	XX-XX-XXXX
@info:	structure data for google START

*/

if(isset($arrOutput['structure'])){
	if(is_array($arrOutput['structure'])){
		?>
		<!-- start structured data organisation -->
		<span itemscope itemtype="http://schema.org/Organization" itemprop="organization">	
			<meta itemprop="url" content="<?php echo htmlSafeTag($arrOutput['structure']['organisation']['url']); ?>">
			<meta itemprop="email" content="<?php echo htmlSafeTag($arrOutput['structure']['organisation']['email']); ?>">
			<meta itemprop="telephone" content="<?php echo htmlSafeTag($arrOutput['structure']['organisation']['telephone']); ?>">
			<meta itemprop="logo" content="<?php echo htmlSafeTag($arrOutput['structure']['organisation']['logo']); ?>">
			<meta itemprop="legalName" content="<?php echo htmlSafeTag($arrOutput['structure']['organisation']['legalName']); ?>">
			<meta itemprop="name" content="<?php echo htmlSafeTag($arrOutput['structure']['organisation']['legalName']); ?>">
		</span>	
		<!-- end structured data organisation -->
		<!-- start structured data webpage -->
		<span itemscope="" itemtype="<?php echo $arrOutput['structure']['shema'];?>">
			<!-- start structured data generic infos -->
			<meta itemprop="inLanguage" content="<?php echo htmlSafeTag($arrOutput['structure']['lang']);?>">	
			<meta itemprop="name" content="<?php echo htmlSafeTag($arrOutput['structure']['name']);?>">
			<meta itemprop="description" content="<?php echo htmlSafeTag($arrOutput['structure']['description']);?>">	
			<!-- end structured data generic infos -->
			<!-- start structured data specialty -->
			<span itemscope="" itemtype="http://schema.org/MedicalSpecialty" itemprop="specialty">
				<meta itemprop="name" content="<?php echo htmlSafeTag($arrOutput['structure']['specialty']['name']); ?>">
				<meta itemprop="description" content="<?php echo htmlSafeTag($arrOutput['structure']['specialty']['description']); ?>">
			</span>
			<!-- end structured data specialty -->
			<!-- start structured data copyright -->
			<span itemscope itemtype="https://health-lifesci.schema.org/Organization" itemprop="copyrightHolder">
				<meta itemprop="url" content="<?php echo htmlSafeTag($arrOutput['structure']['copyrightHolder']['url']); ?>">
				<meta itemprop="email" content="<?php echo htmlSafeTag($arrOutput['structure']['copyrightHolder']['email']); ?>">
				<meta itemprop="telephone" content="<?php echo htmlSafeTag($arrOutput['structure']['copyrightHolder']['telephone']); ?>">
				<meta itemprop="logo" content="<?php echo htmlSafeTag($arrOutput['structure']['copyrightHolder']['logo']); ?>">
				<meta itemprop="legalName" content="<?php echo htmlSafeTag($arrOutput['structure']['copyrightHolder']['legalName']); ?>">
			</span>
			<!-- end structured data copyright -->
		<?php
		}
	}
	
	
	