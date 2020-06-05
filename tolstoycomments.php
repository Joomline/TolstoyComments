<?php
/**
 * Tolstoy Comments
 *
 * @version 1.0.0
 * @author JoomLine(sale@joomline.ru)
 * @copyright (C) 2020 JoomLine(https://joomline.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 **/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgContentTolstoycomments extends JPlugin
{

	public function onContentAfterDisplay($context, &$article, &$params, $limitstart = 0){
	if($context == 'com_content.article'){

		JPlugin::loadLanguage( 'plg_content_tolstoycomments' );	



		if (strpos($article->text, '{tolstoycomments-off}') !== false) {
			$article->text = str_replace("{tolstoycomments-off}","",$article->text);
			return "";
		}

		if (strpos($article->text, '{tolstoycomments}') === false && !$this->params->def('autoAdd')) {
			return $scriptPage;	
		}

		$exceptcat = is_array($this->params->def('categories')) ? $this->params->def('categories') : array($this->params->def('categories'));
		if (!in_array($article->catid,$exceptcat)) {
			$view = JRequest::getCmd('view');
			if ($view == 'article') {

				$doc = &JFactory::getDocument();
				
				$apiId 			= $this->params->def('apiId');


				$pagehash = $article->id;
				$scriptPage .= <<<HTML
				
					<script type="text/javascript">!(function(w,d,s,l,x){w[l]=w[l]||[];w[l].t=w[l].t||new Date().getTime();var f=d.getElementsByTagName(s)[0],j=d.createElement(s);j.async=!0;j.src='//web.tolstoycomments.com/sitejs/app.js?i='+l+'&x='+x+'&t='+w[l].t;f.parentNode.insertBefore(j,f);})(window,document,'script','tolstoycomments','$apiId');</script>

					<div class="tolstoycomments-feed"></div>
					<script type="text/javascript">
						window['tolstoycomments'] = window['tolstoycomments'] || [];
						window['tolstoycomments'].push({
							action: 'init',
							values: {
								identity: "$scriptPage",
								visible: true
							}
						});
					</script>					
HTML;
				
				if ($this->params->def('autoAdd') == 1) {
					$article->text = str_replace("{tolstoycomments}",$scriptPage,$article->text);
					return $scriptPage;				
				} else {
					$article->text = str_replace("{tolstoycomments}",$scriptPage,$article->text);
				}

			}
		} else {
			$article->text = str_replace("{tolstoycomments}","",$article->text);
		}

	}
}

}
