<?php
if( !defined('ABSPATH') ) exit;

function zipaddr_admin_menu(){                    // 設定メニュー下にサブメニューを追加
	add_menu_page( 'Zipaddr-JP', 'Zipaddr-JP', 'activate_plugins', zipaddr_KEYS, 'zipaddr_conf');
	add_submenu_page(zipaddr_KEYS, __('option','zipaddr-jp'),__('option','zipaddr-jp'), 'activate_plugins', zipaddr_KEYS, 'zipaddr_conf');
}
function zipaddr_radio($iname,$selected,$table){
$cdata1=  '<label><input name="{iname}" id="{iname}_{n}" type="radio" value="{key}"{select} />';
$cdata1.= '{data}</label><br />'."\n";
	$ans= "";
	$n= 0;
	foreach($table as $key => $data) {
		$select= ($key==$selected) ?  " checked" : "";
		$n++;
		$dat= str_replace("{iname}", $iname, $cdata1);
		$dat= str_replace("{n}",     $n,     $dat);
		$dat= str_replace("{select}",$select,$dat);
		$dat= str_replace("{key}",   $key,   $dat);
		$dat= str_replace("{data}",  $data,  $dat);
		$ans.= $dat;
	}
	return $ans;
}
function zipaddr_conf(){
	if( !empty($_GET['page']) && $_GET['page'] == zipaddr_KEYS ){
		if( isset($_POST['zipaddr_token']) && !empty($_POST['zipaddr_token']) ){
			check_admin_referer('zipaddrjp', 'zipaddrjp_def');
			$array= array();
			$flds= zipaddrjp_fld();
			foreach($flds as $i => $key){
				$keys= zipaddr_SYS.$key;          // sys_site
				$da= isset($_POST[$keys]) ?  sanitize_text_field(wp_unslash($_POST[$keys])) : "";
				if( $key=="plce" && $da=="&nbsp;" ) {;}
				else $da= htmlspecialchars($da,ENT_QUOTES,'UTF-8');
				$array[$keys]= $da;
				$$keys= $da;
			}
			if( $sys_site < "1" || "5" < $sys_site )    $array[zipaddr_SYS.'site']= "4";
			if( $sys_keta < "5" || "7" < $sys_keta )    $array[zipaddr_SYS.'keta']= "7";
//			if( $sys_site == "4" )                      $array[zipaddr_SYS.'keta']= "7";
			if( $sys_site == "2" )                      $array[zipaddr_SYS.'keta']= "5";
			else                                        $array[zipaddr_SYS.'keta']= "7";
			if( $sys_deli=="" || $sys_deli=="-" ){;}
			else                                        $array[zipaddr_SYS.'deli']= "-";
			if( !preg_match("/^[0-9\-]+$/",$sys_tate) ) $array[zipaddr_SYS.'tate']= "";
			if( !preg_match("/^[0-9\-]+$/",$sys_yoko) ) $array[zipaddr_SYS.'yoko']= "";
			if( !preg_match("/^[0-9\-]+$/",$sys_pfon) ) $array[zipaddr_SYS.'pfon']= "";
			if( !preg_match("/^[0-9\-]+$/",$sys_sfon) ) $array[zipaddr_SYS.'sfon']= "";
			if( $sys_dyna=="" || $sys_dyna=="1" ){;}
			else                                        $array[zipaddr_SYS.'dyna']= "";
			$array[zipaddr_SYS.'parm']= str_replace(",", "|", $sys_parm);
			$param= serialize($array);
			update_option(zipaddr_DEFINE,$param); // 定義情報を更新する
			$mesg= "稼働環境を設定しました。";
		}
		else $mesg= "【稼働環境の設定】";
//
		$title= array();
		$title[0]= "[必須]";
		$title[1]= "▼郵番DBの稼働環境選択";
		$title[2]= "利用サイト";
		$title[3]= "郵便番号の区切り文字";
		$title[4]= "ガイダンス位置の補正";
		$title[5]= "ガイダンス画面の文字サイズ";
		$title[6]= "フッター表示オプション";
		$title[7]= "placeholder上書（m:住所自動入力）";
		$title[8]= "選択後にフォーカスするid名";
		$title[9]= "システム拡張用のAP識別子";
		$title[10]="htmlの動的生成（1:有効）";
		$title[11]="オウンコード設定パラメータ";
		$title[12]="無条件挿入（;区切でurlのkeyword）";
		$comnt= array();
		$comnt[1]= "縦";
		$comnt[2]= "横";
		$comnt[3]= "（@ZipAddr_xx部分）";
		$rei= "例：";
//
		$flds= zipaddrjp_fld(); foreach($flds as $i => $key){$keys=zipaddr_SYS.$key; $$keys="";}
		$param= unserialize( get_option(zipaddr_DEFINE) ); // get定義情報
		foreach((array)$param as $key => $data){
			$da= ($data=="&nbsp;") ?  $data : htmlspecialchars($data,ENT_QUOTES,'UTF-8');
			$$key= $da;
		}
		if( empty($sys_site) ) $sys_site= "4";
		if( empty($sys_keta) ) $sys_keta= "7";
		if( $sys_site == "1" ) $sys_site= "4";
		if( $sys_keta < 5 ||  7 < $sys_keta ) $sys_keta= "7";
		if( $sys_pfon < 9 || 25 < $sys_pfon ) $sys_pfon= "";
		if( $sys_sfon < 9 || 25 < $sys_sfon ) $sys_sfon= "";
//		$sit= array("4" => "商用版サイト（default）","5" => "商用版サイト（zipaddra.js）","2" => "有償版ガイダンス有り","3" => "有償版ガイダンス無し");
//		$gid= array(""  => "表示（default）","1" => "非表示");
//		$site= zipaddr_radio("sys_site", $sys_site, $sit);
//		$gide= zipaddr_radio("sys_gide", $sys_gide, $gid);
		$radio= array();
		$radio[1]= "商用版サイト（default）";
		$radio[2]= "商用版サイト（zipaddra.js）";
		$radio[3]= "有償版ガイダンス有り";
		$radio[4]= "有償版ガイダンス無し";
		$ky1= explode("_", "4_5_2_3");
		$selct= array();                          // sys_site
		for( $j=0;$j<count($ky1);$j++ ){$selct[$j]= ($ky1[$j] == $sys_site) ?  "checked " : "";} //利用サイト_radio
		$ky2= explode("_", "_1");
		$serct= array();                          // sys_gide
		for( $j=0;$j<count($ky2);$j++ ){$serct[$j]= ($ky2[$j] == $sys_gide) ?  "checked " : "";} //利用サイト_radio
		$param= str_replace("|", ",", $sys_parm);
		$token= zipaddrjp_str();                  // token
?>
<h2><?php zipaddrjp_e($mesg) ?>(zipaddr-jp)&nbsp;V<?php zipaddrjp_e(zipaddr_VERS) ?></h2>
<form id="zipaddr-conf" method="post" action="">
<table border="1" cellspacing="0" cellpadding="8" summary=" ">
    <tr><td colspan="2" width="90" bgcolor="#f3f3f3"><?php zipaddrjp_e($title[1]) ?></td>
    </tr>
    <tr ><td bgcolor="#f3f3f3"><?php zipaddrjp_e($title[2]) ?><span style="color: #ff0000;">&nbsp;<?php zipaddrjp_e($title[0]) ?></span></td>
<td>
<label><input name="sys_site" id="sys_site_1" type="radio" value="4" <?php zipaddrjp_e($selct[0]) ?>/><?php zipaddrjp_e($radio[1]) ?></label><br />
<label><input name="sys_site" id="sys_site_2" type="radio" value="5" <?php zipaddrjp_e($selct[1]) ?>/><?php zipaddrjp_e($radio[2]) ?></label><br />
<label><input name="sys_site" id="sys_site_3" type="radio" value="2" <?php zipaddrjp_e($selct[2]) ?>/><?php zipaddrjp_e($radio[3]) ?></label><br />
<label><input name="sys_site" id="sys_site_4" type="radio" value="3" <?php zipaddrjp_e($selct[3]) ?>/><?php zipaddrjp_e($radio[4]) ?></label><br />
</td>
    </tr>
    <tr><td bgcolor="#f3f3f3"><?php zipaddrjp_e($title[3]) ?></td>
        <td><input type="text" name="sys_deli" size="5" maxlength="1" value="<?php zipaddrjp_e($sys_deli) ?>" style="ime-mode:disabled;" />&nbsp;&nbsp;(default: '-')</td>
    </tr>
    <tr><td bgcolor="#f3f3f3"><?php zipaddrjp_e($title[4]) ?></td>
        <td>
<?php zipaddrjp_e($comnt[1]) ?>:&nbsp;<input type="text" name="sys_tate" size="5" maxlength="4" value="<?php zipaddrjp_e($sys_tate) ?>" style="ime-mode:disabled;" />&nbsp;&nbsp;(default: 18)<br />
<?php zipaddrjp_e($comnt[2]) ?>:&nbsp;<input type="text" name="sys_yoko" size="5" maxlength="4" value="<?php zipaddrjp_e($sys_yoko) ?>" style="ime-mode:disabled;" />&nbsp;&nbsp;(default: 22)
        </td>
    </tr>
    <tr><td bgcolor="#f3f3f3"><?php zipaddrjp_e($title[5]) ?></td>
        <td>
PC:&nbsp;<input type="text" name="sys_pfon" size="5" maxlength="4" value="<?php zipaddrjp_e($sys_pfon) ?>" style="ime-mode:disabled;" />&nbsp;&nbsp;(default: 12)<br />
SF:&nbsp;<input type="text" name="sys_sfon" size="5" maxlength="4" value="<?php zipaddrjp_e($sys_sfon) ?>" style="ime-mode:disabled;" />&nbsp;&nbsp;(default: 20)
        </td>
    </tr>
    <tr ><td bgcolor="#f3f3f3"><?php zipaddrjp_e($title[6]) ?><br /><?php zipaddrjp_e($comnt[3]) ?></td>
<td>
<label><input name="sys_gide" id="sys_gide_1" type="radio" value=""  <?php zipaddrjp_e($serct[0]) ?>/>表示（default）</label><br />
<label><input name="sys_gide" id="sys_gide_2" type="radio" value="1" <?php zipaddrjp_e($serct[1]) ?>/>非表示</label><br />
</td>
    </tr>
    <tr><td bgcolor="#f3f3f3"><?php zipaddrjp_e($title[7]) ?></td>
        <td><input type="text" name="sys_plce" size="25" maxlength="50" value="<?php zipaddrjp_e($sys_plce) ?>" /></td>
    </tr>
    <tr><td bgcolor="#f3f3f3"><?php zipaddrjp_e($title[8]) ?></td>
        <td><input type="text" name="sys_focs" size="25" maxlength="50" value="<?php zipaddrjp_e($sys_focs) ?>" /></td>
    </tr>
    <tr><td bgcolor="#f3f3f3"><?php zipaddrjp_e($title[9]) ?></td>
        <td><input type="text" name="sys_syid" size="25" maxlength="50" value="<?php zipaddrjp_e($sys_syid) ?>" /></td>
    </tr>
    <tr><td bgcolor="#f3f3f3"><?php zipaddrjp_e($title[10]) ?></td>
        <td><input type="text" name="sys_dyna" size="25" maxlength="50" value="<?php zipaddrjp_e($sys_dyna) ?>" /></td>
    </tr>
    <tr><td bgcolor="#f3f3f3"><?php zipaddrjp_e($title[11]) ?></td>
        <td><input type="text" name="sys_parm" size="25" maxlength="255" value="<?php zipaddrjp_e($param) ?>" placeholder="<?php zipaddrjp_e($rei) ?>bgc=#3366ff,rtrs=" /></td>
    </tr>
    <tr><td bgcolor="#f3f3f3"><?php zipaddrjp_e($title[12]) ?></td>
        <td><input type="text" name="sys_drct" size="25" maxlength="50" value="<?php zipaddrjp_e($sys_drct) ?>" placeholder="<?php zipaddrjp_e($rei) ?>/contact" /></td>
    </tr>
</table>
<br />
<?php
	$footer= array();
	$footer[1]= "▼郵便番号DBの稼働場所は、次の2系統があります。";
	$footer[2]= "　商用版サイト： ".zipaddr_COM." 系";
	$footer[3]= "　有償版サイト： ".zipaddr2COM." 系";
	$footer[4]= "";
	$footer[5]= "※有償版のご利用には別途、";
	$footer[6]= "▼[システム拡張AP識別子（'_'区切り）]";
	$footer[7]= "▼以下は自動判定します。";
	$footer[8]= "利用申請が必要となります。";
	$value=  "この内容で登録する";
?>
<?php zipaddrjp_e($footer[1]) ?><br />
<?php zipaddrjp_e($footer[2]) ?><br />
<?php zipaddrjp_e($footer[3]) ?><br />

<?php zipaddrjp_e($footer[5]) ?><a href="<?php zipaddrjp_e(zipaddr2COM) ?>use/" target="_blank"><?php zipaddrjp_e($footer[8]) ?></a><br />
<?php zipaddrjp_e($footer[6]) ?><br />
<?php zipaddrjp_e($footer[7]) ?><br />
&nbsp;&nbsp;Contact Form 7/&nbsp;MW WP Forme/&nbsp;Trust Form/&nbsp;Ninja Forms/&nbsp;WP-Members/&nbsp;WPForms<br />
&nbsp;&nbsp;WooCommerce/&nbsp;Welcart/&nbsp;Mailform Pro/&nbsp;Forminator/&nbsp;TieredWorks/&nbsp;booking-package<br />
&nbsp;&nbsp;SnowMonkeyForm/&nbsp;Visual Form Builder<br />
<br />
<div class="btn-area">
	<ul><li>
		<input type="hidden" name="zipaddr_token" value="<?php zipaddrjp_e($token) ?>" />
		<?php wp_nonce_field( 'zipaddrjp', 'zipaddrjp_def' ); ?>
		<input name="submit" id="submit" class="button button-primary" type="submit" value="<?php zipaddrjp_e($value) ?>" />
	</li></ul>
</div>
</form>
<?php
	}
}
?>
