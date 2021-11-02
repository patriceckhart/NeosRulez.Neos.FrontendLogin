<?php
namespace NeosRulez\Neos\FrontendLogin\Fusion;

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use Neos\Fusion\FusionObjects\AbstractFusionObject;

class UserValuesFusion extends AbstractFusionObject {

    /**
     * @Flow\Inject
     * @var \NeosRulez\Neos\FrontendLogin\Domain\Service\UserService
     */
    protected $userService;

    /**
     * @Flow\Inject
     * @var \NeosRulez\Neos\FrontendLogin\Domain\Repository\UserRepository
     */
    protected $userRepository;


    /**
     * @return mixed
     */
    public function evaluate() {
        $identifier = $this->fusionValue('identifier');
        $result = false;
        if($identifier) {
            $user = $this->userRepository->findByUser($this->userService->getCurrentUser())->getFirst();
            if($user) {
                $result = $user->getProperty($identifier);
            }
        }
        return $result;
    }

}
