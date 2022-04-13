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
class CreateUserFinisher extends AbstractFinisher
{

    /**
     * @Flow\Inject
     * @var \NeosRulez\Neos\FrontendLogin\Domain\Repository\UserRepository
     */
    protected $userRepository;

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
     * @Flow\Inject
     * @var \Neos\Flow\Persistence\PersistenceManagerInterface
     */
    protected $persistenceManager;

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

        $role = false;
        $sendCredentials = false;
        $adminConfirmation = false;
        $adminMail = false;
        $finishers = $formRuntime->getFormDefinition()->getFinishers();
        foreach ($finishers as $finisher) {
            if(get_class($finisher) == 'NeosRulez\Neos\FrontendLogin\Finisher\CreateUserFinisher') {
                if(array_key_exists('role', (array) $finisher->options)) {
                    $role = $finisher->options['role'];
                }
                if(array_key_exists('sendCredentials', (array) $finisher->options)) {
                    $sendCredentials = $finisher->options['sendCredentials'];
                }
                if(array_key_exists('adminConfirmation', (array) $finisher->options)) {
                    $adminConfirmation = $finisher->options['adminConfirmation'];
                    if(array_key_exists('adminMail', (array) $finisher->options)) {
                        $adminMail = $finisher->options['adminMail'];
                    }
                }
            }
        }

        if($role && array_key_exists('username', $formValues) && array_key_exists('firstname', $formValues) && array_key_exists('lastname', $formValues)) {
            if(!empty($formValues)) {
                $properties = [];
                $properties['active'] = !$adminConfirmation;
                foreach ($formValues as $formValueIterator => $formValue) {
                    $properties[$formValueIterator] = $formValue;
                }

                if(filter_var($properties['username'], FILTER_VALIDATE_EMAIL)) {
                    $password = $this->userService->generatePassword();
                    $user = $this->userService->createUser($properties['username'], $password, $properties['firstname'], $properties['lastname'], [$role]);
                    $this->userRepository->createUser($properties, $user);
                    if($sendCredentials) {
                        $this->mailService->sendMail($this->settings['createUser']['templatePathAndFilename'], ['username' => $properties['username'], 'password' => $password, 'adminConfirmation' => $adminConfirmation], $this->translator->translateById('content.subjectCreate', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.FrontendLogin'), $this->settings['senderMail'], $properties['username']);
                    }
                    if($adminConfirmation && $adminMail) {
                        $this->mailService->sendMail($this->settings['createUser']['confirmation']['templatePathAndFilename'], ['username' => $properties['username']], $this->translator->translateById('content.subjectCreateConfirm', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.FrontendLogin'), $this->settings['senderMail'], $adminMail);
                    }
                } else {
                    throw new \Neos\Flow\Exception('Username must be an email address', 1347145544);
                }

            }
        } else {
            throw new \Neos\Flow\Exception('One or more of the required form fields with IDs username, firstname and lastname are not defined. Maybe you forgot to define the role at the finisher?', 1347145544);
        }

    }

}
