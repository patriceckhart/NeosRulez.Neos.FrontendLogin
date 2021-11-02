<?php
namespace NeosRulez\Neos\FrontendLogin\Finisher;

/*
 * This file is part of the NeosRulez.Neos.FrontendLogin package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Form\Core\Model\AbstractFinisher;

/**
 * This finisher create a new frontend user
 */
class ResetPasswordFinisher extends AbstractFinisher
{

    /**
     * @Flow\Inject
     * @var \NeosRulez\Neos\FrontendLogin\Domain\Service\UserService
     */
    protected $userService;

    /**
     * @Flow\Inject
     * @var \NeosRulez\Neos\FrontendLogin\Domain\Service\MailService
     */
    protected $mailService;

    /**
     * @Flow\Inject
     * @var \Neos\Flow\I18n\Translator
     */
    protected $translator;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings) {
        $this->settings = $settings;
    }


    /**
     * Executes this finisher
     * @return void
     * @throws FinisherException
     * @see AbstractFinisher::execute()
     */
    protected function executeInternal()
    {
        $formRuntime = $this->finisherContext->getFormRuntime();
        $formValues = $formRuntime->getFormState()->getFormValues();

        if(!empty($formValues)) {
            $properties = [];
            foreach ($formValues as $formValueIterator => $formValue) {
                $properties[$formValueIterator] = $formValue;
            }

            if(array_key_exists('username', $properties)) {
                $user = $this->userService->getUser($properties['username'], 'NeosRulez.Neos.FrontendLogin:NeosFrontend');
                $password = $this->userService->generatePassword();
                $this->userService->setUserPassword($user, $password);
                $this->mailService->sendMail($this->settings['resetPassword']['templatePathAndFilename'], ['username' => $properties['username'], 'password' => $password], $this->translator->translateById('content.subjectReset', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.FrontendLogin'), $this->settings['senderMail'], $properties['username']);
            } else {
                throw new \Neos\Flow\Exception('The required field with the ID username is not defined.', 1347145544);
            }

        }

    }

}
