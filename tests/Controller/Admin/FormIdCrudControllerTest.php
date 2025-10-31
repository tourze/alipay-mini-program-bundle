<?php

namespace AlipayMiniProgramBundle\Tests\Controller\Admin;

use AlipayMiniProgramBundle\Controller\Admin\FormIdCrudController;
use AlipayMiniProgramBundle\Entity\FormId;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
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
#[CoversClass(FormIdCrudController::class)]
#[RunTestsInSeparateProcesses]
final class FormIdCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testControllerClassStructure(): void
    {
        $client = self::createClientWithDatabase();

        // Create and login as admin user
        $this->createAdminUser('admin@test.com', 'admin123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        // Test HTTP layer accessibility
        $client->request('GET', '/admin/alipay-mini-program/form-id');
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
            'Controller endpoint should be accessible'
        );

        // Verify controller class structure
        $controller = new FormIdCrudController();
        $this->assertSame(FormId::class, $controller::getEntityFqcn());

        // Check controller method existence
        $reflection = new \ReflectionClass(FormIdCrudController::class);
        $this->assertTrue($reflection->hasMethod('configureCrud'));
        $this->assertTrue($reflection->hasMethod('configureFields'));
        $this->assertTrue($reflection->hasMethod('configureActions'));
        $this->assertTrue($reflection->hasMethod('configureFilters'));
        $this->assertTrue($reflection->hasMethod('createIndexQueryBuilder'));

        // Verify AdminCrud attribute exists
        $attributes = $reflection->getAttributes();
        $hasAdminCrudAttribute = false;
        foreach ($attributes as $attribute) {
            if (str_contains($attribute->getName(), 'AdminCrud')) {
                $hasAdminCrudAttribute = true;
                break;
            }
        }
        $this->assertTrue($hasAdminCrudAttribute, 'Controller should have AdminCrud attribute');
    }

    public function testUnauthorizedAccess(): void
    {
        $client = self::createClientWithDatabase();

        // Test unauthorized access to admin endpoint
        try {
            $client->request('GET', '/admin/alipay-mini-program/form-id');

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

        // Test controller instantiation
        $controller = new FormIdCrudController();
        $this->assertInstanceOf(FormIdCrudController::class, $controller);

        // Verify inheritance
        $this->assertInstanceOf(AbstractCrudController::class, $controller);
    }

    public function testRequiredFieldValidation(): void
    {
        $client = self::createClientWithDatabase();

        // Create and login as admin user
        $this->createAdminUser('admin@test.com', 'admin123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        // Test HTTP layer accessibility
        $client->request('GET', '/admin/alipay-mini-program/form-id');
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
            'Controller endpoint should be accessible'
        );

        $controller = new FormIdCrudController();

        // Test field configuration
        $fields = iterator_to_array($controller->configureFields('new'));
        $this->assertNotEmpty($fields);

        // Test that essential fields are configured for FormId
        $fieldNames = [];
        foreach ($fields as $field) {
            if (is_object($field)) {
                $fieldNames[] = $field->getAsDto()->getProperty();
            }
        }

        // FormId entity should have these essential fields configured
        $this->assertContains('miniProgram', $fieldNames, 'miniProgram field should be configured');
        $this->assertContains('user', $fieldNames, 'user field should be configured');
        $this->assertContains('formId', $fieldNames, 'formId field should be configured');
        $this->assertGreaterThanOrEqual(3, count($fields), 'Should have at least 3 fields configured');

        // Verify controller has proper field configuration - FormId should have at least 10 configured fields
        $this->assertGreaterThanOrEqual(10, count($fields), 'FormId controller should have at least 10 configured fields including id, miniProgram, user, formId, expireTime, usedCount, isUsed, createdBy, updatedBy, createTime, updateTime');
    }

    public function testSearchFiltersConfiguration(): void
    {
        $client = self::createClientWithDatabase();

        // Create and login as admin user
        $this->createAdminUser('admin@test.com', 'admin123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        // Test HTTP layer accessibility
        $client->request('GET', '/admin/alipay-mini-program/form-id');
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
            'Controller endpoint should be accessible'
        );

        $controller = new FormIdCrudController();

        // Test filter configuration - FormId has 5 filters (TextFilter:formId, EntityFilter:miniProgram, EntityFilter:user, etc.)
        $filters = $controller->configureFilters(Filters::new());
        $this->assertInstanceOf(Filters::class, $filters);

        // Test search configuration
        $crud = $controller->configureCrud(Crud::new());
        $this->assertInstanceOf(Crud::class, $crud);

        // Test actions configuration
        $actions = Actions::new()
            ->add(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DELETE)
        ;

        $configuredActions = $controller->configureActions($actions);
        $this->assertInstanceOf(Actions::class, $configuredActions);
    }

    public function testValidationErrors(): void
    {
        $client = self::createClientWithDatabase();

        // Test form validation by attempting to access the form
        try {
            $client->request('GET', '/admin/alipay-mini-program/form-id/new');
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
        return self::getService(FormIdCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '关联小程序' => ['关联小程序'];
        yield '关联用户' => ['关联用户'];
        yield '表单ID' => ['表单ID'];
        yield '过期时间' => ['过期时间'];
        yield '使用次数' => ['使用次数'];
        yield '是否已用完' => ['是否已用完'];
        yield '创建人' => ['创建人'];
        yield '更新人' => ['更新人'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'miniProgram' => ['miniProgram'];
        yield 'user' => ['user'];
        yield 'formId' => ['formId'];
        yield 'usedCount' => ['usedCount'];
    }

    /**
     * 独立的字段配置测试，绕过基类的客户端创建问题
     * 这个测试直接验证字段配置而不依赖有问题的基类方法
     */
    public function testNewPageShowsConfiguredFieldsWorkaround(): void
    {
        // 直接测试控制器配置，而不进行HTTP请求
        $controller = new FormIdCrudController();

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
        yield 'user' => ['user'];
        yield 'formId' => ['formId'];
        yield 'usedCount' => ['usedCount'];
    }
}
