<?php
/**
 * Translation Plugin: Simple multilanguage plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     i-net software <tools@inetsoftware.de>
 * @author     Gerry Weissbach <gweissbach@inetsoftware.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class helper_plugin_fancysearch extends DokuWiki_Plugin {

    function tpl_searchform($namespaces, $return = false) {
        global $QUERY;
        $cur_val = isset($_REQUEST['namespace']) ? $_REQUEST['namespace'] : '';
        $lang = preg_quote($this->getLangCode(), '/');
        $cur_val = preg_replace('/^'.$lang.':/', '', $cur_val);
        
		ob_start();
		$QUERY = hsc(preg_replace('/ ?@\S+/','',$QUERY));
		tpl_searchform(true, false);
		$searchForm = ob_get_contents();
		ob_end_clean();

		// Default Selct
        $namespaceSelect =  '<select class="fancysearch_namespace" name="namespace">';
        foreach ($namespaces as $element) {
        	list($ns, $name, $class) = $element;
            $namespaceSelect .= '<option class="fancysearch_ns_'.hsc($class).'" value="'.hsc($ns).'"'.($cur_val === $ns ? ' selected="selected"' : '').'>'.$name.'</option>';
        }
        $namespaceSelect .= '</select>';

		// Insert reight at the beginning.
		$searchForm = substr_replace($searchForm, $namespaceSelect, strpos($searchForm, '<input'), 0);

		if ( $return ) {
			return '<div id="dokuwiki__sitetools" class="fancysearch__container">'.$searchForm.'</div>';
		} else {
			print '<div class="fancysearch__container">'.$searchForm.'</div>';
		}
    }

    private function translatedNamespace($id) {
        global $conf;

        if ($id === '') return '';
        static $lang = null;
        if ($lang === null) {
            $lang = $this->getLangCode();
            if ($lang !== '') {
                $lang .= ':';
            }
        }

        if (page_exists($lang . $id . ':' . $conf['start'])) return $lang . $id;
        return $id;
    }

    private function getLangCode() {
        if (!isset($_SESSION[DOKU_COOKIE]['translationlc']) || empty($_SESSION[DOKU_COOKIE]['translationlc'])) {
            return '';
        }
        return $_SESSION[DOKU_COOKIE]['translationlc'];
    }
}
