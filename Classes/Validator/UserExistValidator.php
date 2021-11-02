<?php
namespace NeosRulez\Neos\FrontendLogin\Validator;

use Neos\Flow\Annotations as Flow;

/**
 * @api
 * @Flow\Scope("singleton")
 */
class UserExistValidator extends \Neos\Flow\Validation\Validator\AbstractValidator
{
    /**
     * @Flow\Inject
     * @var \NeosRulez\Neos\FrontendLogin\Domain\Service\UserService
     */
    protected $userService;

    /**
     * @var boolean
     */
    protected $acceptsEmptyValues = false;

    protected $supportedOptions = array(
        'trueIfExist' => array([], 'Is true if the user already exists', 'array', true)
    );

    /**
     * @param mixed $value The value that should be validated
     * @return void
     * @api
     */
    protected function isValid($value)
    {
        if(empty($value)) {
            $this->addError('This property is required.', 1221560910);
        } else {
            if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->addError('Please specify a valid email address', 1221559976);
            } else {
                $existingUser = $this->userService->getUser($value, 'NeosRulez.Neos.FrontendLogin:NeosFrontend');
                if(array_key_exists('trueIfExist', $this->options)) {
                    if($this->options['trueIfExist']) {
                        if(empty($existingUser)) {
                            $this->addError('The username does not exist.', 1635844210);
                        }
                    } else {
                        if(!empty($existingUser)) {
                            $this->addError('The username already exists.', 1635844257);
                        }
                    }
                }
            }
        }
    }
}
