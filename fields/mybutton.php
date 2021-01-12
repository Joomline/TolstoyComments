<?php
/**
 * Tolstoy Comments
 *
 * @version 1.1.1
 * @author JoomLine(sale@joomline.ru)
 * @copyright (C) 2020-2021 JoomLine(https://joomline.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 **/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldMybutton extends JFormField
{
     protected $type = 'Mybutton';
     protected function getInput()
    {	$plugin = JPluginHelper::getPlugin('content', 'tolstoycomments');	
			$paramsplug = new JRegistry($plugin->params);
			$apikey = str_replace('api key','',$paramsplug->get('apikey',false)); 
			if(empty($apikey)){
				$html ='';
			}else{
			$plg_tolstoycomments_warning = JText::_('PLG_TOLSTOYCOMMENTS_WARNING');
			$plg_tolstoycomments_download_comments = JText::_('PLG_TOLSTOYCOMMENTS_DOWNLOAD_COMMENTS');
			$plg_tolstoycomments_cron = JText::_('PLG_TOLSTOYCOMMENTS_CRON');
			$url = JUri::root();
        $html = <<<HTML
		$plg_tolstoycomments_cron - $url?option=com_ajax&plugin=tolstoycomments&group=content&format=json&key=$apikey
		<script type="text/javascript">
		function Get_tolstoycomments(){
                    jQuery.ajax( {
				url: "$url?option=com_ajax&plugin=tolstoycomments&group=content&format=json&key=$apikey",
				type: "post",
				success: function(response){
                    if(response  == "unsucess"){
						alert("$plg_tolstoycomments_warning");					
                    }
					if(response  == "sucess"){
						window.location.reload();				
                    }
				}
			} );
                }
			</script>
		<button onclick="Get_tolstoycomments();return false;">$plg_tolstoycomments_download_comments</button>
HTML;
			}
		$db = JFactory::getDBO();
		$query = 'SELECT id_comm, datеtime, user_name, message FROM #__tolstoycomments ORDER BY `id_comm` DESC';
		$db->setQuery($query);
		$results = $db->loadObjectList();
		if(count($results)){
			$html .= '<table class="table table-striped">
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
			$html .= '</tbody></table>';
		}
        return $html;
    }
}