//
// CMSUno
// Theme Uno1
//
function f_save_uno1(){
	// generic name : f_save_[name of the theme]()
	var h=[],a=document.getElementById("themeOption").getElementsByTagName("input");
	h.push({name:'action',value:'save'});
	h.push({name:'unox',value:Unox});
	for(v=0;v<a.length;v++){
		if(a[v].type=='text')h.push({name:a[v].name,value:a[v].value});
		else if(a[v].type=='checkbox')h.push({name:a[v].name,value:a[v].checked?1:0});
	}
	jQuery.post('uno/template/uno1/uno1.php',h,function(r){
		f_alert(r);
	});
}
function f_load_uno1(){
	jQuery.getJSON("uno/data/"+Ubusy+"/uno1.json",function(data){
		jQuery.each(data,function(k,v){
			var a=document.getElementById(k);
			if(a.tagName=='INPUT'&&a.type=='text')a.value=v;
			else if(a.tagName=='INPUT'&&a.type=='checkbox')a.checked=(v==1?true:false);
		});
		jQuery('#themeOption .color').colorPicker();
	});
}
function f_del_uno1(f){
	var g=f.parentNode.firstChild,h=g.id;
	jQuery(g).parent().empty().append('<input type="text" class="color" style="width:150px;" name="'+h+'" id="'+h+'" /><span class="del" onclick="f_del_uno1(this);"></span>');
}
//
f_load_uno1();