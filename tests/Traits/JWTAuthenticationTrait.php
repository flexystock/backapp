<?php

namespace App\Tests\Traits;

use App\Entity\Main\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Ramsey\Uuid\Uuid;

trait JWTAuthenticationTrait
{
    /**
     * Authenticate a test client with a JWT token
     */
    protected function authenticateClient(KernelBrowser $client, ?User $user = null): string
    {
        if ($user === null) {
            $user = $this->getOrCreateTestUser();
        }

        echo "\n📧 User email: " . $user->getEmail() . "\n";
        echo "🔑 User ID: " . $user->getUserIdentifier() . "\n";

        /** @var JWTTokenManagerInterface $jwtManager */
        $jwtManager = static::getContainer()->get('lexik_jwt_authentication.jwt_manager');
        
        try {
            $token = $jwtManager->create($user);
            echo "✅ Token generated successfully\n";
            echo "🔑 Token length: " . strlen($token) . "\n";
        } catch (\Exception $e) {
            echo "❌ Error generating token: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
            throw $e;
        }

        $client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', $token));

        return $token;
    }

    /**
     * Get existing test user or create a new one
     */
    protected function getOrCreateTestUser(): User
    {
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get('doctrine')->getManager();
        $userRepository = $em->getRepository(User::class);

        // Buscar usuario existente
        $existingUser = $userRepository->findOneBy(['email' => 'test@example.com']);
        
        if ($existingUser) {
            echo "♻️ Using existing test user\n";
            return $existingUser;
        }

        echo "🆕 Creating new test user\n";
        return $this->createTestUser($em);
    }

    /**
     * Create a test user for authentication
     */
    protected function createTestUser(EntityManagerInterface $em): User
    {
        $uuid = Uuid::uuid4()->toString();
        
        $user = new User();
        $user->setUuid($uuid);
        $user->setName('Test');
        $user->setSurnames('User');
        $user->setEmail('test@example.com');
        $user->setPassword('$2y$13$test_hashed_password');
        $user->setIsRoot(true);
        $user->setActive(true);
        $user->setUuidUserCreation($uuid);
        $user->setDatehourCreation(new \DateTime());
        
        // Persistir en BD
        $em->persist($user);
        $em->flush();
        
        echo "✅ Test user created with UUID: " . $uuid . "\n";
        
        return $user;
    }

    /**
     * Reset client authentication
     */
    protected function resetAuthentication(KernelBrowser $client): void
    {
        $client->setServerParameter('HTTP_AUTHORIZATION', '');
    }

    /**
     * Clean up test users after tests
     */
    protected function cleanupTestUsers(): void
    {
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get('doctrine')->getManager();
        $userRepository = $em->getRepository(User::class);
        
        $testUser = $userRepository->findOneBy(['email' => 'test@example.com']);
        
        if ($testUser) {
            $em->remove($testUser);
            $em->flush();
        }
    }
}
