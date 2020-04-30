<?php
function sendMail($to,$title,$content,$email_title,$smtp_email,$smtp_key){
    require_once(MODULE_ROOT."/mail/class.phpmailer.php"); 
    require_once(MODULE_ROOT."/mail/class.smtp.php");
    $mail = new PHPMailer();
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->SMTPAuth=true;
    $mail->Host = 'smtp.qq.com';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->Hostname = 'http://www.wlxgj.cn';
    $mail->CharSet = 'UTF-8';
    $mail->FromName = $email_title;
    $mail->Username =$smtp_email;
    $mail->Password = $smtp_key;
    $mail->From = $smtp_email;
    $mail->isHTML(true); 
    if(strpos($to, ',')){
        $arr=explode(',', $to);
        foreach ($arr as $key => $value) {
            if($value){
                $mail->addAddress($value,'酒店通知');
            }
        }
    }else{
        $mail->addAddress($to,'酒店通知');
    }
    $mail->Subject = $title;
    $mail->Body = $content;
    $status = $mail->send();
    // if($status) {
    //     return true;
    // }else{
    //     return false;
    // }
}

?>