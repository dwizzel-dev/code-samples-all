

SCHEMA.ORG

	http://health-lifesci.schema.org/MedicalEntity

	http://schema.org/MedicalEntity

	- MedicalEntity
		- medicineSystem
		- relevantSpecialty
		- recognizingAuthority


	http://schema.org/PhysicalActivity

	http://health-lifesci.schema.org/PhysicalActivity

	http://schema.org/PhysicalActivityCategory

	http://health-lifesci.schema.org/PhysicalActivityCategory


	- PhysicalActivity


	https://health-lifesci.schema.org/MedicalAudience

	https://health-lifesci.schema.org/MedicalSpecialty

	https://health-lifesci.schema.org/Physiotherapy

	- Physiotherapy

	


------------------------------------------


SELECT * 
FROM keywords 
WHERE name REGEXP '^gen[\d]+.*$'
ORDER BY name ASC


SELECT * FROM `keywords` WHERE `name` LIKE 'GEN%' ORDER BY `name` ASC

SELECT * FROM keywords WHERE name REGEXP '^GEN[\d]+.*' ORDER BY name ASC

SELECT * FROM keywords WHERE name REGEXP '^gen[0-9]+$' ORDER BY name ASC

SELECT * FROM keywords WHERE title REGEXP '^[0-9]+.*$' ORDER BY `keywords`.`title` ASC

SELECT * FROM `keywords` WHERE `name` REGEXP '^[&]+.*$' ORDER BY `name` ASC

SELECT * FROM `keywords` WHERE `name` REGEXP "^.*[\"',]+.*$" ORDER BY `name` ASC

SELECT * FROM `keywords` WHERE `name` REGEXP "^.*[Ã]+.*|.*[ã]+.*$"

SELECT * FROM `keyword` WHERE `keyword` REGEXP "^.*Ã][‰].*|.*[ã][‰].*$"

SELECT * FROM `keyword` WHERE `keyword` REGEXP "^.*Ãƒ.*$"

SELECT * FROM `keywords` WHERE `name` LIKE "%Ã%"

SELECT * FROM `exercises` WHERE `short_title` REGEXP "^[A-Z]{3}[[:space:][:digit:]]+$"

SELECT * FROM `exercises` WHERE `short_title` REGEXP "^[A-Z]{3}[ [:digit:]]+$"

SELECT * FROM exercises WHERE `short_title` REGEXP "^[^a-zA-Z0-9&].*$"



Sans titre
Empty text
Insert your text here
Ins&eacute;rez votre texte ici
Ins&eacute;rez le nom de l'exercice ici


Ã© = &eacute;
Ã³ = &oacute;
Ã¡ = &aacute;
Ã¨ = &egrave;
Ã± = &ntilde;
Ã£ = &atilde;
Ãº = &uacute;
Ãª = &ecirc;
Ã¢ = &acirc;
Ã´ = &ocirc;
Ã§ = &ccedil;







DELETE FROM keywords WHERE name REGEXP '^ACV[0-9]+|AGEN[0-9]+|AIR[0-9]+|ALL[0-9]+|AMB[0-9]+|AMP[0-9]+|AMT[0-9]+|ANA[0-9]+|APE[0-9]+|APP[0-9]+|AQU[0-9]+|ASM[0-9]+|AST[0-9]+|AVQ[0-9]+|BAC[0-9]+|BAL[0-9]+|BAR[0-9]+|CAR[0-9]+|CH[0-9]+|CRE[0-9]+|CTS[0-9]+|DOG[0-9]+|ECH[0-9]+|EQU[0-9]+|ERG[0-9]+|FFC[0-9]+|FFG[0-9]+|FFS[0-9]+|FFT[0-9]+|FLE[0-9]+|FMS[0-9]+|GEN[0-9]+|GER[0-9]+|GOL[0-9]+|GRA[0-9]+|HAN[0-9]+|HCE[0-9]+|HCP[0-9]+|HCT[0-9]+|HWO[0-9]+|KCO[0-9]+|KIM[0-9]+|KOR[0-9]+|LEA[0-9]+|LSP[0-9]+|MAT[0-9]+|MHS[0-9]+|MLT[0-9]+|MRC[0-9]+|MSH[0-9]+|MTE[0-9]+|NEU[0-9]+|NEW[0-9]+|OHA[0-9]+|ORT[0-9]+|PBR[0-9]+|PED[0-9]+|PEN[0-9]+|PFL[0-9]+|PIL[0-9]+|PRE[0-9]+|PRO[0-9]+|PSP[0-9]+|PTE[0-9]+|PTP[0-9]+|PWR[0-9]+|REN[0-9]+|REP[0-9]+|RES[0-9]+|RIM[0-9]+|ROC[0-9]+|SHE[0-9]+|SMM[0-9]+|SPE[0-9]+|STE[0-9]+|STH[0-9]+|STP[0-9]+|SUR[0-9]+|TEN[0-9]+|VES[0-9]+|YOG[0-9]+|ZVE[0-9]+$';

DELETE FROM keywords WHERE keyword_id NOT IN(SELECT DISTINCT(keyword_id) FROM exercises_keywords);

SELECT DISTINCT(`keyword`) 
FROM  `keyword` 
WHERE  `keyword` REGEXP '^.*(&[#a-zA-Z\d]+;|\bu[\da-fA-F]{4}\b|\b0[xX][0-9a-fA-F]{1,4}\b|\\[a-zA-Z]{1}|\c[A-Z]).*$'
ORDER BY  `keyword`.`keyword` 


---------------------------------------------------------------------------------------------------------------------------

Projet: 

- visou.com 

Decription: 
	
- creer une liste d'exercises et de repertoires avec les category, filter, keyword que l'on a dans physiotec DB

Explication:

- la structure des repertoires

	1) les images

		a) thumbnails
			
			/images/physiotec/t0-nom-de-exercise-ID.jpg
			/images/physiotec/t1-nom-de-exercise-ID.jpg
	
		b) pictures
			
			/images/physiotec/p0-nom-de-exercise-ID.jpg
			/images/physiotec/p1-nom-de-exercise-ID.jpg


	2) les exercises listing

		a) pour les liens canonique tout les exercices vont se retrouver dans un seul repertoire selon la langue

			/physiotec/exercises/fr/nom-de-exercice-en-francais-ID.html
			/physiotec/exercises/en/exercise-name-in-english-ID.html
			etc...

		b) pour les categories

			1) 	/physiotec/exercises/fr/nom-de-la-categorie/
				/physiotec/exercises/en/category-name/
				etc...

			2) un fichier index.html contenant la liste des filters et exercise de cette categorie

				/physiotec/exercises/fr/nom-de-la-categorie/index.html 
				contenant
					a) la liste des filtres avec leur liens
					b) la liste des exercises avec leur lien nom-de-categorie/nom-de-filtre/nom-de-exercise-ID.html
				

		c) pour les filtres
			
			1) 	/physiotec/exercises/fr/nom-de-la-categorie/nom-du-filtre/
				/physiotec/exercises/en/category-name/filter-name/
				etc...

			2) un fichier index.html contenant la liste des filters et exercise de cette categorie/filter

				/physiotec/exercises/fr/nom-de-la-categorie/nom-du-filtre/index.html 
				contenant
					a) la liste des exercises avec leur lien nom-de-categorie/nom-de-filtre/nom-de-exercise-ID.html
					b) lien vers la categorie parent



	3) les keywords

		1) fichier de type physiotec/keywords/fr/nom-du-keyword.html

			a) contient la liste des category
			b) contient la liste des filtres
			c) contient la liste des exercices

		
	4) l'exercice en details

		a) doit etre nom-de-exercise-ID.html
		b) un titre H1
		c) une a deux images avec un alt="title" le nom de l'image est en rapport avec le nom de exercise /images/physiotec/p0-nom-de-exercise-ID.jpg
		d) une description racourcis avec un lien vers le trial sous le "read more"
		e) un video youtube sur cerain exercises qui sera change de facon random 
		f) la liste des categories dans lequel cet exercice fait partie
		g) la liste des categorie/filtres  dans lequel cet exercice fait partie
		h) un lien canonique menant vers le plus simple path /physiotec/exercises/fr/nom-de-exercice-en-francais-ID.html
		i) evidement tout les metas qui vont avec
		j) dans la description souligne les keyword avec un lien qui les mene vers /physiotec/keywords/fr/keyword-name-ID.html qui contient des liens vers d'autres exercice avec ce meme keyword classe par categorie/filters/
		h) une mini explicatioen de ce qu'est physiotec software	

