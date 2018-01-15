/*

utilitaire de conversion

*/


dynamic class utils.CieConversion{

	static private var __className:String = 'CieConversion';
	
	public function CieConversion(Void){
		};
	             
	public function centimetresEnMetre(taille:Number):Number{
		// taille en centimetre
		return (taille/100); 
		};
		
	public function centimetresEnPouces(taille:Number):Number{
		// taille en centimetre
		return (taille/2.54); 
		};
	
	public function piedEnCentimetres(taille:Number):Number{
		//taille en pied		
		return ((taille*0.3048)*100);
		};
	
	public function metreEnPied(taille:Number):Number{
		// taille en metre
		return (taille/0.3048); 
		};	

	public function conversionTailleTexte(taille:Number):String{
		//centimetre
		var tailleEnMetre = this.centimetresEnMetre(taille);
		var tailleEnPied = this.metreEnPied(tailleEnMetre);		
		var tailleEnPouce = this.metreEnPied(tailleEnMetre) - Math.floor(this.metreEnPied(tailleEnMetre));
		
		tailleEnPied = tailleEnPied - tailleEnPouce;		
		tailleEnPouce = Math.ceil(this.centimetresEnPouces(this.piedEnCentimetres(tailleEnPouce)));
			
		var strTaillePied:String = String(tailleEnPied) + "'" + String(tailleEnPouce);
		var strTaille:String = String(String(tailleEnMetre) + ' m (' + strTaillePied + '")');
		return strTaille;
		};	
		
	/*  POIDS ******************************************************************************************/	
	public function lbs(poids:Number):Number{
		return Math.round(this.kg(Number(poids))/0.4536);
		};
			
	public function kg(poids:Number):Number{
		return Number(poids)/10;
		};
	
	public function conversionPoidsTexte(poids:Number):String{
		return String(this.kg(poids) + ' kg (' + this.lbs(poids) + ' lbs)');
		};
	
	/*  CHIFFRE APRES VIRGULE ****************************************************************************/	
	public function conversionChiffreApresVirgule(nombre:Number, exposant:Number):Number{
		var expose = 10;
		chVirgule = Math.pow(expose, exposant);
		var arrondi = Math.round(nombre*chVirgule);
		arrondi = arrondi/chVirgule;
		return arrondi;
		}    
	
	/*  RANDOM ******************************************************************************************/	
	public function makeRandomNum(iMin:Number, iMax:Number):Number{
		return Math.round(Math.random() * (iMax-iMin)) + iMin;
		}; 
		
	/***************************************************************************************************/		
	public function destroy(Void):Void{
		this = null;
		delete this;
		};
	/*	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieConversion{
		return this;
		};
	*/	
	}	
