<?php
namespace BackendBundle\DataFixtures\ORM;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Faker\Factory;
use Nelmio\Alice\Fixtures;
use BackendBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Console\Helper\Helper;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

/**
 * Class LoadAliceFixtures
 */
class LoadAliceFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $ormFixtureDirs;

    /**
     * @var string
     */
    private $fileFixtureDir;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->ormFixtureDirs = [__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Resources'.
            DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'ORM'];
        $this->fileFixtureDir = '';
    }

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $stopWatch = new Stopwatch();

        $files = glob($this->container->getParameter('kernel.root_dir').'/documents/agent/*'); // get all file names
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        /**
         * @param string $message
         */
        $lapCallback = function ($message) use (&$stopWatch) {
            $event = $stopWatch->lap('lap');
            $eventPeriods = $event->getPeriods();
            $lastPeriod = array_pop($eventPeriods);
            echo sprintf(
                '%s => Execution time: %.2f seconds, memory usage: %s.%s',
                $message,
                $lastPeriod->getDuration() / 1000,
                Helper::formatMemory($lastPeriod->getMemory()),
                PHP_EOL
            );
        };

        $stopWatch->start('process');
        $stopWatch->start('lap');
        foreach ($this->getAliceFiles($this->ormFixtureDirs) as $file) {
            try {
                Fixtures::load(
                    $file,
                    $manager,
                    ['providers' => [$this]],
                    []
                );
                $lapCallback($file);
            } catch (\Exception $ex) {
                echo "Exception when loading file $file\n";
                throw $ex;
            }
        }
        $event = $stopWatch->stop('process');
        echo sprintf(
            'Done! Overall execution time: %.2f seconds, memory usage: %s.%s',
            $event->getDuration() / 1000,
            Helper::formatMemory($event->getMemory()),
            PHP_EOL
        );
    }

    /**
     * @return string
     */
    public function generateSalt()
    {
        return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }

    /**
     * @param string $plainPassword
     * @param string $salt
     * @return string
     */
    public function generatePassword($plainPassword, $salt)
    {
        $encoder = $this->container->get('security.encoder_factory')->getEncoder(new User());

        return $encoder->encodePassword($plainPassword, $salt);
    }

    /**
     * @param Object $object
     * @param string $collectionField
     * @return mixed
     */
    public function randomCollectionElement($object, $collectionField)
    {
        $this->manager->refresh($object);

        $getter = 'get'.ucfirst($collectionField);
        if (!method_exists($object, $getter) || !is_callable([$object, $getter])) {
            throw new \UnexpectedValueException(
                sprintf(
                    'Unable to load collection "%s" from the object "%s".',
                    $collectionField,
                    is_object($object) ? get_class($object) : gettype($object)
                )
            );
        }

        $collection = $object->$getter();
        if ($collection instanceof Collection) {
            $collection = $collection->toArray();
        }

        return Factory::create()->randomElement($collection);
    }

    /**
     * @param string  $filename
     * @param boolean $execute
     * @return UploadedFile|void
     * @throws \Exception
     */
    public function upload($filename, $execute = true)
    {
        if ($execute !== true) {
            return;
        }

        if (is_array($filename)) {
            $filename = \Faker\Provider\Base::randomElement($filename);
        }

        $rootDir = $this->container->getParameter('kernel.root_dir').'/db/files/';
        $filePath = $rootDir.$filename;

        $path = sprintf('/tmp/%s', uniqid());
        $copy = copy($filePath, $path);
        if (!$copy) {
            throw new \Exception('Copy failed');
        }

        $mimeType = MimeTypeGuesser::getInstance()->guess($path);
        $size = filesize($path);
        $file = new UploadedFile($path, $filePath, $mimeType, $size, null, true);

        return $file;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 0;
    }

    /**
     * @param string $paths
     * @return array|Finder
     */
    protected function getAliceFiles($paths)
    {
        return Finder::create()
            ->ignoreVCS(true)
            ->files()
            ->name('*.yml')
            ->sortByName()
            ->in($paths);
    }
}
