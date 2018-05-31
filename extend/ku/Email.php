<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/13
 * Time: 13:26
 */
namespace ku;
use phpmailer\PHPMailer;
use phpmailer\Exception;
use phpmailer\SMTP;
use think\Config;

class Email{

    /**
     * 邮箱发送
     * @param $email      //收件邮箱
     * @param $subject //标题
     * @param $body //内容
     * @return bool
     */
    public static function sendEmail($email,$subject,$body){
        //实例化
        $emailConfig = Config::get('basketball.email');
        $mail=new PHPMailer(true);
        try{
            //邮件调试模式
//            $mail->SMTPDebug = 2;
            //设置邮件使用SMTP
            $mail->isSMTP();
            // 设置邮件程序以使用SMTP
            $mail->Host = 'smtp.qq.com';
            // 设置邮件内容的编码
            $mail->CharSet='UTF-8';
            // 启用SMTP验证
            $mail->SMTPAuth = true;
            // SMTP username
            $mail->Username = $emailConfig['email'];
            // SMTP password
            $mail->Password = $emailConfig['password'];
            // 启用TLS加密，`ssl`也被接受
            //            $mail->SMTPSecure = 'tls';
            // 连接的TCP端口
            //            $mail->Port = 587;
            //设置发件人
            $mail->setFrom($emailConfig['email'], $emailConfig['name']);
            //  添加收件人1
            $mail->addAddress($email, 'dear');     // Add a recipient
//            $mail->addAddress('913294974@qq.com', 'dear');
            //            $mail->addAddress('ellen@example.com');               // Name is optional
            //            收件人回复的邮箱
            $mail->addReplyTo($emailConfig['email'], $emailConfig['name']);
            //            抄送
            //            $mail->addCC('cc@example.com');
            //            $mail->addBCC('bcc@example.com');
            //附件
            //            $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            //Content
            // 将电子邮件格式设置为HTML
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->send();
            $mail->isSMTP();
            return true;
        }catch (Exception $e){
            return false;
        }
    }

}