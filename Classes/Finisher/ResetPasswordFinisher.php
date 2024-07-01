<?php
namespace NeosRulez\Neos\FrontendLogin\Finisher;

/*
 * This file is part of the NeosRulez.Neos.FrontendLogin package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\I18n\Translator;
use Neos\Form\Core\Model\AbstractFinisher;
use Neos\Form\Exception\FinisherException;
use NeosRulez\Neos\FrontendLogin\Domain\Repository\UserRepository;
use NeosRulez\Neos\FrontendLogin\Domain\Service\MailService;
use NeosRulez\Neos\FrontendLogin\Domain\Service\UserService;

/**
 * This finisher create a new frontend user
 */
class ResetPasswordFinisher extends AbstractFinisher
{

    /**
     * @Flow\Inject
     * @var UserService
     */
    protected $userService;

    /**
     * @Flow\Inject
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @Flow\Inject
     * @var MailService
     */
    protected $mailService;

    /**
     * @Flow\Inject
     * @var Translator
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
    public function injectSettings(array $settings): void
    {
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

                $email = false;
                if(filter_var($properties['username'], FILTER_VALIDATE_EMAIL)) {
                    $email = str_replace(' ', '', $properties['username']);
                } else {
                    $userEntitys = $this->userRepository->findByUser($user);
                    if(count($userEntitys) > 0 && array_key_exists('email', $userEntitys[0]->getProperties())) {
                        $email = $userEntitys[0]->getProperties()['email'];
                    }
                }
                if($email) {
                    $this->mailService->sendMail($this->settings['resetPassword']['templatePathAndFilename'], ['username' => $properties['username'], 'password' => $password], $this->translator->translateById('content.subjectReset', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.FrontendLogin'), $this->settings['senderMail'], $email);
                }
            } else {
                throw new \Neos\Flow\Exception('The required field with the ID username is not defined.', 1347145544);
            }
        }
    }

}
