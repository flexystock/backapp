<?php

namespace App\Tests\Product\Infrastructure\InputAdapters;

use App\Product\Application\DTO\UpdateProductRequest;
use App\Product\Application\DTO\UpdateProductResponse;
use App\Product\Application\InputPorts\UpdateProductUseCaseInterface;
use App\Product\Infrastructure\InputAdapters\UpdateProductController;
use App\Security\PermissionService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateProductControllerTest extends TestCase
{
    private UpdateProductUseCaseInterface $useCase;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private PermissionService $permissionService;
    private UpdateProductController $controller;

    protected function setUp(): void
    {
        $this->useCase = $this->createMock(UpdateProductUseCaseInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->permissionService = $this->createMock(PermissionService::class);

        $this->controller = new UpdateProductController(
            $this->logger,
            $this->useCase,
            $this->serializer,
            $this->validator,
            $this->permissionService
        );

        // Inject a mock token storage so getUser() works
        // Use an anonymous class to provide the custom getUuid() method required by setUuidUserModification
        $user = new class implements UserInterface {
            public function getRoles(): array { return []; }
            public function eraseCredentials(): void {}
            public function getUserIdentifier(): string { return 'test@example.com'; }
            public function getUuid(): string { return 'user-uuid-abc'; }
        };

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn($token);

        // Inject token storage via the container (AbstractController uses the container)
        $container = new \Symfony\Component\DependencyInjection\Container();
        $container->set('security.token_storage', $tokenStorage);
        $this->controller->setContainer($container);
    }

    private function buildJsonRequest(): Request
    {
        $payload = json_encode([
            'uuidClient' => 'c014a415-4113-49e5-80cb-cc3158c15236',
            'uuidProduct' => '9a6ae1c0-3bc6-41c8-975a-4de5b4357666',
            'name' => 'Test Product',
        ]);

        return Request::create('/api/product_update', 'PUT', [], [], [], [], $payload);
    }

    private function stubSerializerAndValidator(): void
    {
        $dto = new UpdateProductRequest(
            'c014a415-4113-49e5-80cb-cc3158c15236',
            '9a6ae1c0-3bc6-41c8-975a-4de5b4357666',
            'Test Product',
        );

        $this->serializer
            ->method('deserialize')
            ->willReturn($dto);

        $this->validator
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->permissionService
            ->method('hasPermission')
            ->willReturn(true);
    }

    public function testControllerReturnsErrorJsonWhenUseCaseReturns404(): void
    {
        $this->stubSerializerAndValidator();

        $this->useCase
            ->method('execute')
            ->willReturn(new UpdateProductResponse(null, 'PRODUCT_NOT_FOUND', 404));

        $response = $this->controller->invoke($this->buildJsonRequest());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(404, $response->getStatusCode());

        $body = json_decode($response->getContent(), true);
        $this->assertSame('error', $body['status']);
        $this->assertSame('PRODUCT_NOT_FOUND', $body['message']);
    }

    public function testControllerReturnsErrorJsonWhenUseCaseReturns500(): void
    {
        $this->stubSerializerAndValidator();

        $this->useCase
            ->method('execute')
            ->willReturn(new UpdateProductResponse(null, 'Internal Server Error', 500));

        $response = $this->controller->invoke($this->buildJsonRequest());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(500, $response->getStatusCode());

        $body = json_decode($response->getContent(), true);
        $this->assertSame('error', $body['status']);
        $this->assertSame('Internal Server Error', $body['message']);
    }

    public function testControllerReturnsSuccessJsonWhenUseCaseReturns200(): void
    {
        $this->stubSerializerAndValidator();

        $productData = ['uuid' => '9a6ae1c0-3bc6-41c8-975a-4de5b4357666', 'name' => 'Test Product'];
        $this->useCase
            ->method('execute')
            ->willReturn(new UpdateProductResponse($productData, null, 200));

        $response = $this->controller->invoke($this->buildJsonRequest());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(200, $response->getStatusCode());

        $body = json_decode($response->getContent(), true);
        $this->assertSame('success', $body['status']);
        $this->assertSame('PRODUCT_UPDATED_SUCCESSFULLY', $body['message']);
        $this->assertSame($productData, $body['product']);
    }
}
