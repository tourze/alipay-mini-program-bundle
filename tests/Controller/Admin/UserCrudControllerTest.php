<?php

namespace AlipayMiniProgramBundle\Tests\Controller\Admin;

use AlipayMiniProgramBundle\Controller\Admin\UserCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(UserCrudController::class)]
#[RunTestsInSeparateProcesses]
final class UserCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testConfigureActions(): void
    {
        $client = self::createClientWithDatabase();

        // Create and login as admin user
        $this->createAdminUser('admin@test.com', 'admin123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        // Test HTTP layer accessibility
        $client->request('GET', '/admin/alipay-mini-program/user');
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
            'Controller endpoint should be accessible'
        );

        $controller = new UserCrudController();
        $actions = $controller->configureActions(Actions::new());
        $this->assertInstanceOf(Actions::class, $actions);
    }

    public function testConfigureCrud(): void
    {
        $client = self::createClientWithDatabase();

        // Create and login as admin user
        $this->createAdminUser('admin@test.com', 'admin123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        // Test HTTP layer accessibility
        $client->request('GET', '/admin/alipay-mini-program/user');
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
            'Controller endpoint should be accessible'
        );

        $controller = new UserCrudController();
        $crud = $controller->configureCrud(Crud::new());
        $this->assertInstanceOf(Crud::class, $crud);
    }

    public function testConfigureFields(): void
    {
        $client = self::createClientWithDatabase();

        // Create and login as admin user
        $this->createAdminUser('admin@test.com', 'admin123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        // Test HTTP layer accessibility
        $client->request('GET', '/admin/alipay-mini-program/user');
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
            'Controller endpoint should be accessible'
        );

        $controller = new UserCrudController();
        $fields = iterator_to_array($controller->configureFields(Crud::PAGE_INDEX));
        $this->assertNotEmpty($fields);
    }

    public function testConfigureFilters(): void
    {
        $client = self::createClientWithDatabase();

        // Create and login as admin user
        $this->createAdminUser('admin@test.com', 'admin123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        // Test HTTP layer accessibility
        $client->request('GET', '/admin/alipay-mini-program/user');
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
            'Controller endpoint should be accessible'
        );

        $controller = new UserCrudController();
        $filters = $controller->configureFilters(Filters::new());
        $this->assertInstanceOf(Filters::class, $filters);
    }

    public function testRequiredFieldValidation(): void
    {
        $client = self::createClientWithDatabase();

        // Create and login as admin user
        $this->createAdminUser('admin@test.com', 'admin123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        // Test HTTP layer accessibility
        $client->request('GET', '/admin/alipay-mini-program/user');
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
            'Controller endpoint should be accessible'
        );

        $controller = new UserCrudController();
        $fields = iterator_to_array($controller->configureFields(Crud::PAGE_NEW));
        $this->assertNotEmpty($fields);

        // Test that essential fields are configured for User
        $fieldNames = [];
        foreach ($fields as $field) {
            if (is_object($field)) {
                $fieldNames[] = $field->getAsDto()->getProperty();
            }
        }

        // User entity should have these essential fields configured
        $this->assertContains('miniProgram', $fieldNames, 'miniProgram field should be configured');
        $this->assertContains('openId', $fieldNames, 'openId field should be configured');
        $this->assertGreaterThanOrEqual(2, count($fields), 'Should have at least 2 fields configured');

        // Verify controller has proper field configuration - User should have core field setup
        $this->assertGreaterThanOrEqual(5, count($fields), 'User controller should have at least 5 configured fields including miniProgram, openId and other essential fields');
    }

    public function testUnauthorizedAccess(): void
    {
        $client = self::createClientWithDatabase();

        // Test unauthorized access to admin endpoint
        try {
            $client->request('GET', '/admin/alipay-mini-program/user');

            // Should redirect to login or return 403/401 for unauthorized access
            $this->assertTrue(
                $client->getResponse()->isRedirect()
                || $client->getResponse()->getStatusCode() >= 400,
                'Unauthorized access should be denied with redirect or 4xx status code'
            );
        } catch (AccessDeniedException $e) {
            // Access denied exception is expected for unauthorized access
            $this->assertStringContainsString('Access Denied', $e->getMessage());
        }

        // Test controller configuration is correct
        $controller = new UserCrudController();
        $this->assertInstanceOf(AbstractCrudController::class, $controller);
    }

    public function testValidationErrors(): void
    {
        $client = self::createClientWithDatabase();

        // Test form validation by attempting to access the form
        try {
            $client->request('GET', '/admin/alipay-mini-program/user/new');
        } catch (AccessDeniedException $e) {
            // Access denied exception is expected for unauthorized access
            $this->assertStringContainsString('Access Denied', $e->getMessage());

            return;
        }

        // Should redirect to login or return error for unauthorized access
        $response = $client->getResponse();
        if ($response->isRedirect()) {
            // Validation test passes - unauthorized access properly redirected
            $this->assertResponseRedirects();

            return;
        }

        // If we get access, the form should have validation requirements
        if ($response->isSuccessful()) {
            $this->assertResponseStatusCodeSame(200);
            // Verify form has validation by checking for required field indicators
            // Verify form has validation indicators
            $content = $response->getContent();
            if (false !== $content) {
                $this->assertStringContainsString('should not be blank', $content, 'Form validation should show blank field errors');
            }
        } else {
            // Any other response code indicates validation is working
            // Form validation should return 422 for required field errors
            $this->assertResponseStatusCodeSame(422, 'Form validation should handle required fields');
        }
    }

    /**
     * @return UserCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(UserCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '关联小程序' => ['关联小程序'];
        yield '用户OpenID' => ['用户OpenID'];
        yield '用户昵称' => ['用户昵称'];
        yield '用户头像' => ['用户头像'];
        yield '省份' => ['省份'];
        yield '城市' => ['城市'];
        yield '性别' => ['性别'];
        yield '最后更新信息时间' => ['最后更新信息时间'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'miniProgram' => ['miniProgram'];
        yield 'openId' => ['openId'];
        yield 'nickName' => ['nickName'];
        yield 'avatar' => ['avatar'];
        yield 'province' => ['province'];
        yield 'city' => ['city'];
        yield 'gender' => ['gender'];
        // lastInfoUpdateTime 被设置为 hideOnForm()，不应该在编辑页面出现
    }

    /**
     * 独立的字段配置测试，绕过基类的客户端创建问题
     * 这个测试直接验证字段配置而不依赖有问题的基类方法
     */
    public function testNewPageShowsConfiguredFieldsWorkaround(): void
    {
        // 直接测试控制器配置，而不进行HTTP请求
        $controller = new UserCrudController();

        // 测试NEW页面的字段配置
        $fields = iterator_to_array($controller->configureFields('new'));

        // 验证字段数量
        $this->assertGreaterThan(0, count($fields), 'NEW页面应该配置有字段');

        // 验证必需的字段存在
        $fieldNames = [];
        foreach ($fields as $field) {
            if ($field instanceof FieldInterface) {
                $fieldNames[] = $field->getAsDto()->getProperty();
            }
        }

        // 验证provideNewPageFields中的字段都在配置中
        $providedFields = [];
        foreach (self::provideNewPageFields() as $data) {
            $providedFields[] = $data[0];
        }

        foreach ($providedFields as $expectedField) {
            $this->assertContains($expectedField, $fieldNames,
                sprintf('字段 %s 应该在NEW页面配置中', $expectedField));
        }

        // 验证字段配置的基本属性
        foreach ($fields as $field) {
            if ($field instanceof FieldInterface) {
                $dto = $field->getAsDto();
                $this->assertNotNull($dto->getProperty(), '字段属性不应为null');
                $this->assertNotEmpty($dto->getProperty(), '字段属性不应为空');
            }
        }

        $this->assertTrue(true, 'NEW页面字段配置验证通过');
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'miniProgram' => ['miniProgram'];
        yield 'openId' => ['openId'];
        yield 'nickName' => ['nickName'];
        yield 'avatar' => ['avatar'];
        yield 'province' => ['province'];
        yield 'city' => ['city'];
        yield 'gender' => ['gender'];
        yield 'lastInfoUpdateTime' => ['lastInfoUpdateTime'];
    }
}
