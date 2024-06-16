<?php
namespace App\User\Application;

use App\Entity\Main\User;
use App\Entity\Main\Client;
use App\User\Infrastructure\InputPorts\RegisterUserInputPort;
use App\User\Infrastructure\OutputPorts\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Uid\Uuid;
class RegisterUserUseCase implements RegisterUserInputPort
{
    private $userRepository;
    private $passwordHasher;
    private $validator;

    public function __construct(UserRepositoryInterface $userRepository, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
    }

    public function register(array $data): User
    {
        $user = new User();
        $uuid = Uuid::v4()->toRfc4122();// Generar UUID
        $user->setUuid($uuid);
        $user->setEmail($data['mail']);
        $user->setName($data['name']);
        $user->setSurnames($data['surnames']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $data['password'])
        );
        $user->setUuidUserCreation($uuid); // Generar UUID para el usuario que creó el registro
        $user->setDatehourCreation(new \DateTime()); // Fecha y hora de creación

        // Validar el usuario
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new \Exception((string) $errors);
        }

        // Guardar el usuario en la base de datos
        $this->userRepository->save($user);

        return $user;
    }
}