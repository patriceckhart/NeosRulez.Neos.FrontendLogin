<?php
namespace NeosRulez\Neos\FrontendLogin\Domain\Service;

use Neos\Flow\Annotations as Flow;

class MailService {

    /**
     * @param string $template
     * @param array $args
     * @param string $subject
     * @param string $sender
     * @param string $recipient
     * @return void
     */
    public function sendMail(string $template, array $args, string $subject, string $sender, string $recipient):void
    {

        $view = new \Neos\FluidAdaptor\View\StandaloneView();
        $view->setTemplatePathAndFilename($template);
        $view->assignMultiple($args);

        $mail = new \Neos\SwiftMailer\Message();
        $mail
            ->setFrom($sender)
            ->setTo(array($recipient => $recipient))
            ->setSubject($subject);
        $mail->setBody($view->render(), 'text/html');
        $mail->send();

    }

}
