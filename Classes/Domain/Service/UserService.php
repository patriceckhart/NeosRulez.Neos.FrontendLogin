<?php
namespace NeosRulez\Neos\FrontendLogin\Domain\Service;

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use Neos\Neos\Domain\Model\User as User;

/**
 *
 * @Flow\Scope("singleton")
 */
class UserService extends \Neos\Neos\Domain\Service\UserService
{

    /**
     * @var string
     */
    protected $defaultAuthenticationProviderName = 'NeosRulez.Neos.FrontendLogin:NeosFrontend';

    /**
     * Adds a user whose User object has been created elsewhere
     *
     * This method basically "creates" a user like createUser() would, except that it does not create the User
     * object itself. If you need to create the User object elsewhere, for example in your ActionController, make sure
     * to call this method for registering the new user instead of adding it to the PartyRepository manually.
     *
     * This method also creates a new user workspace for the given user if no such workspace exist.
     *
     * @param string $username The username of the user to be created.
     * @param string $password Password of the user to be created
     * @param User $user The pre-built user object to start with
     * @param array $roleIdentifiers A list of role identifiers to assign
     * @param string $authenticationProviderName Name of the authentication provider to use. Example: "Neos.Neos:Backend"
     * @return User The same user object
     * @api
     */
    public function addUser($username, $password, User $user, array $roleIdentifiers = null, $authenticationProviderName = null)
    {
        $roleIdentifiers = $this->normalizeRoleIdentifiers($roleIdentifiers);
        $account = $this->accountFactory->createAccountWithPassword($username, $password, $roleIdentifiers, $authenticationProviderName ?: $this->defaultAuthenticationProviderName);
        $this->partyService->assignAccountToParty($account, $user);

        $this->partyRepository->add($user);
        $this->accountRepository->add($account);

        $this->emitUserCreated($user);

        $this->persistenceManager->persistAll();

        return $user;
    }

    /**
     * @param string $length
     * @return string
     */
    public function generatePassword($length = 8):string
    {
        $chars = '23456789bcdfhkmnprstvzBCDFHJKLMNPRSTVZ';
        $shuffled = str_shuffle($chars);
        $result = mb_substr($shuffled, 0, $length);
        return $result;
    }

//    /**
//     * @return \NeosRulez\Neos\FrontendLogin\Domain\Model\User
//     */
//    public function getLoggedInUser():\NeosRulez\Neos\FrontendLogin\Domain\Model\User
//    {
//        $result = [];
//        if ($this->authenticationManager->isAuthenticated() === true) {
//            $result = $this->externalUserRepository->findByAccount($this->securityContext->getAccount())->getFirst();
//        }
//        return $result;
//    }

}
