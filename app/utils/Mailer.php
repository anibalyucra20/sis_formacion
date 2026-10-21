<?php

namespace App\Utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Core\Model;

class Mailer
{
    public static function send(string $toEmail, string $toName, string $subject, string $html, string $alt = ''): array
    {
        try {
            $db = Model::getDB();
            $stmt = $db->query("SELECT * FROM configuracion_sistema WHERE id = 1 LIMIT 1");
            $cfg = $stmt->fetch() ?: [];
        } catch (\Exception $e) {
            $cfg = [];
        }

        $host     = !empty($cfg['host_mail']) ? $cfg['host_mail'] : 'smtp.gmail.com';
        $username = !empty($cfg['email_email']) ? $cfg['email_email'] : '';
        $password = !empty($cfg['password_email']) ? $cfg['password_email'] : '';
        $port     = !empty($cfg['puerto_email']) ? (int)$cfg['puerto_email'] : 587;
        $fromName = !empty($cfg['nombre_sistema']) ? $cfg['nombre_sistema'] : 'Control de Certificados';

        // Si no hay credenciales configuradas en producción, registramos en log y avisamos
        if (empty($username) || empty($password)) {
            error_log("[Mailer] SMTP no configurado: to={$toEmail}, subject={$subject}");
            return [
                'ok'    => false,
                'error' => 'SMTP no configurado en el sistema. Revise las credenciales en la base de datos.',
                'mid'   => null
            ];
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $username;
            $mail->Password   = $password;
            $mail->Port       = $port;
            $mail->SMTPSecure = ($port === 587)
                ? PHPMailer::ENCRYPTION_STARTTLS
                : PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ]
            ];
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($username, $fromName);
            $mail->Sender   = $username;
            $mail->addAddress($toEmail, $toName);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body    = $html;
            $mail->AltBody = $alt ?: strip_tags($html);

            $ok = $mail->send();
            $mid = $mail->getLastMessageID();
            return ['ok' => $ok, 'error' => $ok ? null : $mail->ErrorInfo, 'mid' => $mid];
        } catch (Exception $e) {
            return ['ok' => false, 'error' => $mail->ErrorInfo ?: $e->getMessage()];
        }
    }
}
