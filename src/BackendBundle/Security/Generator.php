<?php
namespace BackendBundle\Security;

use BackendBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * Class Generator
 */
class Generator
{
    /**
     * @var EncoderFactory
     */
    protected $encoderFactory;

    /**
     * @param EncoderFactory $encoderFactory
     */
    public function __construct(EncoderFactory $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param string $plainPassword
     * @param string $salt
     * @return string
     */
    public function encodePassword($plainPassword, $salt)
    {
        /** @var PasswordEncoderInterface $encoder */
        $encoder = $this->encoderFactory->getEncoder(User::class);

        return $encoder->encodePassword($plainPassword, $salt);
    }

    /**
     * @return string
     */
    public function generateSalt()
    {
        return $this->generateRandomString();
    }

    /**
     * @param int $length
     * @return string
     */
    public function generateRandomPlainPassword($length = 6)
    {
        return substr(base64_encode(sha1(uniqid(mt_rand(), true))), 0, $length);
    }

    /**
     * @return string
     */
    protected function generateRandomString()
    {
        $bytes = random_bytes(20);
        $hexEncoded = bin2hex($bytes);

        return base_convert($hexEncoded, 16, 36);
    }
}
