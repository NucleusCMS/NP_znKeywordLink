<?php
	//var0.30  : 
	//             1) url'javascript:'
	//             2) 
	//             3) inputJavaScript
	//             4) SQLaddslashes
	//         : highlight
	//         : PreSkinParse()EUCUTF-8
	//         : 
	//ver0.22  : mysql_num_rows($qid)
	//ver0.21  : 
	//ver0.20    Logi
	//         : Blog
	//         : 
	//         : 
	//         : 
	//ver0.11  : 
	//ver0.10  : 
	//           EUCAndy
	//           targetcha_cya
	//ver0.02  : 
	//ver0.01  : 
class NP_znKeywordLink extends NucleusPlugin {
	function getName()           { return 'znKeywordLink'; }
	function getAuthor()         { return ''._ZNKLINK1.''; }
	function getURL()            { return 'http://wa.otesei.com/'; }
	function supportsFeature($w) { return ($w == 'SqlTablePrefix') ? 1 : 0; }
	function getTableList()      { return array( sql_table('plug_znKeywordLink'), sql_table( 'plug_znKeywordLink_item') ); }
	function getDescription()    { return ''._ZNKLINK2.''; }
	function getVersion()        {
		if (!$this->checkPluginOption('verCheck'))
			$this->createOption('verCheck', ''._ZNKLINK3.'', 'yesno', 'no'); //version check //vc
		return '0.30';
	}
	function getEventList()      {
		return array(
			'PreItem', 
			'QuickMenu', 
			'PostAddItem', 
			'PreUpdateItem', 
			'AddItemFormExtras', 
			'EditItemFormExtras', 
			'PostDeleteItem', 
			'PreSkinParse', 
			'PostPluginOptionsUpdate', //vc
		);
	}
	function init()              {
		// include language file for this plugin
		$language = str_replace( array('/','\\'), '', getLanguageName());
		$incFile  = (file_exists($this->getDirectory().$language.'.php')) ? $language : 'english';
		include_once($this->getDirectory().$incFile.'.php');
		$this->language = $incFile;
	}
	//
	//
	//
	function install(){
		$this->createOption('excl_blog', ''._ZNKLINK4.' 1,3','text','');
		$this->createOption('link_mode',''._ZNKLINK5.'','select','1',''._ZNKLINK6.'|1|'._ZNKLINK7.'|2');
		$this->createOption("hidd_form", ""._ZNKLINK8."", "yesno", "yes");
		$this->createOption("quickmenu", ""._ZNKLINK9."", "yesno", "no");
		$this->createOption("flg_table_drop", ""._ZNKLINK10."", "yesno", "no");
		mysql_query("CREATE TABLE IF NOT EXISTS ".sql_table("plug_znKeywordLink").
			" ( 
			`kid`     INT(11) NOT NULL AUTO_INCREMENT, 
			`kword`   TEXT    NOT NULL, 
			`klink`   TEXT    NOT NULL, 
			`korder`  INT(11) NOT NULL DEFAULT '50', 
			`ktarget` TINYINT NOT NULL, 
			PRIMARY KEY (kid)
			)");
		//0
		mysql_query("CREATE TABLE IF NOT EXISTS ".sql_table("plug_znKeywordLink_item").
			" ( 
			`litem` INT     NOT NULL, 
			`lmode` TINYINT NOT NULL, 
			PRIMARY KEY (litem)
			)");
		
		global $manager; //vc
		$manager->subscriptions['AdminPrePageFoot'][] = postVar('filename'); //vc
	}
	function event_PostPluginOptionsUpdate($data) {
		global $manager; //vc
		if ($data['context'] != 'global' || $data['plugid'] != $this->GetID() || $this->getOption('verCheck') != "yes") return; //vc
		$this->setOption('verCheck', 'no'); //vc
		$this->plugName = quickQuery('SELECT pfile result FROM '.sql_table('plugin').' WHERE pid='.intval($this->getID())); //vc
		$manager->subscriptions['AdminPrePageFoot'][] = $this->plugName; //vc
	}
	function event_AdminPrePageFoot(){ //vc
		$this->plugName = (postVar('filename')) ? postVar('filename') : $this->plugName; //vc
		$result         = $this->verCheck(); //vc
		echo '<span style="color: #'.(($result['version'] == $this->getVersion()) ? '00f' : 'f00').'">'.htmlspecialchars($result['message'], ENT_QUOTES).'</span>'; //vc
	}
	//
	//
	//
	function uninstall(){
		if ($this->getOption('flg_table_drop') == 'yes'){
			mysql_query("DROP table IF EXISTS ". sql_table("plug_znKeywordLink"));
			mysql_query("DROP table IF EXISTS ". sql_table("plug_znKeywordLink_item"));
		}
	}
	//
	//
	//
	function event_QuickMenu($data){
		// only show when option enabled
		if ($this->getOption('quickmenu') != 'yes') return;
		global $member;
		// only show to admins
		if (!($member->isLoggedIn() && $member->isAdmin())) return;
		array_push(
			$data['options'],
			array('title' => 'znKeywordLink', 'url' => $this->getAdminURL(),'tooltip' => ''._ZNKLINK11.'')
		);
	}
	function hasAdminArea(){ return 1; }
	//
	//
	//
	function event_PreSkinParse() {
		global $blogid;
		$this->lmode     = $this->getOption('link_mode');
		$excl_blog       = explode(",", $this->getOption('excl_blog'));
		$this->excl_blog = FALSE;
		foreach ($excl_blog as $value) if (trim($value) == $blogid) $this->excl_blog = TRUE;
	}
	//
	//
	//
	function event_AddItemFormExtras($data) {
		if ($this->getOption('hidd_form') == "no") return;
		?>
			<h3><?php echo _ZNKLINK12; ?></h3>
			<?php echo _ZNKLINK13; ?>
			<select name="lmode">
				<option value="0" selected>
					<?php echo _ZNKLINK14; ?>
				 (<?php echo ($this->getOption('link_mode') == 1) ? ""._ZNKLINK6."" : ""._ZNKLINK7.""; ?>) 
				</option>
				<option value="1"><?php echo _ZNKLINK6; ?></option>
				<option value="2"><?php echo _ZNKLINK7; ?></option>
				<option value="3"><?php echo _ZNKLINK15; ?></option>
			</select>
		<?php
	}
	//
	//
	//
	function event_EditItemFormExtras($data) {
		if ($this->getOption('hidd_form') == "no") return;
		$id  = intval($data['variables']['itemid']);
		$qid = mysql_query("SELECT * FROM ".sql_table("plug_znKeywordLink_item")." WHERE litem='".$id."'");
		$row = mysql_fetch_array($qid);
		?>
			<h3><?php echo _ZNKLINK12; ?></h3>
			<?php echo _ZNKLINK13; ?>
			<select name="lmode">
				<option value="0" <?php echo ($row["lmode"] == 0) ? "selected": ""; ?>>
					<?php echo _ZNKLINK14; ?>
				 (<?php echo ($this->getOption('link_mode') == 1) ? ""._ZNKLINK6."" : ""._ZNKLINK7.""; ?>) 
				</option>
				<option value="1" <?php echo ($row["lmode"] == 1) ? "selected": ""; ?>><?php echo _ZNKLINK6; ?></option>
				<option value="2" <?php echo ($row["lmode"] == 2) ? "selected": ""; ?>><?php echo _ZNKLINK7; ?></option>
				<option value="3" <?php echo ($row["lmode"] == 3) ? "selected": ""; ?>><?php echo _ZNKLINK15; ?></option>
			</select>
		<?php
	}
	//
	//
	//
	function event_PostAddItem($data) {
		if ($this->getOption('hidd_form') == "no") return;
		if (intRequestVar('lmode') == 0) return;                     //0
		$this->itemdataAdd($data['itemid'], intRequestVar('lmode')); //
	}
	//
	//
	//
	function event_PreUpdateItem($data) {
		if ($this->getOption('hidd_form') == "no") return;
		$litem   = intval($data["itemid"]);
		$sql_str = "SELECT * FROM ".sql_table("plug_znKeywordLink_item")." WHERE litem=".$litem;
		$qid = mysql_query($sql_str);
		if (mysql_num_rows($qid) == 1){                                         //
			if (intRequestVar('lmode') == 0) $this->itemdataDel($data['itemid']); //0
			else $this->itemdataUpd($data['itemid'], intRequestVar('lmode'));     //
		} else {                                                                //
			if (intRequestVar('lmode') == 0) return;                              //0
			$this->itemdataAdd($data['itemid'], intRequestVar('lmode'));          //
		}
	}
	//
	//
	//
	function event_PostDeleteItem($data) { $this->itemdataDel($data['itemid']); }
	//
	//
	//
	function itemdataAdd($itemid, $lmode){
		$itemid = intval($itemid);
		$lmode  = intval($lmode);
		mysql_query("INSERT INTO ".sql_table("plug_znKeywordLink_item")." SET litem=".$itemid.", lmode=".$lmode);
	}
	function itemdataUpd($itemid, $lmode){
		$itemid = intval($itemid);
		$lmode  = intval($lmode);
		mysql_query("UPDATE ".sql_table("plug_znKeywordLink_item")." SET lmode=".$lmode." WHERE litem=".$itemid);
	}
	function itemdataDel($itemid)        {
		$itemid = intval($itemid);
		mysql_query("DELETE FROM ".sql_table("plug_znKeywordLink_item")." WHERE litem=".$itemid);
	}
	//
	//
	//
	function array_set(){
		$qid = mysql_query("SELECT * FROM ".sql_table("plug_znKeywordLink")." ORDER BY korder");
		$this->search = array();
		$target = array("", " target='_blank'");
		$this->replace = array();
		while ($row = mysql_fetch_object($qid)){
			//quotemeta |{}/
			$temp = preg_replace(array("/\|/", "/\{/", "/\}/", "/\//"), array("\|", "\{", "\}", "\/"), quotemeta($row->kword));
			array_push( $this->search, mb_convert_encoding("/(".$temp.")/", 'UTF-8', _CHARSET) );
			$forSql__klink = preg_replace('/[\'"]/', '', $row->klink);
			$this->replace[] = mb_convert_encoding('<a href="'.$forSql__klink.'" class="znkwl"'.$target[$row->ktarget].' title="'.addslashes($row->kword).'">\0</a>', "UTF-8", _CHARSET);
		}
	}
	//
	//
	//
	function event_PreItem($data){
		if ($this->excl_blog) return;
		$litem   = intval($data['item']->itemid);
		$sql_str = "SELECT * FROM ".sql_table("plug_znKeywordLink_item")." WHERE litem=".$litem;
		$qid     = mysql_query($sql_str);
		if ($qid <> false){
			if (mysql_num_rows($qid) == 1){//if ($qid <> false){
				$row   = mysql_fetch_object($qid);
				$this->lmode = $row->lmode;
			}
		}
		if ($this->lmode == 3) return;
		if ($this->lmode == 2) unset($this->search_f);
		//echo "[".$this->lmode."]";
		
		if (!$this->search) $this->array_set();
		if(_CHARSET != 'UTF-8'){
			$temp = mb_convert_encoding($data['item']->body, 'UTF-8', _CHARSET);
			$temp = $this->addKeywordLink($this->search, $this->replace, $temp);
			$data['item']->body = mb_convert_encoding($temp, _CHARSET, 'UTF-8');
			
			$temp = mb_convert_encoding($data['item']->more, 'UTF-8', _CHARSET);
			$temp = $this->addKeywordLink($this->search, $this->replace, $temp);
			$data['item']->more = mb_convert_encoding($temp, _CHARSET, 'UTF-8');
		} else {
			$data['item']->body = $this->addKeywordLink($this->search, $this->replace, $data['item']->body);
			$data['item']->more = $this->addKeywordLink($this->search, $this->replace, $data['item']->more);
		}
		
	}
	//
	//
	//$this->lmode == 1 
	//$this->lmode == 2 
	//$this->lmode == 3 
	//
	function addKeywordLink($search, $replace, $body){
		if (!$replace || !$search) return $body;
		if (is_array($search) && (count($search) == 0)) return $body;
		
		$body = '<!--h-->'.$body;
		//preg_match_all('/(<[^>]+>)([^<>]*)/', $body, $matches);
		preg_match_all('/(<[^>]+>)([^<]*)/', $body, $matches);        //Nucleus highlight
		$result = '';
		for ($i = 0; $i < sizeof($matches[2]); $i++){
			if ($i != 0) $result .= $matches[1][$i];                    //<!--h-->
			if (strtolower(substr($matches[1][$i], 0, 2)) <> "<a"){     //<a>
				if (is_array($search)){                                   //
					for ($j = 0; $j < sizeof($search); $j++){               //
						if ($search[$j]) $matches[2][$i] = $this->addKeywordLink($search[$j], $replace[$j], $matches[2][$i]);
					}
					$result .= $matches[2][$i];
				} else {                                                  //
					if ($this->search_f[$search] <> 1 or $this->lmode <> 2){
						$result .= @preg_replace( $search, $replace, $matches[2][$i], (($this->lmode == 2) ? 1 : -1) ); // or 
						$this->search_f[$search] = @preg_match($search, $matches[2][$i]); //0 or 1
					} else $result .= $matches[2][$i];                      //
				}
			} else $result .= $matches[2][$i];                          //<a>
		}
		return $result;
	}
	//
	//
	//
	function checkPluginOption($optionName) {
		$pid   = intval($this->getID());
		$query = mysql_query("SELECT COUNT(oid) FROM ".sql_table('plugin_option_desc')." WHERE opid=".$pid." AND oname='".$optionName."'");
		$row   = mysql_fetch_array($query);
		return $row[0]; //0
	}
	//
	//JavaScript
	//
	function cnvHtmlUrlAttribute($forHtmlAtt__str)
	{
		//on
		$forHtmlAtt__str = preg_replace('/[\'"]/', '', $forHtmlAtt__str);
		
		//href="javascript:"
		$forHtmlAtt__str = preg_replace('/javascript/i', '', preg_replace('/[\x00-\x20\x22\x27]/', '', $forHtmlAtt__str));
		
		//
		return addslashes($forHtmlAtt__str);
	}
	//
	//Version Check Service(XML-RPC)
	//
	function verCheck()
	{
		global $DIR_LIBS;
		if (!class_exists(xmlrpcmsg)) include($DIR_LIBS . "xmlrpc.inc.php");
		$service = new xmlrpc_client('/xmlrpc/verCheckService.php', 'wa.otesei.com', 80);
		$para    = array(new xmlrpcval($this->plugName, 'string'), new xmlrpcval($this->getVersion(), 'string'));
		$res     = $service->send(new xmlrpcmsg('versioncheck.ping', $para), 20);
		if ($res && !$res->faultCode()){
			$struct  = $res->value();
			$version = $struct->structmem('version');
			$message = $struct->structmem('message');
			return array('version' => $version->scalarval(), 'message' => $message->scalarval());
		}
		return array('version' => '', 'message' => 'Version Check :: Error');
	}
}
?>
