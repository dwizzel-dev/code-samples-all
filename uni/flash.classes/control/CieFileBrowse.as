/*

FileBrowse to upload files for the courriel

*/


//import flash.net.FileReferenceList;
import flash.net.FileReference;
//import graphic.CieSquare;
import control.CieTools;
import control.CieButton;
import messages.CieTextMessages;

dynamic class control.CieFileBrowse{

	static private var __className = 'CieFileBrowse';
	
	private var __arrFiles:Array;
	private var __allTypes:Array;
	private var __textTypes:Object;	
	private var __imageTypes:Object;
	private var __mv:MovieClip;
	private var __mvAttach:MovieClip;
	//private var __mvAttachements:Array;
	//private var __toolBrowse:CieTools;
	private var __toolBrowse:CieButton;
	private var __hvSpacer:Number;
	private var __fileSpacer:Number;
	//private var __fileRef:FileReference;
	private var __fileRef:Array;
	public  var __fileListener:Object;
	//private var __fileRefList:FileReferenceList;
	private var __cbClass:Object;
	private var __cbObject:Object;
	
	//new object
	private var __cmptAttach:Number;
	private var __cmptIndexArrFileRef:Number;
	
	/*************************************************************************************************************************************************/
	
	public function CieFileBrowse(mv:MovieClip){
		this.__mv = mv;
		this.__hvSpacer = 10;
		this.__fileRef = new Array();
		this.__fileSpacer = 4;
		this.__cmptAttach = 0;
		this.__arrFiles = new Array();
		this.__allTypes = new Array();
		this.__textTypes = new Object();
		this.__imageTypes = new Object();
		//this.__videoTypes = new Object();
		//this.__mvAttachements = new Array();
		this.__cmptIndexArrFileRef = -1;
		
		this.setTypes();		
		this.initListener();	
		//this.initFileRef();
		this.setDisplay();
		};
	
	/*************************************************************************************************************************************************/
	
	private function initListener(Void):Void{
		this.__fileListener = new Object();
		this.__fileListener.__class = this;
		//ON SELECT
		this.__fileListener.onSelect = function(file:FileReference):Void{
			this.__class.insertFile(file);
			};
		};

	/*************************************************************************************************************************************************/	
		
	public function	setRedrawFunction(cbClass:Object, oPanel:Object):Void{
		this.__cbClass = cbClass;
		this.__cbObject = oPanel;
		};
		
	/*************************************************************************************************************************************************/	
		
	private function setDisplay(Void):Void{
		//empty movie will hold all
		this.__mvAttach = this.__mv.attachMovie('mvContent', 'ATTACH_REPLY', this.__mv.getNextHighestDepth());
		this.__mvAttach._x = 0;
		this.__mvAttach._y = 0;
		//draw a button(tool) after the text box
		this.__toolBrowse = new CieButton(this.__mvAttach, gLang[0], 120, 20, 0, 0);
		this.__toolBrowse.getMovie().__class = this;
		this.__toolBrowse.getMovie().onRelease = function(Void):Void{
			this.__class.fileBrowse();
			};
		
		};
	
	/*************************************************************************************************************************************************/
	private function removeFile(fileName:String):Void{
		if(this.__arrFiles[fileName] != undefined){
			//remove the clip
			this.__arrFiles[fileName].__mv.removeMovieClip();
			//delete in the array
			delete this.__arrFiles[fileName];
			//loop throught all for repositionning
			var yPos:Number = 0;
			for(var o in this.__arrFiles){
				this.__arrFiles[o].__mv._y = yPos;
				yPos += this.__arrFiles[o].__mv._height + this.__fileSpacer;
				}
			//reposition the browse button after all filesBoxes
			this.__toolBrowse.redraw(0, yPos + this.__hvSpacer);
			//redraw all the section courrier
			//MODIFIED BY AARIZO
			this.__cmptAttach--;
			if(this.__mv.__isMsgOpen != 3){
				this.__mv.__isMsgOpen = 2;
				}
			this.__cbClass.redrawMultipleTextBoxMsgCourriel(this.__cbObject.__class.getPanelContent(), this.__cbClass.__registeredForResizeEvent['courriel'].__textboxes, this.__cbObject.__class.getPanelSize().__width);	
			cStage.redraw();
			//MODIFIED BY AARIZO
			}
		};
		
	/*************************************************************************************************************************************************/	
	
	private function insertFile(file:FileReference):Void{
		var maxSize:Number = 307200;
		var bContinueInsertFile:Boolean = true;
		var fileName:String = file.name;
		
		//max file size
		if(Number(file.size) >  maxSize){
			new CieTextMessages('MB_OK', gLang[504], gLang[16]);
			bContinueInsertFile = false;
			}
				
		if(this.__arrFiles[fileName] == undefined && bContinueInsertFile){
			//attache the textBox
			var mvAttachement:MovieClip = this.__mvAttach.attachMovie('mvAttachReply', 'ATTACHEMENTS_' + fileName, this.__mvAttach.getNextHighestDepth());
			//put the filename
			mvAttachement.txtFile.htmlText = unescape(fileName);
			mvAttachement.txtFile._x = 15;
			//draw around the text box
			
			//var toolDelete = new CieTools(mvAttachement, gLang[1], '16X16', 'mvIconImage_5');
			var toolDelete = new CieTools(mvAttachement, gLang[1], '16X16', 'mvIconImage_23');
			toolDelete.changeColor(0xffffff, 0, 0xffffff, 0xffffff, 0xffffff);
			
			toolDelete.redraw(0, 0);
			toolDelete.getIcon().__class = this;
			toolDelete.getIcon().__filename = fileName;
			toolDelete.getIcon().onRelease = function(Void):Void{
				this.__class.removeFile(this.__filename);
				};
			//put the file in the array
			this.__cmptAttach++;
			this.__arrFiles[fileName] = new Object();
			this.__arrFiles[fileName].__name = fileName;
			this.__arrFiles[fileName].__file = file;
			this.__arrFiles[fileName].__mv = mvAttachement;
			//loop throught all for repositionning
			var yPos:Number = 0;
			for(var o in this.__arrFiles){
				this.__arrFiles[o].__mv._y = yPos;
				yPos += this.__arrFiles[o].__mv._height + this.__fileSpacer;
				}
			//reposition the browse button after all filesBoxes
			this.__toolBrowse.redraw(0, yPos + this.__hvSpacer);
			//redraw all the section courrier
			//MODIFIED BY AARIZO
			if(this.__mv.__isMsgOpen != 3){
				this.__mv.__isMsgOpen = 2;
				}
			this.__cbClass.redrawMultipleTextBoxMsgCourriel(this.__cbObject.__class.getPanelContent(), this.__cbClass.__registeredForResizeEvent['courriel'].__textboxes, this.__cbObject.__class.getPanelSize().__width);	
			cStage.redraw();
			//MODIFIED BY AARIZO
			}
		};
	
	
	/*************************************************************************************************************************************************/	
		
	public function getComptAttach(Void):Number{
		return this.__cmptAttach;
		};
		
	/*************************************************************************************************************************************************/	
		
	public function getFileBrowseArray(Void):Array{
		var arrTemp:Array = new Array();
		for(var o in this.__arrFiles){
			arrTemp[o] = new Object();
			arrTemp[o].__name = this.__arrFiles[o].__name;
			arrTemp[o].__file = this.__arrFiles[o].__file;
			}
		return arrTemp;
		};	
		
	/*************************************************************************************************************************************************/	

	public function fileBrowse(Void):Void{
		this.__fileRef[++this.__cmptIndexArrFileRef] = new FileReference();
		this.__fileRef[this.__cmptIndexArrFileRef].addListener(this.__fileListener);
		this.__fileRef[this.__cmptIndexArrFileRef].browse(this.__allTypes);
		};
	
	/*************************************************************************************************************************************************/
	
	public function redraw(Void):Void{
		//clear the drawings
		this.__mvAttach.clear();
		};
		
	/*************************************************************************************************************************************************/	
		
	public function getMovie(Void):MovieClip{
		return this.__mvAttach;
		};
	
	/*************************************************************************************************************************************************/
	
	private function setTypes(Void):Void{
		//images files
		this.__imageTypes.description = "Images (*.jpg, *.jpeg, *.gif, *.png)";
		this.__imageTypes.extension = "*.jpg;*.jpeg;*.gif;*.png";
		this.__allTypes.push(this.__imageTypes);
		//text files
		this.__textTypes.description = "Text Files (*.txt, *.rtf)";
		this.__textTypes.extension = "*.txt;*.rtf";
		this.__allTypes.push(this.__textTypes);
		//video files
		//this.__videoTypes.description = "Video Files (*.mpg;, *.mpeg, *.mp4, *.avi, *.mov, *.wmv, *.flv)";
		//this.__videoTypes.extension = "*.mpg;*.mpeg;*.mp4;*.avi;*.mov;*.wmv;*.flv";
		//this.__allTypes.push(this.__videoTypes);
		};
		
	/*************************************************************************************************************************************************/	
	
	public function getClassName(Void):String{
		return __className;
		};
	
	public function getClass(Void):CieFileBrowse{
		return this;
		};
		
		
	};
	