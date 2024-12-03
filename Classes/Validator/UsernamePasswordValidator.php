<?php
namespace NeosRulez\Neos\FrontendLogin\Validator;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\I18n\Translator;
use Neos\Flow\Validation\Validator\AbstractValidator;
use NeosRulez\Neos\FrontendLogin\Domain\Service\AuthenticationService;

/**
 * @api
 * @Flow\Scope("singleton")
 */
class UsernamePasswordValidator extends AbstractValidator
{

    /**
     * @var boolean
     */
    protected $acceptsEmptyValues = false;

    protected $supportedOptions = [];

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
     * @param mixed $value The value that should be validated
     * @return void
     * @api
     */
    protected function isValid($value)
    {
        if(is_array($value) && array_key_exists('username', $value) && array_key_exists('password', $value)) {
            if(!$this->authenticationService->validatePassword($value['username'], $value['password'])) {
                $this->addError($this->translator->translateById('content.failure.message', [], null, null, $sourceName = 'Main', $packageKey = 'NeosRulez.Neos.FrontendLogin'), 0);
            }
        } else {
            $this->addError('The username and password properties are required.', 0);
        }
    }
}
