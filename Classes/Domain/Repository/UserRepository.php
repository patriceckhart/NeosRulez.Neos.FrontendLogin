<?php
namespace NeosRulez\Neos\FrontendLogin\Domain\Repository;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class UserRepository extends Repository
{

    protected $defaultOrderings = array(
        'created' => \Neos\Flow\Persistence\QueryInterface::ORDER_DESCENDING,
    );

    /**
     * @param array $properties
     * @param \Neos\Neos\Domain\Model\User $user
     * @return void
     */
    public function createUser(array $properties, \Neos\Neos\Domain\Model\User $user):void
    {
        $newUser = new \NeosRulez\Neos\FrontendLogin\Domain\Model\User();
        $newUser->setProperties(json_encode($properties, JSON_FORCE_OBJECT));
        $newUser->setModified(new \DateTime);
        $newUser->setUser($user);
        $this->add($newUser);
    }
}
