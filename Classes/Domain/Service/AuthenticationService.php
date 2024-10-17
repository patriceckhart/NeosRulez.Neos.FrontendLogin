<?php
namespace NeosRulez\Neos\FrontendLogin\Domain\Service;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Log\Utility\LogEnvironment;
use Neos\Flow\Security\AccountRepository;
use Neos\Flow\Security\Authentication\AuthenticationManagerInterface;
use Neos\Flow\Security\Authentication\Token\UsernamePassword;
use Neos\Flow\Security\Authentication\TokenInterface;
use Neos\Flow\Security\Context;
use Neos\Flow\Security\Cryptography\HashService;
use Psr\Log\LoggerInterface;

class AuthenticationService
{

    /**
     * @var AuthenticationManagerInterface
     * @Flow\Inject
     */
    protected $authenticationManager;

    /**
     * @var Context
     * @Flow\Inject
     */
    protected $securityContext;

    /**
     * @var AccountRepository
     * @Flow\Inject
     */
    protected $accountRepository;

    /**
     * @var HashService
     * @Flow\Inject
     */
    protected $hashService;

    /**
     * @Flow\Inject(name="Neos.Flow:SecurityLogger")
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param string $accountIdentifier
     * @param string $password
     * @return bool
     */
    public function authenticate(string $accountIdentifier, string $password): bool
    {
        $this->logger->debug(sprintf('NeosRulez.Neos.FrontendLogin: Starting authentication for user "%s" ...', $accountIdentifier), LogEnvironment::fromMethodName(__METHOD__));

        $account = $this->accountRepository->findActiveByAccountIdentifierAndAuthenticationProviderName($accountIdentifier, 'NeosRulez.Neos.FrontendLogin:NeosFrontend');
        if($account !== null) {
            if($this->hashService->validatePassword($password, $account->getCredentialsSource())) {

                $account->authenticationAttempted(TokenInterface::AUTHENTICATION_SUCCESSFUL);

                $tokens = $this->securityContext->getAuthenticationTokensOfType(UsernamePassword::class);

                /* @var UsernamePassword $token */
                foreach ($tokens as $token) {
                    $token->setAccount($account);
                    $token->setAuthenticationStatus(TokenInterface::AUTHENTICATION_SUCCESSFUL);
                }
                $this->authenticationManager->authenticate();

                $this->logger->debug(sprintf('NeosRulez.Neos.FrontendLogin: Successfully authenticated account "%s" with authentication provider %s.', $account->getAccountIdentifier(), $account->getAuthenticationProviderName()), LogEnvironment::fromMethodName(__METHOD__));

                return true;
            }

            $this->logger->debug(sprintf('NeosRulez.Neos.FrontendLogin: Authentication with account "%s" and authentication provider %s cannot be performed.', $account->getAccountIdentifier(), $account->getAuthenticationProviderName()), LogEnvironment::fromMethodName(__METHOD__));
        }
        return false;
    }

}
