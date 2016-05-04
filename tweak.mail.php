<?php
// Replaces wp_mail()-function, so as to send system-notifications via UKMs mail.class.php

# The original function looks like this: 
/*
function wp_mail( $to, $subject, $message, $headers = '' ) {
  if( $headers == '' ) {
    $headers = "MIME-Version: 1.0\n" .
      "From: " . get_settings('admin_email') . "\n" . 
      "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\n";
  }

  return @mail( $to, $subject, $message, $headers );
}
*/

if(!function_exists('wp_mail')) {
	function wp_mail($to, $subject, $message, $headers = '', $attachments = array()) {
		require_once('UKM/mail.class.php');

		$mail = new UKMmail();
	    $mail->subject($subject);
	    // Turn receivers into comma separated list
	    $m_to = '';
	    if(is_array($to)) {
	    	foreach ($to as $t) {
	    		$m_to .= $t.', ';
	    	} 
	    	rtrim($m_to, ', ');
	    }

	    $mail->to($m_to);
	    #$mail->to('asgeirsh@ukmmedia.no');
	    $mail->message($message);
	    $res = $mail->ok();
	    if (true !== $res) {
	        error_log('wp_mail: Klarte ikke å sende e-post. PHPMAILER_error: '.$res);
	        return false;
	    }
	    return true;
	}
}