
/*****

Image Cross Fade Redux
Version 1.0
Last revision: 02.15.2006
steve@slayeroffice.com

Please leave this notice intact. 

Rewrite of old code found here: http://slayeroffice.com/code/imageCrossFade/index.html


*****/


window.addEventListener?window.addEventListener("load",so_init,false):window.attachEvent("onload",so_init);

var d=document, imgs = new Array(), zInterval = null, current=0, pause=false;

function so_init() {
  if(!d.getElementById || !d.createElement)return;
  imgs[0] = d.getElementById("diashow").getElementsByTagName("img")[0];
  imgs[0].style.display = "block";
  imgs[0].xOpacity = .99;
  setTimeout(so_xfade,3000);
  newimg();
}

function so_xfade() {
	cOpacity = imgs[current].xOpacity;
	nIndex = imgs[current+1]?current+1:0;

	nOpacity = imgs[nIndex].xOpacity;
	
	cOpacity-=.05; 
	nOpacity+=.05;
	
	imgs[nIndex].style.display = "block";
	imgs[current].xOpacity = cOpacity;
	imgs[nIndex].xOpacity = nOpacity;
	
	setOpacity(imgs[current]); 
	setOpacity(imgs[nIndex]);
	
	if(cOpacity<=0) {
		imgs[current].style.display = "none";
		current = nIndex;
		/* Verweilzeit eines Bildes */
		setTimeout(so_xfade,10000);
    newimg();
  } else {
	/* Fade Effekt*/
    setTimeout(so_xfade,5);
	}
	
	function setOpacity(obj) {
		if(obj.xOpacity>.99) {
			obj.xOpacity = .99;
			return;
		}
		obj.style.opacity = obj.xOpacity;
		obj.style.MozOpacity = obj.xOpacity;
		obj.style.filter = "alpha(opacity=" + (obj.xOpacity*100) + ")";
	}
	
}

function newimg() {
  if (current < 23) {
    var cu1=current+1, cu2=current+2;
    imgAdr="/Fakultationspraesentation/Folie";
    imgs[cu1] = new Image();
    imgs[cu1].src = imgAdr + ((cu2)<10 ? "0" + (cu2) : (cu2)) + ".jpg";
    imgs[cu1].xOpacity = 0;
    d.getElementById("diashow").appendChild(imgs[cu1]);
  }
}
