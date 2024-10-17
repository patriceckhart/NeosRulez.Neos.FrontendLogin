<?php
namespace NeosRulez\Neos\FrontendLogin\Finisher;

/*
 * This file is part of the NeosRulez.Neos.FrontendLogin package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Error\Messages\Error;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Http\Exception as HttpException;
use Neos\Flow\I18n\Translator;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Mvc\FlashMessage\FlashMessageService;
use Neos\Flow\Mvc\Routing\Exception\MissingActionNameException;
use Neos\Flow\Mvc\Routing\UriBuilder;
use Neos\Form\Core\Model\AbstractFinisher;
use Neos\Form\Exception\FinisherException;
use NeosRulez\Neos\FrontendLogin\Domain\Service\AuthenticationService;
use Psr\Http\Message\UriFactoryInterface;

/**
 * This finisher authenticate a frontend user
 */
class AuthenticationFinisher extends AbstractFinisher
{

    /**
     * @var UriBuilder
     */
    protected $uriBuilder;

    /**
     * @Flow\Inject
     * @var UriFactoryInterface
     */
    protected $uriFactory;

    /**
     * @Flow\Inject
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @Flow\Inject
     * @var Translator
     */
    protected $translator;

    /**
     * @Flow\Inject
     * @var FlashMessageService
     */
    protected $flashMessageService;

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
        $request = $formRuntime->getRequest()->getMainRequest();
        $response = $formRuntime->getResponse();
        $response->setStatusCode(200);

        if($formValues && array_key_exists('username', $this->getUsernamePasswordFromFormValues($formValues)) && array_key_exists('password', $this->getUsernamePasswordFromFormValues($formValues))) {
            $uri = null;
            if($this->authenticationService->authenticate($this->getUsernamePasswordFromFormValues($formValues)['username'], $this->getUsernamePasswordFromFormValues($formValues)['password'])) {
                if($this->parseOption('redirectAfterLoginSuccess') !== null) {
                    $uri = $this->buildUriPathForNode($this->parseOption('redirectAfterLoginSuccess'), $request);
                }
            } else {
                if($this->parseOption('redirectAfterLoginFailure') !== null) {
                    $uri = $this->buildUriPathForNode($this->parseOption('redirectAfterLoginFailure'), $request);
                } else {
                    $response->setRedirectUri($this->uriFactory->createUri($_SERVER['HTTP_REFERER']));
                }
            }
            if($uri !== null) {
                $response->setRedirectUri($this->uriFactory->createUri($uri));
            }
        }
    }

    /**
     * Creates a (relative) URI for the given $nodeContextPath removing the "@workspace-name" from the result
     *
     * @param NodeInterface $node
     * @param ActionRequest $request
     * @return string the resulting (relative) URI
     * @throws MissingActionNameException
     * @throws HttpException
     */
    protected function buildUriPathForNode(NodeInterface $node, ActionRequest $request): string
    {
        return $this->getUriBuilder($request)
            ->uriFor('show', ['node' => $node], 'Frontend\\Node', 'Neos.Neos');
    }

    /**
     * Creates an UriBuilder instance for the current request
     *
     * @param ActionRequest $request
     * @return UriBuilder
     */
    protected function getUriBuilder(ActionRequest $request): UriBuilder
    {
        if ($this->uriBuilder !== null) {
            return $this->uriBuilder;
        }

        $this->uriBuilder = new UriBuilder();
        $this->uriBuilder
            ->setFormat('html')
            ->setCreateAbsoluteUri(false)
            ->setRequest($request);

        return $this->uriBuilder;
    }

    /**
     * @param array $formValues
     * @return array
     */
    private function getUsernamePasswordFromFormValues(array $formValues): array
    {
        foreach ($formValues as $formValue) {
            if(is_array($formValue) && array_key_exists('username', $formValue) && array_key_exists('password', $formValue)) {
                return [
                    'username' => $formValue['username'],
                    'password' => $formValue['password']
                ];
            }
        }
        return [];
    }

}
