<!DOCTYPE html>
<?php
if(!isset($_SESSION['cmsuno']) || !isset($_SESSION['unox']) || !isset($unox) || $_SESSION['cmsuno']!=$unox) exit();
?>
<?php
$user=0; $pass=0; // reset
if(file_exists(dirname(__FILE__).'/../files/archive.zip')) unlink(dirname(__FILE__).'/../files/archive.zip');
function f_theme()
	{
	// liste des themes dans un select
	$t = "uno/template/";
	$d = opendir($t);
	while(($f = readdir($d))!==false) { if(is_dir($t.$f) && file_exists($t.$f.'/template.html') && $f!="." && $f!="..") echo '<option value="'.$f.'">'.$f.'</option>'; }
	closedir($d);
	}
?>

<html>
<head>
<meta charset="utf-8" />
<meta name="robots" content="noindex" />
<title>CMSUno</title>
<script type="text/javascript" src="<?php echo $Udep; ?>includes/js/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
var Up=0,Udg=0,Usty=0,Uini=0,Utem=false,Uplug='',Uplugon=0,Unox='<?php echo $unox; ?>',Udep='<?php if(isset($Udep)) echo $Udep; else exit; ?>',Upt=[],Upd=[],Uplugact=[],Upluglist=[],UconfigFile=[],Ulang='<?php echo $lang; ?>',UconfigNum=0,Ubusy='',Uch=0;
function f_init(){
	var x=new XMLHttpRequest(),p='action=init&unox='+Unox;
	x.open('POST','uno/central.php',true);
	x.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	x.setRequestHeader('X-Requested-With','XMLHttpRequest');
	x.setRequestHeader('Content-length',p.length);
	x.setRequestHeader('Connection','close');
	x.onreadystatechange=function(){
		if(x.readyState==4&&x.status==200){
			var r=JSON.parse(x.responseText),k;
			if(r['pl'])for(k in r['pl']){
				Uplugact[k]=r['pl'][k];
			}
			if(r['ck'])for(k in r['ck']){
				UconfigFile[k]=r['ck'][k];
			}
			if(r['plugins'])for(k in r['plugins']){
				Upluglist[k]=r['plugins'][k];
			}
			Ubusy=r['nom'];
			Usty=r['sty'];
			Utem=r['tem'];
			var b=document.createElement('ul');
			b.id='menuSort';
			b.className='ui-sortable';
			for(k in r['chap']){
				Upt[k]=r['chap'][k]['t'];
				Upd[k]=r['chap'][k]['d'];
				var c=document.createElement('li');
				if(k==Up)c.className='bouton current off';
				else{
					c.className='bouton unsort';
					function f_clic(k,c){
						c.onclick=function(){
							if(Uch==1){
								f_alert('!<?php echo _("do not save ?");?>');
								Uch=0;
							}
							else{
								Up=k;
								f_get_site(1);
							}
						};
					};
					f_clic(k,c);
				}
				if(r['chap'][k]['od']==1)c.className+=' chapoff';
				c.innerHTML=r['chap'][k]['t'].replace(/\\/,"");
				b.appendChild(c);
			};
			var y=new XMLHttpRequest(),q='action=getChap&unox='+Unox+'&data='+Upd[Up];
			y.open('POST','uno/central.php',true);
			y.setRequestHeader('Content-type','application/x-www-form-urlencoded');
			y.setRequestHeader('X-Requested-With','XMLHttpRequest');
			y.setRequestHeader('Content-length',p.length);
			y.setRequestHeader('Connection','close');
			y.onreadystatechange=function(){
				if(y.readyState==4&&y.status==200){
					var s=y.responseText,a;
					if(s.length<3)r1+='-';
					a=s.substr(0,1);
					var ok=function(){
						if(Usty==0||!Utem)CKEDITOR.replace('content');
						else CKEDITOR.replace('content',{contentsCss:['uno/template/'+Utem+'/style.css','uno/template/'+Utem+'/styles.css','uno/template/'+Utem+'/css/style.css','uno/template/'+Utem+'/css/style.css']});
						CKEDITOR.instances['content'].setData(s.substr(1));
						CKEDITOR.instances['content'].on('change',function(){
							if(Udg==4){
								document.getElementById('boutonSauv').className="bouton danger";
								Udg=5;
								Uch=1;
							}
							else if(Udg<4)Udg++;
						});
						if(Upluglist.length!=0)document.getElementById('aplugin').style.display='inline';
						document.getElementById('titreChap').onkeypress=function(){
							document.getElementById('boutonSauv').className="bouton danger";
							Udg=1;
							Uch=1;
						};
						document.getElementById('aconfig').style.display='inline';
						document.getElementById('menu').appendChild(b);
						document.getElementById('optOnOff').className="onoff";
						if(r.pub)document.getElementById('boutonPub').style.display="inline";
						if(r.edw)document.getElementById('contentP').style.width=r.edw+'px';
						document.getElementById('titreChap').value=Upt[Up].replace(/\\/,"");
						if(a==1||a==3||a==5||a==7)document.getElementById('optTit').checked=true;
						else document.getElementById('optTit').checked=false;
						if(a==2||a==3||a==6||a==7)document.getElementById('optMenu').checked=true;
						else document.getElementById('optMenu').checked=false;
						if(a==4||a==5||a==6||a==7)document.getElementById('optDisp').checked=true;
						else document.getElementById('optDisp').checked=false;
						document.getElementById('chapOpt').style.display='none';
						Uini=1;
						Udg=0;
						document.getElementById('boutonSauv').className="bouton";
						jQuery(function(){
							jQuery("#menuSort").sortable({cancel:'.unsort',stop:function(){
								var b=document.getElementById('menuSort'),v;
								for(v=0;v<b.children.length;v++){
									if(b.children[v].className=='bouton current off'){
										jQuery.post('uno/central.php',{'action':'sauvePlace','unox':Unox,'chap':Up,'place':v},function(r){
											f_alert(r);
											Up=v;
											f_get_site(0);
										});
										break;
									}
								};
							}});
							jQuery("#menuSort").disableSelection();
						});
						if(r.nom)document.getElementById('avoir').href=r.nom+'.html';
					};
					if(document.readyState=='loading')document.addEventListener('DOMContentLoaded',function(event){ok();},false);
					else ok();
				}
			}
			y.send(q);
		}
	};
	x.send(p);
}
f_init();
</script>
<link rel="icon" type="image/png" href="<?php echo $Udep; ?>includes/img/favicon.png" />
<link rel="stylesheet" href="<?php echo $Udep; ?>includes/css/uno.css" />
</head>
<body>
	<div class="blocTop bgNoir">
		<div class="container">
			<span class="titre" href="/">CMSUno<? if(isset($Uversion)) echo '&nbsp;<em>'.$Uversion.'</em>'; ?></span>
			<div id="info"></div>
			<ul>
				<li id="wait"><img style="margin:2px 6px 0 0;" src="<?php echo $Udep; ?>includes/img/wait.gif" /></li>
				<li><a id="apage" style="text-decoration:underline" href=""><?php echo _("Page");?></a></li>
				<li><a id="aconfig" style="display:none;" onClick="f_config();" href="javascript:void(0)"><?php echo _("Settings");?></a></li>
				<li><a id="aplugin" style="display:none;" onClick="f_plugin(0);f_plugAll(document.getElementById('plugOnOff'),1)" href="javascript:void(0)"><?php echo _("Plugins");?></a></li>
				<li><a id="avoir" href="index.html" target="_blank"><?php echo _("See the website");?></a></li>
				<li><a id="alogout" onClick="f_logout();" href="javascript:void(0)"><?php echo _("Log out");?></a></li>
			</ul>
		</div>
	</div><!-- .blocTop-->

	<div id="chaps" class="container">
		<div class="blocBouton">
			<div class="bouton finder fr" id="boutonFinder0" onClick="f_elfinder(0)" title="<?php echo _("File manager");?>"><img src="<?php echo $Udep; ?>includes/img/finder.png" /></div>
			<div class="bouton fr" onClick="f_publier();" title="<?php echo _("Publish on the web all saved chapters");?>"><?php echo _("Publish");?></div>
			<div id="menu"></div>
		</div>
		<div id="finderDiv"></div>
		<div>
			<div class="input" id="contentP">
				<textarea name="content" id="content"></textarea>
			</div>
		</div>
		<div class="blocBouton" style="text-align:right;">
			<div id="optOnOff" class="onoff" onClick="f_chapOption(this);"></div>
			<div class="bouton fl" onClick="f_supp_chap();" title="<?php echo _("Remove this chapter and title");?>"><?php echo _("Delete Chapter");?></div>
			<span class="blocInput fl">
				<label class="label"><?php echo _("Chapter title");?>&nbsp;:</label>
				<input type="text" id="titreChap" name="titre" class="input" style="" />
			</span>
			<div class="bouton" onClick="f_nouv_chap();" title="<?php echo _("Inserts a chapter after this one. Have you saved ?");?>"><?php echo _("New chapter");?></div>
			<div class="bouton" id="boutonSauv" onClick="f_sauve_chap();" title="<?php echo _("Save this chapter and title");?>"><?php echo _("Save Chapter");?></div>
			<div class="bouton" id="boutonPub" onClick="f_publier();" title="<?php echo _("Publish on the web all saved chapters");?>"><?php echo _("Publish");?></div>
			<div id="chapOpt" class="chapOpt" style="position:relative;display:none;text-align:left;clear:both;">
				<div class="bouton fr" onClick="f_suppPub();" title="<?php echo _("Destroy the HTML file of this page (not the data)");?>"><?php echo _("Delete Publication");?></div>
				<div style="padding-top:12px;">
					<label><?php echo _("No Title");?></label><input type="checkbox" class="input" name="optTit" id="optTit" />
					<label><?php echo _("Not in menu");?></label><input type="checkbox" class="input" name="optMenu" id="optMenu" />
					<label><?php echo _("Hidden");?></label><input type="checkbox" class="input" name="optDisp" id="optDisp" />
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div><!-- .container -->
	<div id="config" class="container" style="display:none;">
		<div class="blocForm">
			<div class="bouton fr" onClick="f_publier();" title="<?php echo _("Publish on the web all saved chapters");?>"><?php echo _("Publish");?></div>
			<h2><?php 
			echo _("My Card"); ?></h2>
			<table class="hForm">
				<tr>
					<td><label><?php echo _("Page Title");?></label></td>
					<td><input type="text" class="input" name="tit" id="tit" onkeyup="f_ctit(this);" /><span id="ctit"></span></td>
					<td><em><?php echo _("Very important. The most important words at the beginning. 65 characters max.");?></em></td>
				</tr><tr>
					<td><label><?php echo _("Page Description");?></label></td>
					<td><input type="text" class="input" name="desc" id="desc" onkeyup="f_cdesc(this);" /><span id="cdesc"></span></td>
					<td><em><?php echo _("Important for attracting visitors. 156 characters max.");?></em></td>
				</tr><tr>
					<td><label><?php echo _("Base URL");?></label></td>
					<td><input type="text" class="input" name="url" id="url" /></td>
					<td><em><?php echo _("Base URL for this site (URL displayed by the browser without uno.php).");?></em></td>
				</tr><tr>
					<td><label><?php echo _("Filename");?></label></td>
					<td><input type="text" class="input" style="text-align:right;width:100px;" name="nom" id="nom" />.html</td>
					<td><em><?php echo _("Created file will be index.html by default.");?></em></td>
				</tr><tr>
					<td><label><?php echo _("E-mail");?></label></td>
					<td><input type="text" class="input" name="mel" id="mel" /></td>
					<td><em><?php echo _("Email address of the site administrator.");?></em></td>
				</tr><tr>
					<td><label><?php echo _("Theme");?></label></td>
					<td>
						<select name="tem" id="tem">
						<?php f_theme(); ?>
						
						</select>
					</td>
					<td><em><?php echo _("Theme to use for this page.");?></em></td>
				</tr>
			</table>
			<h2><?php echo _("Options");?></h2>
			<table class="hForm">
				<tr>
					<td><label><?php echo _("LazyLoad");?></label></td>
					<td><input type="checkbox" class="input" name="lazy" id="lazy" /></td>
					<td><em><?php echo _("Dynamic images loading (recommended)");?></em></td>
				</tr><tr>
					<td><label><?php echo _("Load JQuery");?></label></td>
					<td><input type="checkbox" class="input" name="jq" id="jq" /></td>
					<td><em><?php echo _("Javascript library useful for some plugins. (not recommended if not required)");?></em></td>
				</tr><tr>
					<td><label><?php echo _("CSS template");?></label></td>
					<td><input type="checkbox" class="input" name="sty" id="sty" /></td>
					<td><em><?php echo _("Same styles in the editor and page. Ref");?> : <span style='font-weight:700'>style.css</span> <?php echo _("or");?> <span style='font-weight:700'>styles.css</span> <?php echo _("in");?> <span style='font-weight:700'>template/</span> <?php echo _("or");?> <span style='font-weight:700'>template/css/</span>.</em></td>
				</tr><tr>
					<td><label><?php echo _("Width page");?> (px)</label></td>
					<td><input type="text" class="input" name="edw" id="edw" style="width:50px;" maxlength="4" onkeypress="return f_nombre(event)"/></td>
					<td><em><?php echo _("Adapt the editor width with the observed width of the HTML page. (960 by default)");?></em></td>
				</tr><tr>
					<td><label><?php echo _("Menu offset");?> (px)</label></td>
					<td><input type="text" class="input" name="ofs" id="ofs" style="width:50px;" maxlength="4" onkeypress="return f_nombre(event)"/></td>
					<td><em><?php echo _("Margin height upon arrival on a chapter after clicking on the menu (0 by default)");?></em></td>
				</tr>
			</table>
			<div class="bouton fr" id="boutonConfig" onClick="f_sauve_config();" title="<?php echo _("Saves settings");?>"><?php echo _("Save");?></div>
			<div class="clear"></div>
		</div>
		<div class="blocBouton">
			<div id="archOnOff" class="onoff" onClick="f_archOption(this);"></div>
			<div class="bouton fr" onClick="f_archivage();" title="<?php echo _("Save all the website");?>"><?php echo _("Make a backup");?></div>
			<div id="boutonRestaure" class="bouton fl" onClick="f_restaure(document.getElementById('archive').options[document.getElementById('archive').selectedIndex].value);" title="<?php echo _("Restore a backup (delete the current site)");?>"><?php echo _("Restore a backup");?></div>
			<div id="blocArchive"></div>
			<div id="archOpt" class="archOpt" style="position:relative;display:none;text-align:left;clear:both;">
				<div class="bouton" onClick="f_archDel(document.getElementById('archive').options[document.getElementById('archive').selectedIndex].value);" title="<?php echo _("Remove this backup");?>"><?php echo _("Remove this backup");?></div>
				<div class="bouton" onClick="f_archDownload(document.getElementById('archive').options[document.getElementById('archive').selectedIndex].value);" title="<?php echo _("Download this backup");?>"><?php echo _("Download this backup");?></div>
				<div class="bouton fr" onClick="f_fileDownload();" title="<?php echo _("Download all Finder files");?>"><?php echo _("Download all Finder files");?></div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="blocForm">
			<h2><?php echo _("Change Language");?></h2>
			<table class="hForm">
				<tr>
					<td><label><?php echo _("Language");?></label></td>
					<td>
						<select name="lang" id="lang">
						<?php foreach($langCode as $k=>$r) { echo "<option value='".$k."' ".(($lang==$k)?'selected':'').">".$k."</option>"; } ?>
						</select>
					</td>
					<td>
						<em><?php echo _("Language for the admin side of the site.");?></em>
						<div class="bouton" id="boutonPass" onClick="f_sauve_pass();" title="<?php echo _("Save");?>"><?php echo _("Save");?></div>
					</td>
				</tr>
			</table>
			<h2><?php echo _("Change User / Password");?></h2>
			<table class="hForm">
				<tr>
					<td><label><?php echo _("Current user");?></label></td>
					<td><input type="text" class="input" name="user0" id="user0" /></td>
					<td></td>
				</tr>
				<tr>
					<td><label><?php echo _("Current password");?></label></td>
					<td><input type="password" class="input" name="pass0" id="pass0" /></td>
					<td></td>
				</tr>
				<tr>
					<td><label><?php echo _("User");?></label></td>
					<td><input type="text" class="input" name="user" id="user" /></td>
					<td><em><?php echo _("Enter a nickname. Avoid words that are too simple (admin, user ...)");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Password");?></label></td>
					<td><input type="password" class="input" name="pass" id="pass" /></td>
					<td><em><?php echo _("Very important for the safety of the site. Use lowercase, uppercase and digit.");?></em></td>
				</tr>
				<tr>
					<td><label><?php echo _("Password");?></label></td>
					<td><input type="password" class="input" name="pass1" id="pass1" /></td>
					<td><em><?php echo _("Check. Re-enter the password.");?></em></td>
				</tr>
			</table>
			<div class="bouton fr" id="boutonPass" onClick="f_sauve_pass();" title="<?php echo _("Save password");?>"><?php echo _("Save");?></div>
			<div class="clear"></div>
		</div>
		<div class="blocForm">
			<div class="bouton fr" id="checkUpdate" onClick="f_checkUpdate();"><?php echo _("Check for update"); ?></div>
			<h2><?php echo _("Update");?></h2>
			<div id="updateDiv"></div>
		</div>
	</div><!-- .container -->
	<div id="plugins" class="container" style="display:none;">
		<div class="blocBouton">
			<div id="plugOnOff" class="onoff" onClick="f_plugAll(this);"></div>
			<div class="bouton finder fr" id="boutonFinder1" onClick="f_elfinder(1)" title="<?php echo _("File manager");?>"><img src="<?php echo $Udep; ?>includes/img/finder.png" /></div>
			<div class="bouton fr" onClick="f_publier();" title="<?php echo _("Publish on the web all saved chapters");?>"><?php echo _("Publish");?></div>
			<div id="listPlugins"></div>
		</div>
		<div id="finder1"></div>
		<div class="blocBouton">
			<div id="prePlugin" style="display:none;">
				<h1 id="nomPlug"></h1>
				<div>
					<input type="checkbox" class="input" onchange="f_onPlug(this)" id="onPlug" /><label></label>
				</div>
			</div>
		</div>
		<div id="plugin"></div>
	</div><!-- .container -->
	
<script type="text/javascript" src="<?php if($Udep=='uno/') echo 'uno/includes/js/jquery-2.1.4.min.js'; else echo 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js';?>"></script>
<script type="text/javascript" src="<?php if($Udep=='uno/') echo 'uno/includes/js/jquery-ui.min.js'; else echo 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js'; ?>"></script>
<script type="text/javascript" src="uno/includes/elfinder/js/elfinder.min.js"></script>
<?php if($lang!='en' && $lang!='') echo '<script type="text/javascript" src="uno/includes/elfinder/js/i18n/elfinder.'.$lang.'.js"></script>'; ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $Udep; ?>includes/css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" media="screen" href="uno/includes/elfinder/css/elfinder.min.css" />
<script type="text/javascript">
function f_get_site(f){
	jQuery.ajax({type:"POST",url:'uno/central.php',data:{'action':'getSite','unox':Unox},dataType:'json',async:true,success:function(r){
		var a=document.getElementById('menu'),b;
		Ubusy=r.nom;
		Usty=r.sty;
		Utem=r.tem;
		if(Up!=-1){
			jQuery("#menu").empty();
			b=document.createElement('ul');
			b.id='menuSort';
			b.className='ui-sortable';
			jQuery.each(r.chap,function(k,v){
				Upt[k]=v.t;
				Upd[k]=v.d;
				var c=document.createElement('li');
				if(k==Up)c.className='bouton current off';
				else{
					c.className='bouton unsort';
					c.onclick=function(){
						if(Uch==1){
							f_alert('!<?php echo _("do not save ?");?>');
							Uch=0;
						}
						else{
							Up=k;
							f_get_site(1);
						}
					};
				}
				if(v.od==1)c.className+=' chapoff';
				c.innerHTML=v.t.replace(/\\/,"");
				b.appendChild(c);
			});
			a.appendChild(b);
			jQuery(function(){
				jQuery("#menuSort").sortable({cancel:'.unsort',stop:function(){
					b=document.getElementById('menuSort');
					for(var v=0;v<b.children.length;v++){
						if(b.children[v].className=='bouton current off'){
							jQuery.post('uno/central.php',{'action':'sauvePlace','unox':Unox,'chap':Up,'place':v},function(r){
								f_alert(r);
								Up=v;
								f_get_site(0);
							});
							break;
						}
					};
				}});
				jQuery("#menuSort").disableSelection();
			});
			jQuery("input[name='titre']").val(Upt[Up].replace(/\\/,""));
			document.getElementById('optOnOff').className="onoff";
			if(r.pub)document.getElementById('boutonPub').style.display="inline";
			if(r.edw)document.getElementById('contentP').style.width=r.edw+'px';
			if(f!=0)f_get_chap(Up);
		}else{
			document.getElementById('tit').value=r.tit.replace(/\\/, "")||'';
			document.getElementById('desc').value=r.desc.replace(/\\/, "")||'';
			document.getElementById('nom').value=r.nom||'';
			document.getElementById('url').value=r.url||'<?php echo 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']); ?>';
			var t=document.getElementById("tem"),to=t.options,v;
			for(v=0;v<to.length;v++){
				if(to[v].value==r.tem){
					to[v].selected=true;v=to.length;
				}
			};
			document.getElementById('edw').value=r.edw||'';
			document.getElementById('ofs').value=r.ofs||'';
			if(r.lazy==1)document.getElementById('lazy').checked=true;
			else document.getElementById('lazy').checked=false;
			if(r.jq==1)document.getElementById('jq').checked=true;
			else document.getElementById('jq').checked=false;
			if(r.sty==1)document.getElementById('sty').checked=true;
			else document.getElementById('sty').checked=false;
			document.getElementById('mel').value=r.mel||'';
		}
		if(r.nom)document.getElementById('avoir').href=r.nom+'.html';
	}});
}
function f_get_chap(f){
	Up=f;
	jQuery.post('uno/central.php',{'action':'getChap','unox':Unox,'data':Upd[Up]},function(r1){
		if(r1.length<3)r1+='-';
		CKEDITOR.instances['content'].setData(r1.substr(1));
		var a=r1.substr(0,1);
		if(a==1||a==3||a==5||a==7)document.getElementById('optTit').checked=true;
		else document.getElementById('optTit').checked=false;
		if(a==2||a==3||a==6||a==7)document.getElementById('optMenu').checked=true;
		else document.getElementById('optMenu').checked=false;
		if(a==4||a==5||a==6||a==7)document.getElementById('optDisp').checked=true;
		else document.getElementById('optDisp').checked=false;
		document.getElementById('chapOpt').style.display='none';
		Uini=1;
		Udg=0;
		jQuery("#wait").hide();
		document.getElementById('boutonSauv').className="bouton";
	});
}
function f_sauve_chap(){
	jQuery.post('uno/central.php',{'action':'sauveChap','unox':Unox,'chap':Up,'data':Upd[Up],'content':CKEDITOR.instances['content'].getData(),'titre':document.getElementsByName('titre')[0].value,'otit':document.getElementById('optTit').checked,'omenu':document.getElementById('optMenu').checked,'odisp':document.getElementById('optDisp').checked,},function(r){
		f_alert(r);
		f_get_site(0);
		Udg=0;
		document.getElementById('boutonSauv').className="bouton";
		Uch=0;
	});
}
function f_sauve_config(){
	var nom=document.getElementById('nom').value;
	jQuery.post('uno/central.php',{'action':'sauveConfig','unox':Unox,'tit':document.getElementById('tit').value,'desc':document.getElementById('desc').value,'nom':nom,'mel':document.getElementById('mel').value,'tem':document.getElementById("tem").options[document.getElementById("tem").selectedIndex].value,'url':document.getElementById('url').value,'lazy':document.getElementById('lazy').checked,'jq':document.getElementById('jq').checked,'sty':document.getElementById('sty').checked,'edw':document.getElementById('edw').value,'ofs':document.getElementById('ofs').value},function(r){
		f_alert(r);
		if(nom.length>0)document.getElementById('avoir').href=nom+'.html';
	});
}
function f_sauve_pass(){
	if(document.getElementById('user0').value.length>0||document.getElementById('pass0').value.length>0||document.getElementById('user').value.length>0||document.getElementById('pass').value.length>0){
		if(document.getElementById('user0').value.length<1||document.getElementById('pass0').value.length<1){
			f_alert('!<?php echo _("Current elements are missing");?>');
			return;
		}
		if(document.getElementById('user').value.length<4){
			f_alert('!<?php echo _("Too short name");?>');
			return;
		}
		if(document.getElementById('pass').value!=document.getElementById('pass1').value){
			f_alert('!<?php echo _("Passwords are different");?>');
			return;
		}
		if(document.getElementById('pass').value.length<6){
			f_alert('!<?php echo _("Too short password");?>');
			return;
		}
	}
	jQuery.post('uno/central.php',{'action':'sauvePass','unox':Unox,'user0':document.getElementById('user0').value,'pass0':document.getElementById('pass0').value,'user':document.getElementById('user').value,'pass':document.getElementById('pass').value,'lang':document.getElementById('lang').options[document.getElementById('lang').selectedIndex].value},function(r){
		f_alert(r);
		if(r.substr(0,1)!="!")setTimeout(function(){
			location.reload();
		},1000);
	});
}
function f_nouv_chap(){
	jQuery.post('uno/central.php',{'action':'nouvChap','unox':Unox,'chap':Up,'data':Upd[Up]},function(r){
		Up++;
		f_alert(r);
		f_get_site(1);
	});
}
function f_supp_chap(){
	jQuery.post('uno/central.php',{'action':'suppChap','unox':Unox,'chap':Up,'data':Upd[Up]},function(r){
		if(Up>0)Up--;
		else Up=0;
		f_alert(r);
		f_get_site(1);
	});
}
function f_publier(){
	jQuery("#wait").show();
	jQuery.post('uno/central.php',{'action':'publier','unox':Unox},function(r){
		document.getElementById('boutonPub').style.display="none";
		f_alert(r);
	});
}
function f_suppPub(){
	jQuery.post('uno/central.php',{'action':'suppPub','unox':Unox},function(r){
		f_alert(r);
	});
}
function f_archivage(){
	jQuery("#wait").show();
	jQuery.post('uno/central.php',{'action':'archivage','unox':Unox},function(r){
		f_selectArchive();
		f_alert(r);
	});
}
function f_restaure(f){
	jQuery("#wait").show();
	jQuery.post('uno/central.php',{'action':'restaure','unox':Unox,'zip':f},function(r){
		f_alert(r);
	});
}
function f_archDel(f){
	jQuery("#wait").show();
	jQuery.post('uno/central.php',{'action':'archDel','unox':Unox,'zip':f},function(r){
		var t=document.getElementById("archive"),to=t.options,v;
		for(v=0;v<to.length;v++){
			if(to[v].value==f){
				t.removeChild(to[v]);
			}
		};
		f_alert(r);
	});
}
function f_archDownload(f){
	jQuery("#wait").show();
	jQuery.post('uno/central.php',{'action':'archDownload','unox':Unox,'zip':f},function(r){
		jQuery("#wait").hide();
		if(r.substr(0,1)=="!")f_alert(r);
		else window.location=r;
	});
}
function f_fileDownload(){
	jQuery("#wait").show();
	jQuery.post('uno/central.php',{'action':'filesDownload','unox':Unox},function(r){
		jQuery("#wait").hide();
		if(r.substr(0,1)=="!")f_alert(r);
		else window.location=r;
	});
}
function f_selectArchive(){
	jQuery.post('uno/central.php',{'action':'selectArchive','unox':Unox},function(r){
		if(r){
			document.getElementById('boutonRestaure').style.display="inline";
			document.getElementById('blocArchive').innerHTML=r;
		}
		else{
			document.getElementById('boutonRestaure').style.display="none";
			document.getElementById('blocArchive').innerHTML=''
		}
	});
}
function f_logout(){
	var a=document.getElementById('info'),b=document.createElement("form"),c=document.createElement("input");
	b.method="POST";
	b.action="";
	c.name="logout";
	c.type="hidden";
	c.value=1;
	b.appendChild(c);
	a.appendChild(b);
	b.submit();
}
function f_alert(f){
	if(f.search('<br />')!=-1){
		alert(f);
		jQuery.post('uno/central.php',{'action':'error','unox':Unox,'e':f});
	}
	else{
		var a=document.getElementById('info'),b=document.createElement("span");
		b.id="alert";
		if(f.substr(0,1)=="!"){
			b.style.color="red";
			f=f.substr(1);
		}
		b.innerHTML=f;
		a.appendChild(b);
		setTimeout(function(){
			jQuery("#alert").fadeOut("slow",function(){
				jQuery("#alert").remove();
			});
			jQuery("#info").empty();
		},2000);
	}
	jQuery("#wait").hide();
}
function f_config(){
	document.getElementById('plugins').style.display="none";
	document.getElementById('apage').style.textDecoration='none';
	document.getElementById('aplugin').style.textDecoration='none';
	document.getElementById('aconfig').style.textDecoration='underline';
	Up=-1;
	f_get_site(0);
	document.getElementById('chaps').style.display="none";
	document.getElementById('config').style.display="block";
	f_selectArchive();
	jQuery("#wait").hide();
}
function f_chapOption(f){
	var a=document.getElementById('chapOpt'),b;
	if(a.style.display=='none'){
		b='block';
		f.className='onoff all';
	}
	else{
		b='none';
		f.className='onoff';
	}
	a.style.display=b;
	window.scrollTo(0,document.body.scrollHeight);
}
function f_archOption(f){
	var a=document.getElementById('archOpt'),b;
	if(a.style.display=='none'){
		b='block';
		f.className='onoff all';
	}
	else{
		b='none';
		f.className='onoff';
	}
	a.style.display=b;
}
function f_plugins(){
	Up=-1;
	var a=document.getElementById('listPlugins');
	document.getElementById('config').style.display="none";
	document.getElementById('chaps').style.display="none";
	document.getElementById('plugins').style.display="block";
	document.getElementById('prePlugin').style.display='block';
	jQuery(a).empty();
	jQuery.each(Upluglist,function(k,v){
		var b=document.createElement("span");
		b.className="bouton";
		b.id="p"+v.substr(1);
		b.onclick=function(){
			f_plugin(v);
		};
		if(v=='9_')b.innerHTML='<?php echo _("My theme");?>';
		else b.innerHTML=v.charAt(1).toUpperCase()+v.substr(2);
		if(k==0){
			b.className="bouton current off";
			Uplugon=v.substr(0,1);
		}
		a.appendChild(b);
	});
}
function f_plugin(f){
	var a=document.getElementById('listPlugins'),d,v,c;
	if(f==0){
		f_plugins();
		document.getElementById('apage').style.textDecoration='none';
		document.getElementById('aplugin').style.textDecoration='underline';
		document.getElementById('aconfig').style.textDecoration='none';
		jQuery("#finderDiv").elfinder('close');
		document.getElementById('boutonFinder1').className="bouton finder fr";
		if(Uplugact[0])f='1'+Uplugact[0];
		else f=Uplugon+a.firstChild.id.substr(1);
	}
	d=a.childNodes;
	for(v=0;v<d.length;v++){
		if(d[v].id=="p"+f.substr(1))d[v].className="bouton current off";
		else d[v].className="bouton";
	};
	d=document.getElementById('onPlug');
	d.name=f.substr(1);
	c=f.substr(0,1);
	if(c=='1'){
		d.checked=true;
		d.nextSibling.innerHTML='<?php echo _("Enable");?>';
		d.nextSibling.style.color='green';
	}
	else if(c=='9'){
		d.checked=true;
		d.style.display='none';
		d.nextSibling.innerHTML='';
	}
	else{
		d.checked=false;
		d.nextSibling.innerHTML='<?php echo _("Disable");?>';
		d.nextSibling.style.color='#f79f81';
	}
	if(c=='9')document.getElementById('nomPlug').innerHTML='<?php echo _("My theme");?> : '+Utem;
	else document.getElementById('nomPlug').innerHTML='Plugin : '+f.substr(1);
	document.getElementById('plugin').innerHTML="";
	if(c!='9')jQuery.post('uno/plugins/'+f.substr(1)+'/'+f.substr(1)+'.php',{'action':'plugin','unox':Unox,'udep':Udep},function(r){
		document.getElementById('plugin').innerHTML=r;
		jQuery.getScript('uno/plugins/'+f.substr(1)+'/'+f.substr(1)+'.js');
		jQuery("#wait").hide();
	});
	else jQuery.post('uno/template/'+Utem+'/'+Utem+'.php',{'action':'plugin','unox':Unox,'udep':Udep},function(r){
		document.getElementById('plugin').innerHTML=r;
		jQuery.getScript('uno/template/'+Utem+'/'+Utem+'.js');
		jQuery("#wait").hide();
	});
}
function f_onPlug(f){
	if(f.checked){
		f.nextSibling.innerHTML='<?php echo _("Enable");?>';
		f.nextSibling.style.color='green';
	}
	else{
		f.nextSibling.innerHTML='<?php echo _("Disable");?>';
		f.nextSibling.style.color='#f79f81';
	}
	jQuery.post('uno/central.php',{'action':'onPlug','unox':Unox,'n':f.name,'c':f.checked},function(){
		f_plugin_hook();
	});
	var t=((f.checked)?"1":"0")+f.name;
	document.getElementById('p'+f.name).onclick=function(){
		f_plugin(t);
	};
	if(document.getElementById('plugOnOff').className.search('all')==-1)document.getElementById('p'+f.name).style.display=((f.checked)?"inline-block":"none");
}
function f_plugin_hook(){
	jQuery.ajax({type:"POST",url:'uno/central.php',data:{'action':'pluginsActifs','unox':Unox},dataType:'json',async:true,success:function(r){
		if(r.pl)jQuery.each(r.pl,function(k,v){
			Uplugact[k]=v;
		});
		if(r.ck)jQuery.each(r.ck,function(k,v){
			UconfigFile[k]=v;
		});
	}});
}
function f_plugAll(f,g){
	var b=0,v;
	if((f.className.search('all')!=-1)||g){
		f.className='onoff';
		jQuery("#listPlugins>span").hide();
		for(v=0;v<Uplugact.length;v++){
			if(Uplugact[v])document.getElementById('p'+Uplugact[v]).style.display='inline-block';
			b=1;
		};
	}
	if(b==0){
		f.className='onoff all';
		jQuery("#listPlugins>span").show();
	}
}
function f_ctit(f){
	var a=document.getElementById('ctit');
	if(f.value.length>65)a.style.color="red";
	else a.style.color="green";
	a.innerHTML=f.value.length;
}
function f_cdesc(f){
	var a=document.getElementById('cdesc');
	if(f.value.length>156)a.style.color="red";
	else a.style.color="green";
	a.innerHTML=f.value.length;
}
function f_elfinder(f){
	var a=document.getElementById('finderDiv');
	if(f==1)jQuery("#finderDiv").appendTo(jQuery("#finder1"));
	if(a.style.display!="block"){
		jQuery("#finderDiv").elfinder('open');
		document.getElementById('boutonFinder'+f).className="bouton finder fr current";
		return
	};
	jQuery("#finderDiv").elfinder('close');
	document.getElementById('boutonFinder'+f).className="bouton finder fr";
}
function f_finder_select(f){
	jQuery('<div \>').dialog({modal:true,width:"940px",title:"<?php echo _("Select a file");?>",zIndex: 9999,create:function(e,u){
		jQuery(this).elfinder({resizable:false,url:"uno/includes/elfinder/php/connector.php",useBrowserHistory:false,commandsOptions:{getfile:{oncomplete:'destroy'}},getFileCallback:function(file){
			document.getElementById(f).value=file.url;
			jQuery('a.ui-dialog-titlebar-close[role="button"]').click();
		}}).elfinder('instance')
	}});
}
function f_nombre(e){
	var c=(e.which)?e.which:event.keyCode;
	if(c>31&&(c<48||c>57))return false;
	return true;
}
function f_checkUpdate(){
	var a=document.getElementById('updateDiv'),b=0,c=document.createElement("table"),d='';
	jQuery("#wait").show();
	jQuery("#checkUpdate").hide();
	jQuery.post('uno/central.php',{'action':'checkUpdate','unox':Unox,'u':0},function(r){
		r=r.split('|');
		if(r[1]=='1')d='<div id="lighter" style="float:right"><?php echo _("Remove what is available online ? (recommended)"); ?> <span class="bouton" onClick="f_lighter();"><?php echo _("Lighten"); ?></span></div>';
		d+='<table><tr><td>CMSUno</td>';
		if(r[0]=='0')d+='<td><?php echo _("Up to date"); ?></td>';
		else d+='<td><span class="bouton danger" onClick="f_update(0);"><?php echo _("Update"); ?> '+r[2]+'</span></td>';
		d+='</tr></table>';
		d+='<hr />';
		a.innerHTML=d;
		a.appendChild(c);
		window.scrollTo(0,document.body.scrollHeight);
		jQuery.each(Upluglist,function(k,v){
			++b;
			if(v.substr(0,1)!='9')jQuery.post('uno/central.php',{'action':'checkUpdate','unox':Unox,'u':v.substr(1)},function(r){
				if(r.search('|')!=-1){
					r=r.split('|');
					if(r[0]!='1')c.innerHTML+='<tr><td>'+v.substr(1)+' : '+r[1]+'</td><td><?php echo _("Up to date"); ?></td></tr>';
					else c.innerHTML+='<tr id="T'+v.substr(1)+'"><td>'+v.substr(1)+' : '+r[1]+'</td><td><span class="bouton danger" onClick="f_update(\''+v.substr(1)+'\');"><?php echo _("Update"); ?> '+r[2]+'</span></td></tr>';
					window.scrollTo(0,document.body.scrollHeight);
				}
				--b;
				if(b==0)jQuery("#wait").hide();
			});
		});
		if(b==0)jQuery("#wait").hide();
	});
}
function f_update(f){
	jQuery("#wait").show();
	jQuery.post('uno/central.php',{'action':'update','unox':Unox,'u':f},function(r){
		if(r.search('|')!=-1){
			r=r.split('|');
			f_alert(r[0]);
			jQuery("#wait").hide();
			if(f!=0)document.getElementById('T'+f).innerHTML='<td>'+f+' : '+r[1]+'</td><td><div><?php echo _("Up to date"); ?></div></td>';
			else location.reload(true);
		}
	});
}
function f_lighter(){
	jQuery.post('uno/central.php',{'action':'lighter','unox':Unox},function(r){
		jQuery('#lighter').empty();
		f_alert(r);
	});
}
function f_extraJS(){
	jQuery.getScript(Udep+"includes/js/jqColorPicker.min.js");
}
//
window.scrollTo(0,0);
window.onload=function(){
	jQuery('#finderDiv').elfinder({lang:'<?php echo $lang;?>',url:'uno/includes/elfinder/php/connector.php',useBrowserHistory:false}).elfinder('instance');
	jQuery('#finderDiv').elfinder('close').css('zIndex',99);
	f_extraJS();
	jQuery("#wait").hide();
}
</script>
</body>
</html>
