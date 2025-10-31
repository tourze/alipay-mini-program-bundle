<?php

namespace AlipayMiniProgramBundle\Controller\Admin;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<MiniProgram>
 */
#[AdminCrud(routePath: '/alipay-mini-program/mini-program', routeName: 'alipay_mini_program_mini_program')]
final class MiniProgramCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MiniProgram::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('支付宝小程序')
            ->setEntityLabelInPlural('支付宝小程序')
            ->setPageTitle('index', '支付宝小程序列表')
            ->setPageTitle('new', '新建支付宝小程序')
            ->setPageTitle('edit', '编辑支付宝小程序')
            ->setPageTitle('detail', '支付宝小程序详情')
            ->setHelp('index', '管理支付宝小程序配置信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['name', 'appId', 'code'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('code', '编码')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield TextField::new('name', '小程序名称')
            ->setRequired(true)
        ;

        yield TextField::new('appId', 'AppID')
            ->setRequired(true)
            ->setHelp('支付宝小程序的应用ID')
        ;

        yield CodeEditorField::new('privateKey', '应用私钥')
            ->setLanguage('shell')
            ->onlyOnForms()
            ->setRequired(true)
            ->setHelp('RSA私钥，用于签名')
        ;

        yield CodeEditorField::new('alipayPublicKey', '支付宝公钥')
            ->setLanguage('shell')
            ->onlyOnForms()
            ->setRequired(true)
            ->setHelp('支付宝的RSA公钥，用于验签')
        ;

        yield TextField::new('encryptKey', 'AES密钥')
            ->onlyOnForms()
            ->setHelp('用于数据加密解密的AES密钥')
        ;

        yield BooleanField::new('sandbox', '沙箱环境')
            ->setHelp('是否为支付宝沙箱测试环境')
        ;

        yield ChoiceField::new('signType', '签名类型')
            ->setChoices([
                'RSA2' => 'RSA2',
                'RSA' => 'RSA',
            ])
            ->setHelp('推荐使用RSA2')
        ;

        yield TextField::new('gatewayUrl', '网关地址')
            ->onlyOnForms()
            ->setHelp('支付宝开放平台网关地址')
        ;

        yield TextField::new('authRedirectUrl', '授权回调地址')
            ->onlyOnForms()
            ->setHelp('用户授权后的回调地址')
        ;

        yield TextareaField::new('remark', '备注说明')
            ->onlyOnForms()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->formatValue(function (mixed $value): ?string {
                return ($value instanceof \DateTimeInterface) ? $value->format('Y-m-d H:i:s') : null;
            })
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->formatValue(function (mixed $value): ?string {
                return ($value instanceof \DateTimeInterface) ? $value->format('Y-m-d H:i:s') : null;
            })
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '小程序名称'))
            ->add(TextFilter::new('appId', 'AppID'))
            ->add(BooleanFilter::new('sandbox', '沙箱环境'))
        ;
    }
}
