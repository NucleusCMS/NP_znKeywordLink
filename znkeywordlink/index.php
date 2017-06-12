<?php

include('../../../config.php');
if (!$member->isLoggedIn()) doError("You\'re not logged in.");
include($DIR_LIBS . 'PLUGINADMIN.php');

class znKeywordLink_ADMIN {
	//
	//
	//
	function znKeywordLink_ADMIN() {
		$this->plugAdmin = new PluginAdmin('znKeywordLink');
		$this->plug      = & $this->plugAdmin->plugin;
		$this->plugname  = $this->plug->getName();
		$this->url       = $this->plug->getAdminURL();
		$this->table     = sql_table('plug_znKeywordLink');
	}
	//
	//
	//
	function verCheck() {
		//ver0.1  : ktarget 
		//ver0.02 : 
		//ver0.01 : 
		$res = sql_query("SHOW FIELDS FROM ".sql_table('plug_znKeywordLink') );
		$fieldnames = array();
		while ($co = mysql_fetch_assoc($res)) { $fieldnames[] = $co['Field']; }
		if (in_array('ktarget', $fieldnames)) return "ver0.1";
		return "ver0.02";
	}
	//
	//
	//
	function verUP() {
		$ver = $this->verCheck();
		switch ($ver) {
			case "ver0.02":
			$sql__query = "ALTER TABLE ".sql_table('plug_znKeywordLink')." ADD `ktarget` TINYINT NOT NULL";
		}
		if ($sql__query) {
			$flag__msg    = mysql_query($sql__query);
			$forHtml__msg = ($flag__msg) 
				? '('.$ver.') '._ZNKLINK16.'' 
				: '('.$ver.') '._ZNKLINK17.'';
			$forHtml__msg = '<span style="color: red;">'.$forHtml__msg.'</span>';
			echo $forHtml__msg;
		}
	}
	//
	//
	//
	function action_overview($forHtml__msg = '') {
		global $CONF, $manager;
		$this->plugAdmin->start();
		$this->verUP();
		if ($forHtml__msg) echo "<p>"._MESSAGE.": ".$forHtml__msg."</p>";
?>
		<h2><?php echo $this->plugname; ?></h2>
		<h3><?php echo _ZNKLINK18; ?></h3>
		<table>
			<thead><tr><th><?php echo _ZNKLINK19; ?></th><th><?php echo _ZNKLINK20; ?></th><th><?php echo _ZNKLINK21; ?></th><th><?php echo _ZNKLINK22; ?></th><th colspan='2'><?php echo _ZNKLINK23; ?></th></tr></thead>
			<tbody>
<?php
		$target = array(""._ZNKLINK24."", ""._ZNKLINK25."");
		$qid = mysql_query("SELECT * FROM ".$this->table." ORDER BY korder");
		while($row = mysql_fetch_array($qid)){
?>
				<tr onmouseover='focusRow(this);' onmouseout='blurRow(this);'>
					<td><?php echo htmlspecialchars($row['kword'], ENT_QUOTES); ?></td>
					<td><div style="overflow: auto;"><?php echo htmlspecialchars($row['klink'], ENT_QUOTES); ?></div></td>
					<td><?php echo $target[$row["ktarget"]]; ?></td>
					<td><?php echo $row['korder']; ?></td>
					<td nowrap="nowrap"><a href="<?php echo $this->url ?>index.php?action=sedit&amp;kid=<?php echo $row['kid']; ?>" tabindex="50"><?php echo _LISTS_EDIT ?></a></td>
					<td nowrap="nowrap"><a href="<?php echo $this->url ?>index.php?action=sdelete&amp;kid=<?php echo $row['kid']; ?>" tabindex="50"><?php echo _LISTS_DELETE ?></a></td>
				</tr>
<?php
		}
?>
			</tbody>
		</table>
		<h3><?php echo _ZNKLINK26; ?></h3>
		<form method="post" action="<?php echo $this->url ?>index.php">
			<div>
				<input name="action" value="snew" type="hidden" />
				<?php $manager->addTicketHidden(); ?>
				<table>
					<tr><td><?php echo _ZNKLINK19; ?></td><td><input name="kword" tabindex="10010" size="20" maxlength="200" /></td></tr>
					<tr><td><?php echo _ZNKLINK20; ?></td><td><input name="klink" tabindex="10020" size="60" maxlength="200" /></td></tr>
					<tr>
						<td><?php echo _ZNKLINK21; ?> (<?php echo _ZNKLINK27; ?>)</td>
						<td>
							<select name="ktarget" tabindex="10030">
								<option value="0"><?php echo _ZNKLINK24; ?> (<?php echo _ZNKLINK28; ?>) </option><option value="1"><?php echo _ZNKLINK29; ?></option>
							</select>
						</td>
					</tr>
					<tr><td><?php echo _ZNKLINK30; ?></td><td><input type="submit" tabindex="10040" value="<?php echo _ZNKLINK31; ?>" onclick="return checkSubmit();" /></td></tr>
				</table>
			</div>
		</form>
<?php
		$this->plugAdmin->end();
	}
	//
	//
	//
	function action_sedit($forHtml__msg = '') {
		global $manager;
		$qid = mysql_query("SELECT * FROM ".$this->table." WHERE kid=".intRequestVar('kid'));
		if ($row = mysql_fetch_object($qid)) {
			$this->plugAdmin->start();
			echo "<h2>".$this->plugname."</h2>";
			echo "<h3>"._ZNKLINK32."</h3>";
			if ($forHtml__msg) echo "<p>"._MESSAGE.": ".$forHtml__msg."</p>";
?>
			<form method="post" action="<?php echo $this->url ?>index.php">
				<div>
					<input type="hidden" name="action" value="supdate" />
					<input type="hidden" name="kid" value="<?php echo intRequestVar('kid'); ?>" />
					<?php $manager->addTicketHidden(); ?>
					<table>
						<tr>
							<td><?php echo _ZNKLINK19; ?></td>
							<td><input name="kword" tabindex="10010" maxlength="20" size="20" value="<?php echo htmlspecialchars($row->kword, ENT_QUOTES); ?>" /></td>
						</tr>
						<tr>
							<td><?php echo _ZNKLINK20; ?>URL</td>
							<td><input name="klink" tabindex="10020" size="60" maxlength="200" value="<?php echo htmlspecialchars($row->klink, ENT_QUOTES); ?>" /></td>
						</tr>
						<tr>
							<td><?php echo _ZNKLINK21; ?> (<?php echo _ZNKLINK27; ?>) </td>
							<td>
								<select name="ktarget" tabindex="10030">
									<option value="0" <?php echo ($row->ktarget == 0) ? "selected" : ""; ?>><?php echo _ZNKLINK24; ?> (<?php echo _ZNKLINK28; ?>)</option>
									<option value="1" <?php echo ($row->ktarget == 1) ? "selected" : ""; ?>><?php echo _ZNKLINK29; ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td><?php echo _ZNKLINK33; ?></td>
							<td><input type="submit" tabindex="10040" value="<?php echo _ZNKLINK34; ?>" onclick="return checkSubmit();" /></td>
						</tr>
					</table>
				</div>
			</form>
<?php
			$this->plugAdmin->end();
		} else $this->error(""._ZNKLINK35."");
	}
	//
	//
	//
	function action_sdelete() {
		global $manager;
		$this->plugAdmin->start();
		$qid = mysql_query("SELECT * FROM ".$this->table." WHERE kid=".intRequestVar('kid'));
		$row = mysql_fetch_object($qid);
?>
			<h2><?php echo _DELETE_CONFIRM?></h2>
			<form method="post" action="<?php echo $this->url ?>index.php">
				<div>
					<input type="hidden" name="action" value="sdeleteconfirm" />
					<input type="hidden" name="kid" value="<?php echo intRequestVar('kid'); ?>" />
					<?php $manager->addTicketHidden(); ?>
					<input type="submit" tabindex="10" value="<?php echo _DELETE_CONFIRM_BTN ?>" />
					<?php echo $row->kword; ?>
				</div>
			</form>
<?php
		$this->plugAdmin->end();
	}
	//
	//
	//
	function action_snew() {
		$forSql__kword   = addslashes(requestVar('kword'));
		$forSql__klink   = $this->plug->cnvHtmlUrlAttribute(requestVar('klink'));
		$forSql__korder  = $this->kw_order($forSql__kword);
		$forSql__ktarget = intRequestVar('ktarget');
		if ($forSql__kword != '' && $forSql__klink != ''){
			$sql__query = "INSERT INTO ".$this->table." SET ".
				"kword  ='".$forSql__kword  ."', ".
				"klink  ='".$forSql__klink  ."', ".
				"korder ='".$forSql__korder ."', ".
				"ktarget= ".$forSql__ktarget;
			$flag__msg = mysql_query($sql__query);
		} else $flag__msg = FALSE;
		$forHtml__msg = ($flag__msg) ? ''._ZNKLINK36.'' : '<span style="color: red;">'._ZNKLINK37.'</span>';
		$this->action_overview($forHtml__msg);
	}
	//
	//
	//
	function action_supdate() {
		$forSql__kword   = addslashes(requestVar('kword'));
		$forSql__klink   = $this->plug->cnvHtmlUrlAttribute(requestVar('klink'));
		$forSql__korder  = (int) $this->kw_order($forSql__kword, intRequestVar('kid'));
		$forSql__ktarget = intRequestVar('ktarget');
		$forSql__kid     = intRequestVar('kid');
		if ($forSql__kword != '' && $forSql__klink != ''){
			$sql__query = "UPDATE ".$this->table." SET ".
				"kword  ='".$forSql__kword."', ".
				"klink  ='".$forSql__klink."', ".
				"korder ='".$forSql__korder."', ".
				"ktarget= ".$forSql__ktarget." ".
				"WHERE kid=".$forSql__kid;
			$flag__msg = mysql_query($sql__query);
		} else $flag__msg = FALSE;
		$forHtml__msg = ($flag__msg) ? ''._ZNKLINK38.'' : '<span style="color: red;">'._ZNKLINK39.'</span>';
		$this->action_overview($forHtml__msg);
	}
	//
	//
	//
	function action_sdeleteconfirm() {
		global $manager;
		$flag__msg = mysql_query("DELETE FROM ".$this->table." WHERE kid=".intRequestVar('kid'));
		$forHtml__msg = ($flag__msg) ? ''._ZNKLINK40.'' : '<span style="color: red;">'._ZNKLINK41.'</span>';
		$this->action_overview($forHtml__msg);
	}
	//
	//$kword
	//
	function kw_order($kword, $forSql__kid=0){
		$korder      = 50;
		$kword       = mb_convert_encoding($kword, 'UTF-8', _CHARSET);
		$forSql__kid = intval($forSql__kid);
		$qid         = mysql_query("SELECT * FROM ".$this->table." WHERE kid<>'".$forSql__kid."' ORDER BY kword DESC");
		while ($row = mysql_fetch_object($qid)) {
			//quotemeta |{}/
			$temp = "/".preg_replace(array("/\|/", "/\{/", "/\}/", "/\//"), array("\|", "\{", "\}", "\/"), quotemeta($row->kword))."/";
			$temp = mb_convert_encoding($temp, 'UTF-8', _CHARSET);
			if (preg_match($temp, $kword)) $korder = $row->korder - 1;
		}
		return (int) $korder;
	}
	//
	//
	//
	function disallow(){
		global $HTTP_SERVER_VARS;
		ACTIONLOG::add(WARNING, _ACTIONLOG_DISALLOWED . $HTTP_SERVER_VARS['REQUEST_URI']);
		$this->error(_ERROR_DISALLOWED);
	}
	//
	//
	//
	function action($action) {
		global $manager, $member;
		$member->isAdmin() or $this->disallow();
		$methodName         = 'action_' . $action;
		$aActionsNotToCheck = array( //
			'overview', 
			'sedit', 
			'sdelete', 
		);
		if (!in_array(strtolower($action), $aActionsNotToCheck)) if (!$manager->checkTicket()) $this->error(_ERROR_BADTICKET);
		if (method_exists($this, $methodName)) call_user_func(array(&$this, $methodName)); else $this->error(_BADACTION . " ($action)");
	}
	//
	//
	//
	function error($forHtml__msg) {
		$this->plugAdmin->start();
		echo "<h2>Error!</h2>".$forHtml__msg."<br />";
		echo "<a href='".$this->url."index.php' onclick='history.back()'>"._BACK."</a>";
		$this->plugAdmin->end();
		exit;
	}
}

$myAdmin = new znKeywordLink_ADMIN();
$myAdmin->action((requestVar('action')) ? requestVar('action') : 'overview');

?>
