/*

selectbox

*/

dynamic class control.CieSelectBox{

	static private var __className = 'CieSelectBox';
	static private var __instance:CieSelectBox;
	
	private var __mv:MovieClip;
	private var __selected:MovieClip;
	private var __choices:MovieClip;
	private var __selectID:String;
	private var __arr:Array;
	private var __cbFunction:Function; //rajout d,un callBack Function pour le catch the onRelease
	private var __cbObject:Object; //rajout d,un callBack Function pour le catch the onRelease
	
	public function CieSelectBox(mv:MovieClip, arr:Array, rowCount:Number, w:Number, h:Number, x:Number, y:Number, cbFunction:Function, cbObject:Object){
		this.__mv = mv;
		this.__arr = arr;
		//the callBack function
		this.__cbFunction = cbFunction;
		this.__cbObject = cbObject;
		this.init();
		
		this.createSelectedText(w, x, y);
		this.createSelectChoices(w, x, y + this.__selected._height, rowCount);
		this.__choices._visible = false;
		};
		
	/*************************************************************************************************************************************************/	
		
	private function init(Void):Void{
		this.__selected = this.__mv.createEmptyMovieClip('selected', this.__mv.getNextHighestDepth());
		this.__choices = this.__mv.createEmptyMovieClip('choices', this.__mv.getNextHighestDepth());
		this.__butt = this.__mv.createEmptyMovieClip('butt', this.__mv.getNextHighestDepth());
		};	
		
	/*************************************************************************************************************************************************/
	
	private function createSelectedText(w:Number, x:Number, y:Number){
		
		var strLabel:String = '';
		var strID:String = '';
		
		for (var o in this.__arr){
			strLabel = this.__arr[o][1];
			strID = this.__arr[o][0];
			break;
			}
		
		this.__selected.createClassObject(mx.controls.TextInput, "tSelect", 10);
		this.__selected.tSelect.setStyle("borderStyle", "dropDown");
		this.__selected.tSelect.move(x, y);
		this.__selected.tSelect.setSize(w, 22);
		this.__selected.tSelect.password = false;
		this.__selected.tSelect.editable = false;
		this.__selected.tSelect.selectable = false;
		this.__selected.tSelect.text = strLabel;
		this.__selectID = strID;
		// butt
		this.__butt.attachMovie("ComboDownArrowUp", "button", 10, {_x: x + w, _y: y});
		
		this.__selected.__super = 
		this.__butt.__super = this;
		this.__selected.onRelease = 
		this.__butt.onRelease = function(Void):Void{
			if (!this.__super.__choices._visible){
				this.__super.__choices._visible = true;
			}else{
				this.__super.__choices._visible = false;
				}
			//call du callBack Function prealablement declare comme dernier parametre de CieSelectBox
			this.__super.__cbFunction(this.__super.__cbObject);	
			}
		};
		
	/*************************************************************************************************************************************************/	
		
	private function createSelectChoices(w:Number, x:Number, y:Number, rowCount:Number){
		//liste choix multiple
		var iNumOfElement:Number = 0;
		this.__choices.createClassObject(mx.controls.List, "cList", 50);
		this.__choices.cList.rowHeight = 17;
		this.__choices.cList.tabEnabled = false;
		this.__choices.cList.setStyle("borderStyle", "dropDown");
		this.__choices.cList.move(x, y);
		this.__choices.cList.setSize(w, 17);
		for (var o in this.__arr){
			iNumOfElement++;
			this.__choices.cList.addItem({label:String(this.__arr[o][1]), data:String(this.__arr[o][0])});
			}
		this.__choices.cList.multipleSelection = false; 
		
		if (iNumOfElement <= rowCount){
			this.__choices.cList.rowCount = iNumOfElement;
			this.__choices.cList.vScrollPolicy = 'off';
		}else{
			this.__choices.cList.rowCount = rowCount;
			this.__choices.cList.vScrollPolicy = 'on';
			}
		
		// Create listener object.
		var listListener:Object = new Object();
		listListener.__super = this;
		listListener.change = function(evt_obj:Object):Void{
			if(this.__super.__selectID != evt_obj.target.value){
				this.__super.__cbObject.__bchanged = true;
			}else{
				this.__super.__cbObject.__bchanged = false;
				}
			this.__super.__selectID = evt_obj.target.value;	
			this.__super.__selected.tSelect.text = evt_obj.target.selectedItem.label;
			this.__super.__choices._visible = false;	
			this.__super.__cbFunction(this.__super.__cbObject);	
			};
		// Add listener.
		this.__choices.cList.addEventListener("change", listListener);
		};
		
	/*************************************************************************************************************************************************/
	
	public function setSelectionValue(id):Void{
		//serach for the value
		for(var i=0 ; i<this.__choices.cList.length; i++){
			if(this.__choices.cList.getItemAt(i).data == id){
				this.__selected.tSelect.text = this.__choices.cList.getItemAt(i).label;
				this.__selectID = id;
				break;
				}
			}
		};	
		
	/*************************************************************************************************************************************************/
	
	public function removeAll(Void):Void{
		this.__choices.cList.removeAll();
		};
		
	/*************************************************************************************************************************************************/

	public function fillList(arr:Array, rowCount:Number):Void{
		var iNumOfElement:Number = 0;
		//change the array
		this.__arr = arr;
		//fill the list
		for(var o in this.__arr){
			iNumOfElement++;
			this.__choices.cList.addItem({label:String(this.__arr[o][1]), data:String(this.__arr[o][0])});
			}
		//show scroll or not and limit height if item less then rowCount	
		if (iNumOfElement <= rowCount){
			this.__choices.cList.rowCount = iNumOfElement;
			this.__choices.cList.vScrollPolicy = 'off';
		}else{
			this.__choices.cList.rowCount = rowCount;
			this.__choices.cList.vScrollPolicy = 'on';
			}
		};
	
	/*************************************************************************************************************************************************/
	
	public function hideChoices(Void):Void{
		this.__choices._visible = false;
		};
	
	/*************************************************************************************************************************************************/
	
	public function getSelectionValue(Void):String{
		return this.__selectID;
		};

	/*************************************************************************************************************************************************/	
		
	public function getSelectionText(Void):String{
		return this.__selected.tSelect.text
		};	

	/*************************************************************************************************************************************************/
	
	public function getClassName(Void):String{
		return __className;
		};	
	
	public function getClass(Void):CieSelectBox{
		return this;
		};
	};