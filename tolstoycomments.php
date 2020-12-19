<?php
/**
 * Tolstoy Comments
 *
 * @version 1.1.0
 * @author JoomLine(sale@joomline.ru)
 * @copyright (C) 2020 JoomLine(https://joomline.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 **/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgContentTolstoycomments extends JPlugin
{

	public function onContentAfterDisplay($context, &$article, &$params, $limitstart){
	if($context == 'com_content.article'){
		

		JPlugin::loadLanguage( 'plg_content_tolstoycomments' );			


		if (strpos($article->text, '{tolstoycomments-off}') !== false) {
			$article->text = str_replace("{tolstoycomments-off}","",$article->text);
			return "";
		}

		$exceptcat = is_array($this->params->def('categories')) ? $this->params->def('categories') : array($this->params->def('categories'));
		if (!in_array($article->catid,$exceptcat)) {
		
			$jinput = JFactory::getApplication()->input;
			$view = $jinput->get('view'); ;
			
			if ($view == 'article') {

				$doc = JFactory::getDocument();
				
				$apiId 			= $this->params->def('apiId');

				$scriptPage = "";
				$pagehash = $article->id;
				$scriptPage .= <<<HTML
				
					<script type="text/javascript">!(function(w,d,s,l,x){w[l]=w[l]||[];w[l].t=w[l].t||new Date().getTime();var f=d.getElementsByTagName(s)[0],j=d.createElement(s);j.async=!0;j.src='//web.tolstoycomments.com/sitejs/app.js?i='+l+'&x='+x+'&t='+w[l].t;f.parentNode.insertBefore(j,f);})(window,document,'script','tolstoycomments','$apiId');</script>

					<div class="tolstoycomments-feed"></div>
					<script type="text/javascript">
						window['tolstoycomments'] = window['tolstoycomments'] || [];
						window['tolstoycomments'].push({
							action: 'init',
							values: {
								identity: "$pagehash",
								visible: true
							}
						});
					</script>					
HTML;
				$html = '';
				$db = JFactory::getDBO();
				$query = 'SELECT id_comm, datеtime, user_name, message FROM #__tolstoycomments WHERE identity = "'.$pagehash.'" ORDER BY `id_comm` DESC';
				$db->setQuery($query);
				$results = $db->loadObjectList();
				if(count($results)){
					$html .= '<div style="display:none;"><table class="table table-striped">
							<thead>
								<tr>
								<th class="nowrap center">id</th>
								<th class="nowrap center">datеtime</th>
								<th class="nowrap center">user_name</th>
								<th>message</th>
								</tr>
							</thead>
							<tbody>';
					foreach($results as $result){
						$html .= '<tr><td class="center">'.$result->id_comm.'</td><td class="center">'.$result->datеtime.'</td><td class="center">'.$result->user_name.'</td><td>'.$result->message.'</td></tr>';
					}
					$html .= '</tbody></table></div>';
				}
				if ($this->params->def('autoAdd') == 1) {
					$article->text = str_replace("{tolstoycomments}","",$article->text);
					return $scriptPage.$html;				
				} else {
					$article->text = str_replace("{tolstoycomments}",$scriptPage.$html,$article->text);
				}

			}
		} else {
			$article->text = str_replace("{tolstoycomments}","",$article->text);
		}

	}
}		
			public function onAjaxTolstoycomments() { 
				$input = JFactory::getApplication()->input;
				$plugin = JPluginHelper::getPlugin('content', 'tolstoycomments');	
				$paramsplug = new JRegistry($plugin->params);
				$apikey = str_replace('api key','',$paramsplug->get('apikey',false)); 
				$apiId = $paramsplug->get('apiId',false);
				if($apikey != $input->get('key')){
					echo 'unsucess';
					JFactory::getApplication()->close();
				}
				
					if($apikey && $apiId){
						$i=0;
						$db = JFactory::getDbo();
						$query = $db->getQuery(TRUE); 
						$query->select('MIN(id_comm) as idcomm');
						$query->from('#__tolstoycomments');
						$db->setQuery($query);
						$comment_last_id = $db->loadResult();
						while ($comment_last_id > 0 || $comment_last_id == ""){
							$comment_last ='';
							if($comment_last_id > 0){
								$comment_last = '/'.$comment_last_id;
							}
							$ch = curl_init('https://api.tolstoycomments.com/api/export/'.$apikey.'/site/'.$apiId.'/comment'.$comment_last);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
							curl_setopt($ch, CURLOPT_HEADER, false);
							$results = json_decode(curl_exec($ch));
							curl_close($ch);
							// echo '<pre>'.print_r($results,1).'</pre>';
							$db->setQuery($query)->execute();$comment_last_id = -1;
							if(empty($results->data->comment_last_id)){
								$comment_last_id = -1;
							}else{
								$comment_last_id = $results->data->comment_last_id;
							}
							foreach($results->data->comments as $comments){
								
								if($comments->visible!='1'){continue;}
								$i++;
								$values = array(
									$db->quote($comments->id),
									$db->quote($comments->message),
									$db->quote($comments->ip),
									$db->quote($comments->datеtime),
									$db->quote($comments->rating),
									$db->quote($comments->visible),
									$db->quote($comments->user->id),
									$db->quote($comments->user->nick),
									$db->quote($comments->user->name),
									$db->quote($comments->user->email),
									$db->quote($comments->user->phone),
									$db->quote($comments->user->avatar),
									$db->quote($comments->chat->identity)
								);
								$query = 'INSERT INTO `#__tolstoycomments` (`id_comm`, `message`, `ip`, `datеtime`, `rating`,  `visible`, `user_id`, `user_nick`, `user_name`, `user_email`, `user_phone`, `user_avatar`, `identity`) VALUES  ('.implode(',', $values).')';
								$db->setQuery($query)->execute();

							}
							
						}
						 echo 'sucess';
					}else{
						echo 'unsucess';
					}
				JFactory::getApplication()->close();
			}
		
}	