<?php

namespace AlipayMiniProgramBundle\Tests\Service;

use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use AlipayMiniProgramBundle\Entity\Phone;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use AlipayMiniProgramBundle\Repository\PhoneRepository;
use AlipayMiniProgramBundle\Repository\UserRepository;
use AlipayMiniProgramBundle\Service\UserService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;

class UserServiceTest extends TestCase
{
    private EntityManagerInterface|MockObject $entityManager;
    private PhoneRepository|MockObject $phoneRepository;
    private UserLoaderInterface|MockObject $userLoader;
    private UserRepository|MockObject $alipayUserRepository;
    private UserService $userService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->phoneRepository = $this->createMock(PhoneRepository::class);
        $this->userLoader = $this->createMock(UserLoaderInterface::class);
        $this->alipayUserRepository = $this->createMock(UserRepository::class);
        
        $this->userService = new UserService(
            $this->entityManager,
            $this->phoneRepository,
            $this->userLoader,
            $this->alipayUserRepository
        );
    }

    public function testUpdateUserInfo_withAllFields(): void
    {
        // Arrange
        $user = new User();
        $userInfo = [
            'nick_name' => 'Test User',
            'avatar' => 'https://example.com/avatar.jpg',
            'province' => 'Test Province',
            'city' => 'Test City',
            'gender' => AlipayUserGender::MALE->value,
        ];
        
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($user);
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // Act
        $this->userService->updateUserInfo($user, $userInfo);
        
        // Assert
        $this->assertEquals('Test User', $user->getNickName());
        $this->assertEquals('https://example.com/avatar.jpg', $user->getAvatar());
        $this->assertEquals('Test Province', $user->getProvince());
        $this->assertEquals('Test City', $user->getCity());
        $this->assertEquals(AlipayUserGender::MALE, $user->getGender());
        $this->assertInstanceOf(\DateTimeInterface::class, $user->getLastInfoUpdateTime());
    }
    
    public function testUpdateUserInfo_withPartialFields(): void
    {
        // Arrange
        $user = new User();
        $userInfo = [
            'nick_name' => 'Test User',
            // No avatar, province, city, gender
        ];
        
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($user);
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // Act
        $this->userService->updateUserInfo($user, $userInfo);
        
        // Assert
        $this->assertEquals('Test User', $user->getNickName());
        $this->assertNull($user->getAvatar());
        $this->assertNull($user->getProvince());
        $this->assertNull($user->getCity());
        $this->assertNull($user->getGender());
        $this->assertInstanceOf(\DateTimeInterface::class, $user->getLastInfoUpdateTime());
    }
    
    public function testBindPhone_withNewPhoneNumber(): void
    {
        // Arrange
        $user = new User();
        $phoneNumber = '13800138000';
        
        $this->phoneRepository->expects($this->once())
            ->method('findByNumber')
            ->with($phoneNumber)
            ->willReturn(null);
            
        $this->entityManager->expects($this->exactly(2))
            ->method('persist')
            ->willReturnCallback(function ($entity) use ($user, $phoneNumber) {
                static $callCount = 0;
                $callCount++;
                
                if ($callCount === 1) {
                    $this->assertInstanceOf(Phone::class, $entity);
                    $this->assertEquals($phoneNumber, $entity->getNumber());
                } else {
                    $this->assertInstanceOf(AlipayUserPhone::class, $entity);
                    $this->assertSame($user, $entity->getUser());
                    $this->assertInstanceOf(Phone::class, $entity->getPhone());
                    $this->assertInstanceOf(\DateTimeInterface::class, $entity->getVerifiedTime());
                }
                
                return null;
            });
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // Act
        $this->userService->bindPhone($user, $phoneNumber);
    }
    
    public function testBindPhone_withExistingPhoneNumber(): void
    {
        // Arrange
        $user = new User();
        $phoneNumber = '13800138000';
        $existingPhone = new Phone();
        $existingPhone->setNumber($phoneNumber);
        
        $this->phoneRepository->expects($this->once())
            ->method('findByNumber')
            ->with($phoneNumber)
            ->willReturn($existingPhone);
            
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($entity) use ($user, $existingPhone) {
                return $entity instanceof AlipayUserPhone 
                    && $entity->getUser() === $user
                    && $entity->getPhone() === $existingPhone
                    && $entity->getVerifiedTime() instanceof \DateTimeInterface;
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // Act
        $this->userService->bindPhone($user, $phoneNumber);
    }
    
    public function testGetLatestPhone_whenPhoneExists(): void
    {
        // Arrange
        $user = $this->createMock(User::class);
        $phone = new Phone();
        $phone->setNumber('13800138000');
        
        $user->expects($this->once())
            ->method('getLatestPhone')
            ->willReturn($phone);
        
        // Act
        $result = $this->userService->getLatestPhone($user);
        
        // Assert
        $this->assertEquals('13800138000', $result);
    }
    
    public function testGetLatestPhone_whenNoPhoneExists(): void
    {
        // Arrange
        $user = $this->createMock(User::class);
        
        $user->expects($this->once())
            ->method('getLatestPhone')
            ->willReturn(null);
        
        // Act
        $result = $this->userService->getLatestPhone($user);
        
        // Assert
        $this->assertNull($result);
    }
    
    public function testGetAllPhones(): void
    {
        // Arrange
        $user = $this->createMock(User::class);
        $phone1 = new Phone();
        $phone1->setNumber('13800138000');
        $phone2 = new Phone();
        $phone2->setNumber('13900139000');
        
        $userPhone1 = new AlipayUserPhone();
        $userPhone1->setPhone($phone1);
        $userPhone2 = new AlipayUserPhone();
        $userPhone2->setPhone($phone2);
        
        $phoneCollection = new ArrayCollection([$userPhone1, $userPhone2]);
        
        $user->expects($this->once())
            ->method('getUserPhones')
            ->willReturn($phoneCollection);
        
        // Act
        $result = $this->userService->getAllPhones($user);
        
        // Assert
        $this->assertEquals(['13800138000', '13900139000'], $result);
    }
    
    public function testGetUserInterface_whenUserExists(): void
    {
        // Arrange
        $user = $this->createMock(User::class);
        $bizUser = $this->createMock(UserInterface::class);
        
        $user->expects($this->once())
            ->method('getOpenId')
            ->willReturn('test_open_id');
            
        $this->userLoader->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('test_open_id')
            ->willReturn($bizUser);
        
        // Act
        $result = $this->userService->getBizUser($user);
        
        // Assert
        $this->assertSame($bizUser, $result);
    }
    
    public function testGetAlipayUser_whenFoundByUsername(): void
    {
        // Arrange
        $bizUser = $this->createMock(UserInterface::class);
        $alipayUser = $this->createMock(User::class);
        
        $bizUser->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('test_open_id');
            
        $this->alipayUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['openId' => 'test_open_id'])
            ->willReturn($alipayUser);
        
        // Act
        $result = $this->userService->getAlipayUser($bizUser);
        
        // Assert
        $this->assertSame($alipayUser, $result);
    }
    
    public function testGetAlipayUser_whenNotFound(): void
    {
        // Arrange
        $bizUser = $this->createMock(UserInterface::class);
        
        $bizUser->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('test_open_id');
            
        $this->alipayUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['openId' => 'test_open_id'])
            ->willReturn(null);
        
        // Act
        $result = $this->userService->getAlipayUser($bizUser);
        
        // Assert
        $this->assertNull($result);
    }
    
    public function testRequireAlipayUser_whenUserExists(): void
    {
        // Arrange
        $bizUser = $this->createMock(UserInterface::class);
        $alipayUser = $this->createMock(User::class);
        
        $bizUser->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('test_open_id');
            
        $this->alipayUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['openId' => 'test_open_id'])
            ->willReturn($alipayUser);
        
        // Act
        $result = $this->userService->requireAlipayUser($bizUser);
        
        // Assert
        $this->assertSame($alipayUser, $result);
    }
    
    public function testRequireAlipayUser_whenUserDoesNotExist(): void
    {
        // Arrange
        $bizUser = $this->createMock(UserInterface::class);
        
        $bizUser->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('test_open_id');
            
        $this->alipayUserRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['openId' => 'test_open_id'])
            ->willReturn(null);
        
        // Assert
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('未找到对应的支付宝用户');
        
        // Act
        $this->userService->requireAlipayUser($bizUser);
    }
} 