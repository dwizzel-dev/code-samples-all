/*

selectbox

*/

import mx.controls.List;

dynamic class control.CieListBox{

	static private var __className = 'CieListBox';
	
	private var __mv:MovieClip;
	private var __choices:MovieClip;
	private var __arr:Array;
	private var __arrSelectedItem:Array;
	private var __cbFunction:Function; //rajout d,un callBack Function pour le catch the onRelease
	private var __cbObject:Object; //rajout d,un callBack Function pour le catch the onRelease
	private var __h:Number; //rajout d,un callBack Function pour le catch the onRelease
	
	public function CieListBox(mv:MovieClip, arr:Array, w:Number, x:Number, y:Number, cbFunction:Function, cbObject:Object){
		this.__mv = mv;
		this.__arr = arr;
		//the callBack function
		this.__cbFunction = cbFunction;
		this.__cbObject = cbObject;
		this.__rowCount = 5;
		this.__h = 90;
		
		this.__choices = this.__mv.createEmptyMovieClip('choices', this.__mv.getNextHighestDepth());
		
		this.createSelectChoices(w, this.__h, x, y);		
		};
		
			
	/*************************************************************************************************************************************************/	
		
	private function createSelectChoices(w:Number, h:Number, x:Number, y:Number){
		//liste choix multiple
		var iNumOfElement:Number = 0;
		this.__choices.createClassObject(mx.controls.List, "cList", 50);
		this.__choices.cList.rowHeight = 17
		this.__choices.cList.tabEnabled = false;
		this.__choices.cList.setStyle("borderStyle", "dropDown");
		this.__choices.cList.move(x, y);
		this.__choices.cList.setSize(w, h);
		for (var o in this.__arr){
			iNumOfElement++;
			this.__choices.cList.addItem({label:String(this.__arr[o][1]), data:String(this.__arr[o][0])});
			}
		this.__choices.cList.multipleSelection = true; 
		this.__choices.cList.__rowCount = this.__rowCount;
		if (iNumOfElement <= this.__rowCount){
			this.__choices.cList.vScrollPolicy = 'off';
		}else{
			this.__choices.cList.vScrollPolicy = 'on';
			}
		
		// Create listener object.
		var listListener:Object = new Object();
		listListener.__super = this;
		listListener.change = function(evt_obj:Object):Void{
			this.__super.__arrSelectedItem = new Array();
			for(var o in evt_obj.target.selectedItems){	
				this.__super.__arrSelectedItem.push(evt_obj.target.selectedItems[o].data);
				}
			this.__super.__cbFunction(this.__super.__cbObject);
			};
		// Add listener.
		this.__choices.cList.addEventListener("change", listListener);
		};
		
	/*************************************************************************************************************************************************/	
	
	public function setSelectionValue(id:String):Void{
		//split the string into values
		var arrValues:Array = id.split(',');
		var arrIndex:Array = new Array();
		this.__arrSelectedItem = new Array();
		//search for the value
		for(var i=0 ; i<this.__choices.cList.length; i++){
			for(var o in arrValues){
				if(this.__choices.cList.getItemAt(i).data == arrValues[o]){
					//arr containing the selected items
					this.__arrSelectedItem.push(this.__choices.cList.getItemAt(i).data);
					//indices to be select
					arrIndex.push(i);
					}
				}	
			}
		this.__choices.cList.selectedIndices = arrIndex;
		};	
		
		
	/*************************************************************************************************************************************************/

	public function fillList(arr:Array):Void{
		var iNumOfElement:Number = 0;
		//change the array
		this.__arr = arr;
		//fill the list
		for(var o in this.__arr){
			iNumOfElement++;
			this.__choices.cList.addItem({label:String(this.__arr[o][1]), data:String(this.__arr[o][0])});
			}
		//show scroll or not and limit height if item less then rowCount
		if (iNumOfElement <= this.__rowCount){
			this.__choices.cList.vScrollPolicy = 'off';
		}else{
			this.__choices.cList.vScrollPolicy = 'on';
			}
		};
	
	/*************************************************************************************************************************************************/
	
	public function removeAll(Void):Void{
		//F*** function leave crap on the stage thx macromedia	
		this.__choices.cList.removeAll();
		};
		
	/*************************************************************************************************************************************************/
	
	public function getSelectionValue(Void):Array{
		return this.__arrSelectedItem;
		};

	/*************************************************************************************************************************************************/	
		
	public function getSelectionText(Void):String{
		return this.__selected.tSelect.text
		};	

	/*************************************************************************************************************************************************/
	
	public function getClassName(Void):String{
		return __className;
		};	
	
	public function getClass(Void):CieListBox{
		return this;
		};
	};