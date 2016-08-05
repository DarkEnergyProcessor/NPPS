// JavaScript Document

/************************
  tab.js
  written：Yosuke Takada
  date：2013/07/23
  version：1.0
*************************/


window.onload = function() {
	tab();
}

function tab(id) {
	var lis = document.getElementById("tabs").getElementsByTagName("li");
	for(var i = 0; i < lis.length;i++) {
		if(id) {
			var n = lis[i].getAttribute("name");
			var box = document.getElementById(n);
			if(n == id) {
				box.style.display		= "block";
				box.style.visibility	= "visible";
				lis[i].className		= "open";
			} else {
				box.style.display		= "none";
				box.style.visibility	= "hidden";
				lis[i].className		= "";
			}
		} else {
			lis[i].onclick = function() {
				tab(this.getAttribute("name"));
			}
			lis[i].ontouchstart = function() {
				tab(this.getAttribute("name"));
			}
		}
	}
	if(!id) {
		// tab("デフォルトで表示させるタブ");
		tab("box1");
	}
}
