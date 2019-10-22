<?php
namespace App\Faker\Provider;

use App\Entity\User;
use Faker\Generator;
use Faker\Provider\Base as BaseProvider;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class PasswordProvider extends BaseProvider
{
    private $password;
    private $passwordEncoder;
    public function __construct(Generator $generator, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($generator);
        $this->passwordEncoder = $passwordEncoder;
    }
    public function passwordGenerator(string $password)
    {
        $user = new User();
        $this->password = $this->passwordEncoder->encodePassword(
            $user,
            $password
        );

        return $this->password;
    }
}
