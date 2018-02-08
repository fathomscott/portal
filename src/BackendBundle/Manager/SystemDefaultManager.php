<?php
namespace BackendBundle\Manager;

use BackendBundle\Entity\SystemDefault;
use BackendBundle\Repository\SystemDefaultRepository;

/**
 * Class SystemDefaultManager
 */
class SystemDefaultManager
{
    /**
     * @var SystemDefaultRepository
     */
    private $systemDefaultRepository;

    /**
     * SystemDefaultManager constructor.
     * @param SystemDefaultRepository $systemDefaultRepository
     */
    public function __construct(SystemDefaultRepository $systemDefaultRepository)
    {
        $this->systemDefaultRepository = $systemDefaultRepository;
    }

    /**
     * @param SystemDefault $systemDefault
     */
    public function save(SystemDefault $systemDefault)
    {
        $this->systemDefaultRepository->save($systemDefault);
    }

    /**
     * @return SystemDefault|null
     */
    public function getSystemDefault()
    {
        return $this->systemDefaultRepository->getSystemDefault();
    }
}
