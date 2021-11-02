<?php
namespace NeosRulez\Neos\FrontendLogin\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Security\Authentication\Controller\AbstractAuthenticationController;
use Neos\Flow\Mvc\RequestInterface;
use Neos\Flow\Security\RequestPatternInterface;

class LoginController extends AbstractAuthenticationController
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
     * @var \Neos\Flow\I18n\Translator
     */
    protected $translator;

    /**
     * @param ActionRequest $originalRequest The request that was intercepted by the security framework, NULL if there was none
     * @return void
     */
    protected function onAuthenticationSuccess(ActionRequest $originalRequest = null)
    {
        $user = $this->userRepository->findByUser($this->userService->getCurrentUser())->getFirst();
        if($user->getActive()) {
            if($this->request->hasArgument('redirectAfterLoginTarget')) {
                $redirectUri = $this->request->getArgument('redirectAfterLoginTarget');
                $this->redirectToUri($redirectUri);
            } else {
                $this->redirectToUri('/');
            }
        } else {
            $this->logoutAction();
        }
    }

    /**
     * Is called if authentication failed.
     *
     * Override this method in your login controller to take any
     * custom action for this event. Most likely you would want
     * to redirect to some action showing the login form again.
     *
     * @param \Neos\Flow\Security\Exception\AuthenticationRequiredException $exception The exception thrown while the authentication process
     * @return void
     */
    protected function onAuthenticationFailure(\Neos\Flow\Security\Exception\AuthenticationRequiredException $exception = null)
    {

    }

    /**
     * A template method for displaying custom error flash messages, or to
     * display no flash message at all on errors. Override this to customize
     * the flash message in your action controller.
     *
     * Note: If you implement a nice redirect in the onAuthenticationFailure()
     * method of you login controller, this message should never be displayed.
     *
     * @return \Neos\Error\Messages\Error The flash message
     * @api
     */
    protected function getErrorFlashMessage()
    {
        $title = $this->translator->translateById('content.failure.title', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.FrontendLogin');
        $message = $this->translator->translateById('content.failure.message', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.FrontendLogin');
        return new \Neos\Error\Messages\Error($message, null, [], $title);
    }

    /**
     * return void
     */
    public function logoutAction()
    {
        parent::logoutAction();
        if($this->request->hasArgument('redirectAfterLogoutTarget')) {
            $redirectUri = $this->request->getArgument('redirectAfterLogoutTarget');
            $this->redirectToUri($redirectUri);
        } else {
            $this->redirectToUri('/');
        }
    }

}
