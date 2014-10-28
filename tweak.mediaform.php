<?php
	function UKMwpat_editmedia($media_dims) {
		if(!strpos($media_dims, '&times;'))
			return $media_dims;
#		if(!isset($_GET['attachment_id']))
#			return $media_dims;
		$media_dims = substr($media_dims, 0, strlen($media_dims)-1);
    	$meta = wp_get_attachment_metadata( $_GET['attachment_id'] );
		$sizes = $meta['sizes'];
		
		$order = ($sizes['large']['width'] > $sizes['large']['height']) ? 'width' : 'height';
		
		$ordered = array();
		
		if(!is_array($sizes))
			return $media_dims;
			
		foreach($sizes as $name => $infos)
			if(!isset($ordered[$infos[$order]]))
				$ordered[$infos[$order]] = ', ' . $infos['width'] .'x' . $infos['height'] .'';
			else {
				$temp = $ordered[$infos[$order]];
				$ordered[$infos[$order]] = array(0=>$temp);
				$ordered[$infos[$order]][] = ', ' . $infos['width'] .'x' . $infos['height'] .'';
			}
		
		krsort($ordered);
		
		foreach($ordered as $order => $text)
			if(is_array($text))
				foreach($text as $number => $subtext)
					$media_dims .= $subtext;
			else
				$media_dims .= $text;
		
		return str_replace('&nbsp;&times;&nbsp;', 'x', $media_dims);
	}
	
	
	function UKMwpat_mediaform($content) {
#		echo '<pre>'; var_dump($content); echo '</pre>';
		unset($content['menu_order']);
		unset($content['url']);
		unset($content['post_content']);
#		unset($content['align']);
#		unset($content['image-size']);
		return $content;
	}
	

	function UKMwpat_upload_mimes( $existing_mimes=array() ) {

	    $existing_mimes['zip'] = 'application/zip';
	    $existing_mimes['gz'] = 'application/x-gzip';

		$existing_mimes['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
		$existing_mimes['ppt'] = 'application/vnd.ms-powerpoint';
		$existing_mimes['doc'] = 'application/msword';
		$existing_mimes['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
		$existing_mimes['xls'] = 'application/vnd.ms-excel';
		$existing_mimes['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

		return $existing_mimes;
	}