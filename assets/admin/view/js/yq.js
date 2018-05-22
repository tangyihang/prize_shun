// JavaScript Document

function swap_tab(n){
	for(var i=1;i<=4;i++){
		var curB=document.getElementById("tab_t"+i);
		if(n==i){
			curB.className="a2";
			
		}else{
			curB.className="a1";
		
		}
	}
}