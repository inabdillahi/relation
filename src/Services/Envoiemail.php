<?php 

namespace App\Services;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;


class Envoiemail {




    public function sendEmail(MailerInterface $mailer,$mailEnvoie,$recevEmail){
        $email = (new Email())
            ->from($mailEnvoie)
            ->to($recevEmail)
            ->subject('Activation du compte')
            ->text('Bonjour et Merci de votre inscription, veuillez cliqué le lien ci-dessous')
            ->html('')
        ;

        return $mailer->send($email);
    }
}





?>