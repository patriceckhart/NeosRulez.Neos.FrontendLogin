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
class UpdateUserFinisher extends AbstractFinisher
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

            $user = $this->userRepository->findByUser($this->userService->getCurrentUser())->getFirst();
            if(!empty($user)) {
                $props = $user->getProperties();
                foreach ($properties as $propertyKey => $property) {
                    if(array_key_exists($propertyKey, $props)) {
                        $props[$propertyKey] = $property;
                    }
                }
                $user->setProperties(json_encode($props, JSON_FORCE_OBJECT));
                $user->setModified(new \DateTime());
                $this->userRepository->update($user);
                $name = new \Neos\Party\Domain\Model\PersonName('', $user->getProperty('firstname'), '', $user->getProperty('lastname'), '', $user->getProperty('username'));
                $user->getUser()->setName($name);
                $this->userService->updateUser($user->getUser());
            }
        }

    }

}
