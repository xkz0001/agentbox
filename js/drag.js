// JavaScript Document


// iMouseDown represents the current mouse button state: up or down
var mouseOffset=null;
var iMouseDown=false;
var lMouseState=false;
var dragObject=null;
// Demo 0 variables
var DragDrops=[];
var curTarget=null;
var lastTarget=null;
var dragHelper=null;
var tempDiv=null;
var rootParent=null;
var rootSibling=null;
Number.prototype.NaN0=function () {
	return isNaN(this)?0:this;
}
function CreateDragContainer() {
	var cDrag=DragDrops.length;
	DragDrops[cDrag]=[];
	for(var i=0;i<arguments.length;i++) {
		var cObj=arguments[i];
		DragDrops[cDrag].push(cObj);
		cObj.setAttribute('DropObj',cDrag);
		for(var j=0;j<cObj.childNodes.length;j++) {
			// Firefox puts in lots of #text nodes...skip these
			if(cObj.childNodes[j].nodeName=='#text')continue;
			cObj.childNodes[j].setAttribute('DragObj',cDrag);
		}
	}
}
function mouseCoords(ev) {
	if(ev && (ev.pageX||ev.pageY)) {
		return {
			x:ev.pageX,y:ev.pageY
		};
	}
	return {
		x:ev.clientX+document.body.scrollLeft-document.body.clientLeft,
		y:ev.clientY+document.body.scrollTop-document.body.clientTop
	};
}
function getMouseOffset(target,ev) {
	ev=ev||window.event;
	var docPos=getPosition(target);
	var mousePos=mouseCoords(ev);
	return {
		x:mousePos.x-docPos.x,y:mousePos.y-docPos.y
	};
}
function getPosition(e) {
	var left=0;
	var top=0;
	while(e.offsetParent) {
		left+=e.offsetLeft+(e.currentStyle?(parseInt(e.currentStyle.borderLeftWidth)).NaN0():0);
		top+=e.offsetTop+(e.currentStyle?(parseInt(e.currentStyle.borderTopWidth)).NaN0():0);
		e=e.offsetParent;
	}
	left+=e.offsetLeft+(e.currentStyle?(parseInt(e.currentStyle.borderLeftWidth)).NaN0():0);
	top+=e.offsetTop+(e.currentStyle?(parseInt(e.currentStyle.borderTopWidth)).NaN0():0);
	return {
		x:left,y:top
	};
}
function mouseMove(ev) {
	ev=ev||window.event;
	var target=ev.target||ev.srcElement;
	var mousePos=mouseCoords(ev);
	if(lastTarget&&(target!==lastTarget)) {
		// reset the classname for the target element
		var origClass=lastTarget.getAttribute('origClass');
		if(origClass)lastTarget.className=origClass;
	}
	var dragObj=target.getAttribute('dragObj');
	if(dragObj!=null) {
		// mouseOver event - Change the item's class if necessary
		if(target!=lastTarget) {
			var oClass=target.getAttribute('overClass');
			if(oClass) {
				target.setAttribute('origClass',target.className);
				target.className=oClass;
			}
		}
		// if the user is just starting to drag the element
		if(iMouseDown&&!lMouseState) {
			curTarget=target;
			rootParent=curTarget.parentNode;
			rootSibling=curTarget.nextSibling;
			mouseOffset=getMouseOffset(target,ev);
			for(var i=0;i<dragHelper.childNodes.length;i++)
				dragHelper.removeChild(dragHelper.childNodes[i]);
			dragHelper.appendChild(curTarget.cloneNode(true));
			dragHelper.style.display='block';
			var dragClass=curTarget.getAttribute('dragClass');
			if(dragClass) {
				dragHelper.firstChild.className=dragClass;
			}
			dragHelper.firstChild.removeAttribute('dragObj');
			var dragConts=DragDrops[dragObj];
			curTarget.setAttribute('startWidth',parseInt(curTarget.offsetWidth));
			curTarget.setAttribute('startHeight',parseInt(curTarget.offsetHeight));
		}
	}
	if(curTarget) {
		dragHelper.style.top=mousePos.y-mouseOffset.y;
		dragHelper.style.left=mousePos.x-mouseOffset.x;
		var dragConts=DragDrops[curTarget.getAttribute('DragObj')];
		var activeCont=null;
		
		if(activeCont) {
			// beforeNode will hold the first node AFTER where our div belongs
			var beforeNode=null;
			// loop through each child node (skipping text nodes).
			for(var i=activeCont.childNodes.length-1;i>=0;i--) {
				with(activeCont.childNodes[i]) {
					if(nodeName=='#text')continue;
					// if the current item is "After" the item being dragged
					if(
					curTarget!=activeCont.childNodes[i]&&
					((getAttribute('startLeft')+getAttribute('startWidth'))>xPos)&&
					((getAttribute('startTop')+getAttribute('startHeight'))>yPos)) {
						beforeNode=activeCont.childNodes[i];
					}
				}
			}
			if(beforeNode) {
				if(beforeNode!=curTarget.nextSibling) {
					activeCont.insertBefore(curTarget,beforeNode);
				}
			}else {
				if((curTarget.nextSibling)||(curTarget.parentNode!=activeCont)) {
					activeCont.appendChild(curTarget);
				}
			}
			// make our drag item visible
			if(curTarget.style.display!='') {
				curTarget.style.display='';
			}
		}
		
	}
	// track the current mouse state so we can compare against it next time
	lMouseState=iMouseDown;
	// mouseMove target
	lastTarget=target;
	// track the current mouse state so we can compare against it next time
	lMouseState=iMouseDown;
	// this helps prevent items on the page from being highlighted while dragging
	return false;
}
function mouseUp(ev) {
	ev=ev||window.event;
	if(curTarget) {
		// hide our helper object - it is no longer needed
		dragHelper.style.display='none';
		var mousePos=mouseCoords(ev);
		var xPos=mousePos.x-mouseOffset.x+(parseInt(curTarget.getAttribute('startWidth'))/2);
		var yPos=mousePos.y-mouseOffset.y+(parseInt(curTarget.getAttribute('startHeight'))/2);
		for(var i=0;i<listTd.childNodes.length;i++) {
			with(listTd.childNodes[i].firstChild) {
				var temp = listTd.childNodes[i];
				if((parseInt(getAttribute('startLeft'))<xPos)&&
				(parseInt(getAttribute('startTop'))<yPos)&&
				((parseInt(getAttribute('startLeft'))+parseInt(getAttribute('startWidth')))>xPos)&&
				((parseInt(getAttribute('startTop'))+parseInt(getAttribute('startHeight')))>yPos)) {
					if(listTd.childNodes[i].firstChild.getAttribute('dragIndex') != curTarget.getAttribute('dragIndex'))
						changeFold(curTarget.getAttribute('dragIndex'), listTd.childNodes[i].firstChild.getAttribute('dragIndex'));
					// exit the for loop
					break;
				}
			}
		}
	}
	curTarget=null;
	iMouseDown=false;
}
function mouseDown(ev) {
	ev=ev||window.event;
	var target=ev.target||ev.srcElement;
	var dragObj=target.getAttribute('dragObj');
	// if the mouse was moved over an element that is draggable
	if(dragObj!=null) {
		for(var j=0;j<listTd.childNodes.length;j++) {
			with(listTd.childNodes[j].firstChild) {
				if((nodeName=='#text')||(listTd.childNodes[j].firstChild==target))continue;
				var pos=getPosition(listTd.childNodes[j].firstChild);
				// save the width, height and position of each element
				setAttribute('startWidth',parseInt(offsetWidth));
				setAttribute('startHeight',parseInt(offsetHeight));
				setAttribute('startLeft',pos.x);
				setAttribute('startTop',pos.y);
			}
		}
		iMouseDown=true;
		if(lastTarget) {
			return false;
		}
	}
}
document.onmousemove=mouseMove;
document.onmousedown=mouseDown;
document.onmouseup=mouseUp;
window.onload=function () {
	// Create our helper object that will show the item while dragging
	dragHelper=document.createElement('DIV');
	dragHelper.style.cssText='position:absolute;display:none;';
	document.body.appendChild(dragHelper);
}