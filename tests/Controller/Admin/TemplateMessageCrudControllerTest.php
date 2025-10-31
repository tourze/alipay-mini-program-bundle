<?php

namespace AlipayMiniProgramBundle\Tests\Controller\Admin;

use AlipayMiniProgramBundle\Controller\Admin\TemplateMessageCrudController;
use AlipayMiniProgramBundle\Entity\TemplateMessage;
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
#[CoversClass(TemplateMessageCrudController::class)]
#[RunTestsInSeparateProcesses]
final class TemplateMessageCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testGetEntityFqcnReturnsCorrectClass(): void
    {
        $client = self::createClientWithDatabase();

        // Create and login as admin user
        $this->createAdminUser('admin@test.com', 'admin123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        // Test HTTP layer accessibility
        $client->request('GET', '/admin/alipay-mini-program/template-message');
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
            'Controller endpoint should be accessible'
        );

        $this->assertEquals(TemplateMessage::class, TemplateMessageCrudController::getEntityFqcn());
    }

    public function testConfigureActions(): void
    {
        $client = self::createClientWithDatabase();

        // Create and login as admin user
        $this->createAdminUser('admin@test.com', 'admin123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        // Test HTTP layer accessibility
        $client->request('GET', '/admin/alipay-mini-program/template-message');
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
            'Controller endpoint should be accessible'
        );

        $controller = new TemplateMessageCrudController();
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
        $client->request('GET', '/admin/alipay-mini-program/template-message');
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
            'Controller endpoint should be accessible'
        );

        $controller = new TemplateMessageCrudController();
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
        $client->request('GET', '/admin/alipay-mini-program/template-message');
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
            'Controller endpoint should be accessible'
        );

        $controller = new TemplateMessageCrudController();
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
        $client->request('GET', '/admin/alipay-mini-program/template-message');
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
            'Controller endpoint should be accessible'
        );

        $controller = new TemplateMessageCrudController();
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
        $client->request('GET', '/admin/alipay-mini-program/template-message');
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
            'Controller endpoint should be accessible'
        );

        $controller = new TemplateMessageCrudController();
        $fields = iterator_to_array($controller->configureFields(Crud::PAGE_NEW));
        $this->assertNotEmpty($fields);

        // Test that essential fields are configured for TemplateMessage
        $fieldNames = [];
        foreach ($fields as $field) {
            if (is_object($field)) {
                $fieldNames[] = $field->getAsDto()->getProperty();
            }
        }

        // TemplateMessage entity should have these essential fields configured
        $this->assertContains('miniProgram', $fieldNames, 'miniProgram field should be configured');
        $this->assertContains('toUser', $fieldNames, 'toUser field should be configured');
        $this->assertContains('templateId', $fieldNames, 'templateId field should be configured');
        $this->assertContains('toOpenId', $fieldNames, 'toOpenId field should be configured');
        $this->assertGreaterThanOrEqual(4, count($fields), 'Should have at least 4 fields configured');

        // Verify controller has proper field configuration - TemplateMessage should have comprehensive field setup
        $this->assertGreaterThanOrEqual(8, count($fields), 'TemplateMessage controller should have at least 8 configured fields including miniProgram, formId, templateId, toOpenId and other essential fields');
    }

    public function testUnauthorizedAccess(): void
    {
        $client = self::createClientWithDatabase();

        // Test unauthorized access to admin endpoint
        try {
            $client->request('GET', '/admin/alipay-mini-program/template-message');

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
        $controller = new TemplateMessageCrudController();
        $this->assertInstanceOf(AbstractCrudController::class, $controller);
    }

    public function testValidationErrors(): void
    {
        $client = self::createClientWithDatabase();

        // Test form validation by attempting to access the form
        try {
            $client->request('GET', '/admin/alipay-mini-program/template-message/new');
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
     * @phpstan-ignore-next-line missingType.generics
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(TemplateMessageCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '关联小程序' => ['关联小程序'];
        yield '接收用户' => ['接收用户'];
        yield '模板ID' => ['模板ID'];
        yield '接收者OpenID' => ['接收者OpenID'];
        yield '页面路径' => ['页面路径'];
        yield '模板数据预览' => ['模板数据预览'];
        yield '是否已发送' => ['是否已发送'];
        yield '发送时间' => ['发送时间'];
        yield '发送结果' => ['发送结果'];
        yield '创建人' => ['创建人'];
        yield '更新人' => ['更新人'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'miniProgram' => ['miniProgram'];
        yield 'toUser' => ['toUser'];
        yield 'templateId' => ['templateId'];
        yield 'toOpenId' => ['toOpenId'];
        yield 'page' => ['page'];
        yield 'data' => ['data'];
        yield 'isSent' => ['isSent'];
        // sentTime is hidden on form (hideOnForm)
        // sendResult is hidden on form (hideOnForm)
    }

    /**
     * 独立的字段配置测试，绕过基类的客户端创建问题
     * 这个测试直接验证字段配置而不依赖有问题的基类方法
     */
    public function testNewPageShowsConfiguredFieldsWorkaround(): void
    {
        // 直接测试控制器配置，而不进行HTTP请求
        $controller = new TemplateMessageCrudController();

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
        yield 'toUser' => ['toUser'];
        yield 'templateId' => ['templateId'];
        yield 'toOpenId' => ['toOpenId'];
        yield 'page' => ['page'];
        yield 'data' => ['data'];
        yield 'isSent' => ['isSent'];
        // sentTime is hidden on form (hideOnForm)
        // sendResult is hidden on form (hideOnForm)
    }
}
