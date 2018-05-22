// JavaScript Document
function show01() {
 document.getElementById('small_list01').style.display=''
 //setTimeout(close01,3000);
}
function close01() {
 document.getElementById('small_list01').style.display='none'
}

//left_tree
function index_menu_mm(type,name) {

	submenu=$('sub_'+type+'_'+name).style;

	if (mm_old!=submenu) {
		if (mm_old!='') {
			mm_old.display='none';
		}
		submenu.display='block';
		mm_old=submenu;
	}else {
		submenu.display='none';
		mm_old='';
	}

	celarm();

}


function my_getbyid(id)
{
   itm = null;
   if (document.getElementById)
   {
      itm = document.getElementById(id);
   }
   else if (document.all)
   {
      itm = document.all[id];
   }
   else if (document.layers)
   {
      itm = document.layers[id];
   }
   
   return itm;
}

function ie_nick()
{
	var browser = navigator.appVersion;
	if(browser.indexOf("MSIE") >= 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}


function get_edit_more(id,event_hdl)
{
	xpos	=	0;
	ypos	=	0;
	if(ie_nick())
	{
		var Ypos;
		var Xpos;
		var Offwidth;
		var Offheight;
		var standardCompat = document.compatMode.toLowerCase();
		//alert(standardCompat);
		if (standardCompat == "css1compat")
		{
			Ypos = document.documentElement.scrollTop;
		}
		else if (standardCompat == "backcompat" || standardCompat == "quirksmode" )
		{
			Ypos = document.body.scrollTop;
		}
		var top_tmp	=	event_hdl.clientY	+	Ypos;
		var left_tmp	=	event_hdl.clientX;
	}
	else
	{
		var top_tmp	=	event_hdl.pageY;
		var left_tmp	=	event_hdl.pageX;
	}

	left_tmp	+=	xpos - 0;
	top_tmp	+=	ypos + 10;

	my_getbyid('em_'+id+'_edit').style.left	=	left_tmp	+	'px';
	my_getbyid('em_'+id+'_edit').style.top	=	top_tmp	+	'px';
	my_getbyid('em_'+id+'_edit').style.display = 'block';
	my_getbyid('span_'+id).innerHTML="<a href=\"javascript:cancel_edit_more('"+id+"')\">È¡Ïû</a>";
}

function cancel_edit_more(id)
{
	my_getbyid('em_'+id+'_edit').style.display = 'none';
	//my_getbyid('span_'+id).innerHTML="<a style='cursor:pointer' onclick=\"javascript:get_edit_more('"+id+"', event)\">ÐÞ¸Ä</a>";
}
function changeNav(id)
{
  document.getElementById('amenu'+id).className='dl_on';
 
  for ( var i=1; i<=9;i++)
   {
    if(i !=id)
	{
	 document.getElementById('amenu'+i).className='';
	}
   }
}
