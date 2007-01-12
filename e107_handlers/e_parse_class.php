<?php
/*
+ ----------------------------------------------------------------------------+
|     e107 website system
|
|      Steve Dunstan 2001-2002
|     http://e107.org
|     jalist@e107.org
|
|     Released under the terms and conditions of the
|     GNU General Public License (http://gnu.org).
|
|     $Source: /cvs_backup/e107_0.8/e107_handlers/e_parse_class.php,v $
|     $Revision: 1.2 $
|     $Date: 2007-01-12 21:05:20 $
|     $Author: sweetas $
+----------------------------------------------------------------------------+
*/
if (!defined('e107_INIT')) { exit; }

define ("E_NL", chr(2));
class e_parse
{
	var $e_sc;
	var $e_bb;
	var $e_pf;
	var $e_emote;
	var $e_hook;
	var $search = array('&#39;', '&#039;', '&quot;', 'onerror', '&gt;', '&amp;#039;', '&amp;quot;');
	var $replace = array("'", "'", '"', 'one<i></i>rror', '>', "'", '"');
	var $e_highlighting;
	var $e_query;

	function toDB($data, $nostrip = false, $no_encode = false, $original_author = false, $mod = false)
	{
		/**
		* $nostrip: toDB() assumes all data is GPC ($_GET, $_POST, $_COOKIE) unless you indicate otherwise by setting this var to true.
		* If magic quotes is enabled on the server and you do not tell toDB() that the data is non GPC then slashes will be stripped when they should not be.
		* $no_encode: This var should nearly always be false. It is used by the save_prefs() function to preserve html content within prefs even when 
		* the save_prefs() function has been called by a non admin user / user without html posting permissions.
		* $mod: although not used in core, the 'no_html' and 'no_php' modifiers are available for plugins to blanket prevent html and php posting regardless 
		* of posting permissions.
		*/
		global $pref;
		if (is_array($data)) {
			foreach ($data as $key => $var) {
				$ret[$key] = $this -> toDB($var, $nostrip, $no_encode, $original_author, $mod);
			}
		} else {
			if (MAGIC_QUOTES_GPC == true && $nostrip == false) {
				$data = stripslashes($data);
			}
			if (isset($pref['post_html']) && check_class($pref['post_html']))
			{
				$no_encode = true;
			}
			if (is_numeric($original_author) && !check_class($pref['post_html'], '', $original_author))
			{
				$no_encode = false;
			}
			if ($no_encode === true && strpos($mod, 'no_html') === false)
			{
				$search = array('$', '"', "'", '\\', '<?');
				$replace = array('&#036;','&quot;','&#039;', '&#092;', '&lt;?');
				$ret = str_replace($search, $replace, $data);
			} else {
				$data = htmlspecialchars($data, ENT_QUOTES, CHARSET);
				$data = str_replace('\\', '&#092;', $data);
				$ret = preg_replace("/&amp;#(\d*?);/", "&#\\1;", $data);
			}
			if (!check_class($pref['php_bbcode']) || (is_numeric($original_author) && !check_class($pref['php_bbcode'], '', $original_author)) || strpos($mod, 'no_php') !== false)
			{
				$ret = str_replace(array("[php]", "[/php]"), array("&#91;php&#93;", "&#91;/php&#93;"), $ret);
			}

		}

		return $ret;
	}


	function toForm($text)
	{
		if ($text == '') { return ''; }
		$search = array('&#036;', '&quot;', '<', '>');
		$replace = array('$', '"', '&lt;', '&gt;');
		$text = str_replace($search, $replace, $text);
		if (e_WYSIWYG !== true){
	   	  	$text = str_replace("&nbsp;", " ", $text); // fix for utf-8 issue with html_entity_decode();
		}
		return $text;
	}


	function post_toForm($text) {
		if (MAGIC_QUOTES_GPC == true) {
			$text = stripslashes($text);
		}
		return str_replace(array( "'", '"', "<", ">"), array("&#039;", "&quot;", "&lt;", "&gt;"), $text);
	}


	function post_toHTML($text, $original_author = false, $extra = '', $mod = false) {
		$text = $this -> toDB($text, false, false, $original_author, $mod);
		return $this -> toHTML($text, true, $extra);
	}


	function parseTemplate($text, $parseSCFiles = TRUE, $extraCodes = "") {
		if (!is_object($this->e_sc))
		{
			require_once(e_HANDLER."shortcode_handler.php");
			$this->e_sc = new e_shortcode;
		}
		return $this->e_sc->parseCodes($text, $parseSCFiles, $extraCodes);
	}


	function htmlwrap($str, $width, $break = "\n", $nobreak = "", $nobr = "pre", $utf = false)
	{
		/*
		* htmlwrap() function - v1.1
		* Copyright (c) 2004 Brian Huisman AKA GreyWyvern
		* http://www.greywyvern.com/code/php/htmlwrap_1.1.php.txt
		*
		* This program may be distributed under the terms of the GPL
		*   - http://www.gnu.org/licenses/gpl.txt
		*/

		$content = preg_split("/([<>])/", $str, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		$nobreak = explode(" ", $nobreak);
		$nobr = explode(" ", $nobr);
		$intag = false;
		$innbk = array();
		$innbr = array();
		$drain = "";
		$utf = ($utf || CHARSET == 'utf-8') ? "u" : "";
		$lbrks = "/?!%)-}]\\\"':;";
		if ($break == "\r")
		{
			$break = "\n";
		}
		while (list(, $value) = each($content))
		{
			switch ($value)
			{
				case "<": $intag = true; break;
				case ">": $intag = false; break;
				default:
				if ($intag)
				{
					if ($value{0} != "/")
					{
						preg_match('/^(.*?)(\s|$)/'.$utf, $value, $t);
						if ((!count($innbk) && in_array($t[1], $nobreak)) || in_array($t[1], $innbk)) $innbk[] = $t[1];
						if ((!count($innbr) && in_array($t[1], $nobr)) || in_array($t[1], $innbr)) $innbr[] = $t[1];
					} else {
						if (in_array(substr($value, 1), $innbk)) unset($innbk[count($innbk)]);
						if (in_array(substr($value, 1), $innbr)) unset($innbr[count($innbr)]);
					}
				} else if ($value)
				{
					if (!count($innbr)) $value = str_replace("\n", "\r", str_replace("\r", "", $value));
					if (!count($innbk))
					{
						do
						{
							$store = $value;
							if (preg_match("/^(.*?\s|^)(([^\s&]|&(\w{2,5}|#\d{2,4});){".$width."})(?!(".preg_quote($break, "/").'|\s))(.*)$/s'.$utf, $value, $match))
							{
								for ($x = 0, $ledge = 0; $x < strlen($lbrks); $x++) $ledge = max($ledge, strrpos($match[2], $lbrks{$x}));
								if (!$ledge) $ledge = strlen($match[2]) - 1;
								$value = $match[1].substr($match[2], 0, $ledge + 1).$break.substr($match[2], $ledge + 1).$match[6];
							}
						}
						while ($store != $value);
					}
					if (!count($innbr)) $value = str_replace("\r", E_NL, $value);
				}
			}
			$drain .= $value;
		}
		return $drain;
	}

	function html_truncate ($text, $len = 200, $more = "[more]")
	{
		$pos = 0;
		$curlen = 0;
		$tmp_pos = 0;
		while($curlen < $len && $curlen < strlen($text))
		{
			switch($text{$pos})
			{
				case "<" :
				if($text{$pos+1} == "/")
				{
					$closing_tag = TRUE;
				}
				$intag = TRUE;
				$tmp_pos = $pos-1;
				$pos++;
				break;
				case ">" :
				if($text{$pos-1} == "/")
				{
					$closing_tag = TRUE;
				}
				if($closing_tag == TRUE)
				{
					$tmp_pos = 0;
					$closing_tag = FALSE;
				}
				$intag = FALSE;
				$pos++;
				break;
				case "&" :
				if($text{$pos+1} == "#")
				{
					$end = strpos(substr($text, $pos, 7), ";");
					if($end !== FALSE)
					{
						$pos+=($end+1);
						if(!$intag) {$curlen++;}
						break;
					}
				}
				else
				{
					$pos++;
					if(!$intag) {$curlen++;}
					break;
				}
				default:
				$pos++;
				if(!$intag) {$curlen++;}
				break;
			}
		}
		$ret = ($tmp_pos > 0 ? substr($text, 0, $tmp_pos) : substr($text, 0, $pos));
		if($pos < strlen($text))
		{
			$ret = $ret.$more;
		}
		return $ret;
	}

	function text_truncate($text, $len = 200, $more = "[more]") {
		if(strlen($text) <= $len) {
			return $text;
		} else { // utf-8 compatible substr()
            return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,0}'.
					'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
					'$1',$text).$more;
		}
	}

	function textclean ($text, $wrap=100)
	{
		$text = str_replace ("\n\n\n", "\n\n", $text);
		$text = $this -> htmlwrap($text, $wrap);
		$text = str_replace (array ("<br /> ", " <br />", " <br /> "), "<br />", $text);
		/* we can remove any linebreaks added by htmlwrap function as any \n's will be converted later anyway */
		return $text;
	}

	//
	// Test for text highlighting, and determine the text highlighting transformation
	// Returns TRUE if highlighting is active for this page display
	//
	function checkHighlighting()
	{
		global $pref;

		if (!defined('e_SELF'))
		{
			return FALSE;	// Still in startup, so can't calculate highlighting
		}

		if (!isset($this->e_highlighting))
		{
			$this->e_highlighting = FALSE;
			$shr = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "");
			if ($pref['search_highlight'] && (strpos(e_SELF, 'search.php') === FALSE) && ((strpos($shr, 'q=') !== FALSE) || (strpos($shr, 'p=') !== FALSE)))
			{
				$this->e_highlighting = TRUE;
				if (!isset($this -> e_query))
				{
					$query = preg_match('#(q|p)=(.*?)(&|$)#', $shr, $matches);
					$this -> e_query = str_replace(array('+', '*', '"', ' '), array('', '.*?', '', '\b|\b'), trim(urldecode($matches[2])));
				}
			}
		}
		return $this->e_highlighting;
	}

	function toHTML($text, $parseBB = FALSE, $modifiers = "", $postID = "", $wrap=FALSE) {
		if ($text == '')
		{
			return $text;
		}
		global $pref, $fromadmin;

		$fromadmin = strpos($modifiers, "fromadmin");
		//$text = str_replace(array("&#092;&quot;", "&#092;&#039;", "&#092;&#092;"), array("&quot;", "&#039;", "&#092;"), $text);

		// support for converting defines(constants) within text. eg. Lan_XXXX - must be the entire text string (i.e. not embedded)
		if(strpos($modifiers,"defs") !== FALSE && strlen($text) < 25 && defined(trim($text))){
			return constant(trim($text));
		}

		// replace all {e_XXX} constants with their e107 value
		if(strpos($modifiers, "constants") !== FALSE)
		{
			$text = $this->replaceConstants($text);
		}

       if(!$wrap && $pref['main_wordwrap']) $wrap = $pref['main_wordwrap'];
        $text = " ".$text;

			// Prepare for line-break compression. Avoid compressing newlines in embedded scripts and CSS
        if (strpos($modifiers, 'nobreak') === FALSE) {
            $text = preg_replace("#>\s*[\r]*\n[\r]*#", ">", $text);
            preg_match_all("#<(script|style)[^>]+>.*?</(script|style)>#is", $text, $embeds);
            $text = preg_replace("#<(script|style)[^>]+>.*?</(script|style)>#is", "<|>", $text);
        }

			// Convert URL's to clickable links, unless modifiers or prefs override
        if($pref['make_clickable'] && strpos($modifiers, 'no_make_clickable') === FALSE) {
            if($pref['link_replace'] && strpos($modifiers, 'no_replace') === FALSE) {
                $_ext = ($pref['links_new_window'] ? " rel=\"external\"" : "");
                $text = preg_replace("#(^|[\n ])([\w]+?://[^ \"\n\r\t<]*)#is", "\\1<a href=\"\\2\" {$_ext}>".$pref['link_text']."</a>", $text);
                $text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#is", "\\1<a href=\"http://\\2\" {$_ext}>".$pref['link_text']."</a>", $text);
                if(CHARSET != "utf-8" && CHARSET != "UTF-8"){
                    $email_text = ($pref['email_text']) ? $this->replaceConstants($pref['email_text']) : "\\1\\2&copy;\\3";
                }else{
                    $email_text = ($pref['email_text']) ? $this->replaceConstants($pref['email_text']) : "\\1\\2©\\3";
                }
                $text = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a rel='external' href='javascript:window.location=\"mai\"+\"lto:\"+\"\\2\"+\"@\"+\"\\3\";self.close();' onmouseover='window.status=\"mai\"+\"lto:\"+\"\\2\"+\"@\"+\"\\3\"; return true;' onmouseout='window.status=\"\";return true;'>".$email_text."</a>", $text);
            } else {
                $text = preg_replace("#(^|[\n ])([\w]+?://[^ \"\n\r\t<,]*)#is", "\\1<a href=\"\\2\" rel=\"external\">\\2</a>", $text);
                $text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<,]*)#is", "\\1<a href=\"http://\\2\" rel=\"external\">\\2</a>", $text);
                $text = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a rel='external' href='javascript:window.location=\"mai\"+\"lto:\"+\"\\2\"+\"@\"+\"\\3\";self.close();' onmouseover='window.status=\"mai\"+\"lto:\"+\"\\2\"+\"@\"+\"\\3\"; return true;' onmouseout='window.status=\"\";return true;'>-email-</a>", $text);
            }
        }

			// Convert emoticons to graphical icons, unless modifiers override
        if (strpos($modifiers, 'emotes_off') === FALSE) {
            if ($pref['smiley_activate'] || strpos($modifiers,'emotes_on') !== FALSE) {
                if (!is_object($this->e_emote)) {
                    require_once(e_HANDLER.'emote_filter.php');
                    $this->e_emote = new e_emoteFilter;
                }
                $text = $this->e_emote->filterEmotes($text);
            }
        }

			// Reduce multiple newlines in all forms to a single newline character, except for embedded scripts and CSS
        if (strpos($modifiers, 'nobreak') === FALSE) {
            $text = preg_replace("#[\r]*\n[\r]*#", E_NL, $text);
            foreach ($embeds[0] as $embed) {
                $text = preg_replace("#<\|>#", $embed, $text, 1);
            }
        }

		// Restore entity form of quotes and such to single characters, except for text destined for tag attributes or JS.
		if (strpos($modifiers, 'value') === FALSE) { // output not used for attribute values.
	       	$text = str_replace($this -> search, $this -> replace, $text);
        }else{   									// output used for attribute values.
            $text = str_replace($this -> replace, $this -> search, $text);
		}

        // Start parse [bb][/bb] codes
        if ($parseBB === TRUE) {
            if (!is_object($this->e_bb)) {
                require_once(e_HANDLER.'bbcode_handler.php');
                $this->e_bb = new e_bbcode;
            }
            $text = $this->e_bb->parseBBCodes($text, $postID);
        }
        // End parse [bb][/bb] codes

				// profanity filter
        if ($pref['profanity_filter']) {
            if (!is_object($this->e_pf)) {
                require_once(e_HANDLER."profanity_filter.php");
                $this->e_pf = new e_profanityFilter;
            }
            $text = $this->e_pf->filterProfanities($text);
        }

			// Optional short-code conversion
        if (strpos($modifiers,'parse_sc') !== FALSE)
        {
            $text = $this->parseTemplate($text, TRUE);
        }

        //Run any hooked in parsers
        if(isset($pref['tohtml_hook']) && $pref['tohtml_hook'])
        {
            foreach(explode(",",$pref['tohtml_hook']) as $hook)
            {
                if (strpos($modifiers, 'no_hook') === FALSE)
                {
                    if (!is_object($this->e_hook[$hook]))
                    {
                        require_once(e_PLUGIN.$hook."/".$hook.".php");
                        $hook_class = "e_".$hook;
                        $this->e_hook[$hook] = new $hook_class;
                    }
                    $text = $this->e_hook[$hook]->$hook($text);
                }
            }
        }

        if (strpos($modifiers, 'nobreak') === FALSE) {
            $text = $this -> textclean($text, $wrap);
        }

        // Search Highlight
        if (strpos($modifiers, 'emotes_off') === FALSE) {
        	if ($this->checkHighlighting())
        	{
						$text = $this -> e_highlight($text, $this -> e_query);
					}
				}

        $nl_replace = "<br />";
        if (strpos($modifiers, 'nobreak') !== FALSE)
        {
            $nl_replace = '';
        }
        elseif (strpos($modifiers, 'retain_nl') !== FALSE)
        {
            $nl_replace = "\n";
        }
        $text = str_replace(E_NL, $nl_replace, $text);

		return trim($text);
	}

	function toAttribute($text) {
		$text = str_replace("&amp;","&",$text); // URLs posted without HTML access may have an &amp; in them.
		$text = htmlspecialchars($text); // Xhtml compliance.
		if (!preg_match('/&#|\'|"|\(|\)|<|>/s', $text)) {
			$text = $this->replaceConstants($text);
			return $text;
		} else {
			return '';
		}
	}

	function toJS($stringarray) {
		$search = array("\r\n","\r","<br />","'");
		$replace = array("\\n","","\\n","\'");
		$stringarray = str_replace($search, $replace, $stringarray);
        $stringarray = strip_tags($stringarray);

		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);

		return strtr ($stringarray, $trans_tbl);
	}

	function toRss($text,$tags=FALSE)
	{

		if($tags != TRUE)
		{
			$text = $this -> toHTML($text,TRUE);
			$text = strip_tags($text);
		}

		$text = $this->toEmail($text);
   		$search = array("&amp;#039;", "&amp;#036;", "&#039;", "&#036;"," & ", e_BASE, "href='request.php");
   		$replace = array("'", '$', "'", '$',' &amp; ', SITEURL, "href='".SITEURL."request.php" );
   		$text = str_replace($search, $replace, $text);

		if($tags == TRUE && ($text))
		{
        	$text = "<![CDATA[".$text."]]>";
		}

		return $text;
	}

	//Convert specific characters back to original form, for use in storing code (or regex) values in the db.
	function toText($text)
	{
		$search = array("&amp;#039;", "&amp;#036;", "&#039;", "&#036;", "&#092;", "&amp;#092;");
		$replace = array("'", '$', "'", '$', "\\", "\\");
		$text = str_replace($search, $replace, $text);
		return $text;
	}

//
// $nonrelative:
//   "full" = produce absolute URL path, e.g. http://sitename.com/e107_plugins/etc
//   TRUE = produce truncated URL path, e.g. e107plugins/etc
//   "" (default) = URL's get relative path e.g. ../e107_plugins/etc
//                  AND all other e107 constants are replaced
//
// only an ADMIN user can convert {e_ADMIN}
//
	function replaceConstants($text, $nonrelative = "", $all = false)
	{
		if($nonrelative != "")
		{
			global $IMAGES_DIRECTORY, $PLUGINS_DIRECTORY, $FILES_DIRECTORY, $THEMES_DIRECTORY,$DOWNLOADS_DIRECTORY,$ADMIN_DIRECTORY;
			$replace_relative = array("",$IMAGES_DIRECTORY,$PLUGINS_DIRECTORY,$FILES_DIRECTORY,$THEMES_DIRECTORY,$DOWNLOADS_DIRECTORY);
			$replace_absolute = array(SITEURL,SITEURL.$IMAGES_DIRECTORY,SITEURL.$PLUGINS_DIRECTORY,SITEURL.$FILES_DIRECTORY,SITEURL.$THEMES_DIRECTORY,SITEURL.$DOWNLOADS_DIRECTORY);
			$search = array("{e_BASE}","{e_IMAGE}","{e_PLUGIN}","{e_FILE}","{e_THEME}","{e_DOWNLOAD}");
			if (ADMIN) {
				$replace_relative[] = $ADMIN_DIRECTORY;
				$replace_absolute[] = SITEURL.$ADMIN_DIRECTORY;
				$search[] = "{e_ADMIN}";
			}
			$replace = ((string)$nonrelative == "full" ) ? $replace_absolute : $replace_relative;
			return str_replace($search,$replace,$text);
		}
		$pattern = ($all ? "#\{([A-Za-z_0-9]*)\}#s" : "#\{(e_[A-Z]*)\}#s");
	 	$text = preg_replace_callback($pattern, array($this, 'doReplace'), $text);
		$theme_path = (defined("THEME")) ? constant("THEME") : "";
		$text = str_replace("{THEME}",$theme_path,$text);

		return $text;
	}

	function doReplace($matches)
	{
		if(defined($matches[1]) && ($matches[1] != 'e_ADMIN' || ADMIN))
		{
			return constant($matches[1]);
		}
		return $matches[1];
	}

    function createConstants($url,$mode=0){
        global $IMAGES_DIRECTORY,$PLUGINS_DIRECTORY,$FILES_DIRECTORY,$THEMES_DIRECTORY,$DOWNLOADS_DIRECTORY,$ADMIN_DIRECTORY;

        if($mode == 0) // folder name only.
		{
			$tmp = array(
				"{"."e_IMAGE"."}"=>$IMAGES_DIRECTORY,
				"{"."e_PLUGIN"."}"=>$PLUGINS_DIRECTORY,
				"{"."e_FILE"."}"=>$FILES_DIRECTORY,
				"{"."e_THEME"."}"=>$THEMES_DIRECTORY,
				"{"."e_DOWNLOAD"."}"=>$DOWNLOADS_DIRECTORY,
				"{"."e_ADMIN"."}"=>$ADMIN_DIRECTORY,
  			);
        }
		elseif($mode == 1)  // relative path
		{
			$tmp = array(
				"{"."e_IMAGE"."}"=>e_IMAGE,
				"{"."e_PLUGIN"."}"=>e_PLUGIN,
				"{"."e_FILE"."}"=>e_FILE,
				"{"."e_THEME"."}"=>e_THEME,
				"{"."e_DOWNLOAD"."}"=>e_DOWNLOAD,
				"{"."e_ADMIN"."}"=>e_ADMIN
			);
		}
		foreach($tmp as $key=>$val)
		{
        	$len = strlen($val);
			if(substr($url,0,$len) == $val)
			{
            	return str_replace($val,$key,$url);
			}
		}

		return $url;
    }


	function e_highlight($text, $match) {
		preg_match_all("#<[^>]+>#", $text, $tags);
		$text = preg_replace("#<[^>]+>#", "<|>", $text);
		$text = preg_replace("#(\b".$match."\b)#i", "<span class='searchhighlight'>\\1</span>", $text);
		foreach ($tags[0] as $tag) {
			$text = preg_replace("#<\|>#", $tag, $text, 1);
		}
		return $text;
	}

    function toEmail($text,$posted="")
	{
		if ($posted === TRUE && MAGIC_QUOTES_GPC) {
			$text = stripslashes($text);
		}

	  	$text = $this->replaceConstants($text,"full");
    	$text = $this->toHTML($text,TRUE,"parse_sc");
        return $text;
	}

}

?>
