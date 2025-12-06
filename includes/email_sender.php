<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../phplibs/phpmailer/PHPMailer.php';
require __DIR__ . '/../phplibs/phpmailer/SMTP.php';
require __DIR__ . '/../phplibs/phpmailer/Exception.php';

function sendEmailWithAttachment($to, $subject, $body, $attachmentPath, $attachmentName, $cc = "", $bcc = "")
{
    global $mailHost;
    global $mailSMTPAuth;
    global $mailUsername;
    global $mailPassword;
    global $mailSMTPSecure;
    global $mailPort;

    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = $mailHost;
        $mail->SMTPAuth   = $mailSMTPAuth;
        $mail->Username   = $mailUsername;
        $mail->Password   = $mailPassword;
        $mail->SMTPSecure = $mailSMTPSecure;
        $mail->Port       = $mailPort;

        // Email Settings
        $mail->setFrom('no-reply@ggm.com.co', 'GrandMaster');      // Replace with your name and Gmail address

        $toAddresses = explode(",", $to);
        foreach ($toAddresses as $toAddress) {
            $mail->addAddress($toAddress);
        }

        if (!empty($cc)) {
            $ccAddresses = explode(",", $cc);
            foreach ($ccAddresses as $ccAddress) {
                $mail->addCC($ccAddress);
            }
        }


        if (!empty($bcc)) {

            $bccAddresses = explode(",", $bcc);
            foreach ($bccAddresses as $bccAddress) {
                $mail->addBCC($bccAddress);
            }
        }

        //        $mail->addBCC('catchme@arafath.com');
        //        $mail->addBCC('arafat@ggm.com.co');
        //        $mail->addAddress('arafat@ggm.com.co');
        //        $mail->addReplyTo('spareparts@ggm.com.co', 'GrandMaster'); // Optional, to set a reply-to address
        //
        // Attachment
        //        if (file_exists($attachmentPath)) {
        //            $mail->addAttachment($attachmentPath, $attachmentName);
        //        } else {
        //            throw new Exception("Attachment file not found: " . $attachmentPath);
        //        }

        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body); // Plain-text version for non-HTML email clients

        // Send Email
        $mail->send();
        // return print_r($mail, true);
       return true;
    } catch (Exception $e) {
        \Sentry\captureException($e);
        return "Email could not be sent. Error: {$mail->ErrorInfo}";
    }
}

function sendNotificationWithEmail(int $recipient_userId, string $subject, string $message, string $url): bool
{
    global $db;

    // 1. In-app notification
    recordNotification($recipient_userId, $subject, $message, $url);

    // 2. Get recipient email/name
    $res = $db->query("SELECT email, firstName, lastName FROM users WHERE userID = ?s AND active = 1", $recipient_userId);
    if (!$res || mysqli_num_rows($res) == 0) {
        error_log("sendNotificationWithEmail: User $recipient_userId not found");
        return false;
    }
    $user = mysqli_fetch_assoc($res);
    $email = trim($user['email']);
    $name  = trim($user['firstName'] . ' ' . ($user['lastName'] ?? ''));

    if (empty($email)) {
        error_log("sendNotificationWithEmail: No email for user $recipient_userId");
        return false;
    }

    // 3. Load template
    $templatePath = __DIR__ . "/../emailtemplates/notificationQuotation.html";
    if (!file_exists($templatePath)) {
        error_log("Template missing: $templatePath");
        return false;
    }
    $body = file_get_contents($templatePath);

    // 4. Replace
    $replacements = [
        '{{SUBJECT}}'        => $subject,
        '{{MESSAGE}}'        => nl2br($message),
        '{{RECIPIENT_NAME}}' => $name ?: 'Team Member',
        '{{LINK}}'           => $url
    ];
    $body = str_replace(array_keys($replacements), array_values($replacements), $body);

    // 5. Send
    $emailSubject = "[GrandMaster] $subject";
    $result = sendEmailWithAttachment($email, $emailSubject, $body, "", "");

    if ($result !== true) {
        error_log("sendNotificationWithEmail failed for $email: $result");
        return false;
    }

    return true;
}