<?php
namespace NeosRulez\Neos\FrontendLogin\Controller\Module;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Security\AccountRepository;
use Neos\Flow\Security\Policy\Role;
use Neos\Fusion\View\FusionView;

class UserController extends ActionController
{

    protected $defaultViewObjectName = FusionView::class;

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
     * @var AccountRepository
     */
    protected $accountRepository;


    /**
     * @return void
     */
    public function indexAction():void
    {
        $users = $this->userRepository->findAll(false);
        $this->view->assign('users', $users);
    }

    /**
     * @param \NeosRulez\Neos\FrontendLogin\Domain\Model\User $user
     * @return void
     */
    public function editAction(\NeosRulez\Neos\FrontendLogin\Domain\Model\User $user):void
    {
        $this->view->assign('user', $user);
    }

    /**
     * @param \NeosRulez\Neos\FrontendLogin\Domain\Model\User $user
     * @param array $properties
     * @return void
     */
    public function updateAction(\NeosRulez\Neos\FrontendLogin\Domain\Model\User $user, array $properties):void
    {
        $props = $user->getProperties();
        foreach ($properties as $propertyKey => $property) {
            if(array_key_exists($propertyKey, $props)) {
                $props[$propertyKey] = $property;
            }
        }
        $user->setProperties(json_encode($props, JSON_FORCE_OBJECT));
        $user->setModified(new \DateTime());
        $this->userRepository->update($user);
        if($user->getUser() !== null) {
            $name = new \Neos\Party\Domain\Model\PersonName('', $user->getProperty('firstname'), '', $user->getProperty('lastname'), '', $user->getProperty('username'));
            $neosUser = $user->getUser();
            $neosUser->setName($name);
            $account = $neosUser->getAccounts()[0];
            if(array_key_exists('role', $properties)) {
                $account->setRoles([new Role($properties['role'])]);
            }
            $this->accountRepository->update($account);
            $this->userService->updateUser($neosUser);
        }
        $this->redirect('index');
    }

    /**
     * @param \NeosRulez\Neos\FrontendLogin\Domain\Model\User $user
     * @return void
     */
    public function activateAction(\NeosRulez\Neos\FrontendLogin\Domain\Model\User $user):void
    {
        $this->userService->activateUser($user->getUser());
        $user->setActive(true);
        $user->setModified(new \DateTime());
        $this->userRepository->update($user);
        $this->persistenceManager->persistAll();
        $this->redirect('index');
    }

    /**
     * @param \NeosRulez\Neos\FrontendLogin\Domain\Model\User $user
     * @return void
     */
    public function deactivateAction(\NeosRulez\Neos\FrontendLogin\Domain\Model\User $user):void
    {
        $this->userService->deactivateUser($user->getUser());
        $user->setActive(false);
        $user->setModified(new \DateTime());
        $this->userRepository->update($user);
        $this->persistenceManager->persistAll();
        $this->redirect('index');
    }

    /**
     * @param \NeosRulez\Neos\FrontendLogin\Domain\Model\User $user
     * @return void
     */
    public function removeAction(\NeosRulez\Neos\FrontendLogin\Domain\Model\User $user):void
    {
        if($user->getUser() !== null) {
            $this->userService->deleteUser($user->getUser());
        }
        $this->userRepository->remove($user);
        $this->persistenceManager->persistAll();
        $this->redirect('index');
    }

}
