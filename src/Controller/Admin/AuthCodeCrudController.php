<?php

namespace AlipayMiniProgramBundle\Controller\Admin;

use AlipayMiniProgramBundle\Entity\AuthCode;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Enum\AlipayAuthScope;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * @extends AbstractCrudController<AuthCode>
 */
#[AdminCrud(routePath: '/alipay-mini-program/auth-code', routeName: 'alipay_mini_program_auth_code')]
final class AuthCodeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AuthCode::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('授权码')
            ->setEntityLabelInPlural('授权码')
            ->setPageTitle('index', '授权码列表')
            ->setPageTitle('detail', '授权码详情')
            ->setHelp('index', '支付宝小程序用户授权码记录')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['authCode', 'openId', 'userId'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield from $this->getBasicFields();
        yield from $this->getTokenFields();
        yield from $this->getTimestampFields();
        yield from $this->getMetadataFields();
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getBasicFields(): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
        ;

        yield AssociationField::new('alipayUser', '支付宝用户')
            ->formatValue(function (mixed $value): string {
                return $value instanceof User ? sprintf('%s (%s)', $value->getNickName() ?? '未知', $value->getOpenId()) : '';
            })
        ;

        yield TextField::new('authCode', '授权码')
            ->setMaxLength(20)
            ->formatValue(function (mixed $value): string {
                return is_string($value) ? substr($value, 0, 20) . '...' : '';
            })
        ;

        yield ChoiceField::new('scope', '授权范围')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => AlipayAuthScope::class])
            ->formatValue(function (mixed $value): string {
                return $value instanceof AlipayAuthScope ? match ($value) {
                    AlipayAuthScope::AUTH_BASE => '基础授权',
                    AlipayAuthScope::AUTH_USER => '用户信息授权',
                } : '';
            })
        ;

        yield TextField::new('state', '状态值')
            ->setHelp('防CSRF攻击的状态值')
        ;

        yield TextField::new('userId', '用户ID')
            ->setHelp('支付宝用户ID（已废弃）')
        ;

        yield TextField::new('openId', 'OpenID')
            ->setHelp('支付宝用户OpenID')
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getTokenFields(): iterable
    {
        yield TextField::new('accessToken', '访问令牌')
            ->setMaxLength(20)
            ->formatValue(function (mixed $value): string {
                return is_string($value) ? substr($value, 0, 20) . '...' : '';
            })
        ;

        yield TextField::new('refreshToken', '刷新令牌')
            ->setMaxLength(20)
            ->onlyOnDetail()
            ->formatValue(function (mixed $value): string {
                return is_string($value) ? substr($value, 0, 20) . '...' : '';
            })
        ;

        yield IntegerField::new('expiresIn', '访问令牌有效期')
            ->setHelp('单位：秒')
            ->formatValue(function (mixed $value): string {
                return is_int($value) ? sprintf('%d秒 (%s)', $value, $this->formatDuration($value)) : '';
            })
        ;

        yield IntegerField::new('reExpiresIn', '刷新令牌有效期')
            ->setHelp('单位：秒')
            ->onlyOnDetail()
            ->formatValue(function (mixed $value): string {
                return is_int($value) ? sprintf('%d秒 (%s)', $value, $this->formatDuration($value)) : '';
            })
        ;

        yield TextField::new('sign', '签名')
            ->onlyOnDetail()
            ->setMaxLength(20)
            ->formatValue(function (mixed $value): string {
                return is_string($value) ? substr($value, 0, 20) . '...' : '';
            })
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getTimestampFields(): iterable
    {
        yield DateTimeField::new('authStart', '授权开始时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getMetadataFields(): iterable
    {
        yield TextField::new('createdFromIp', '创建IP')
            ->hideOnForm()
        ;

        yield TextField::new('updatedFromIp', '更新IP')
            ->hideOnForm()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE) // 授权码只读，不允许修改或删除
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $choices = [];
        foreach (AlipayAuthScope::cases() as $case) {
            $label = match ($case) {
                AlipayAuthScope::AUTH_BASE => '基础授权',
                AlipayAuthScope::AUTH_USER => '用户信息授权',
            };
            $choices[$label] = $case->value;
        }

        return $filters
            ->add(TextFilter::new('openId', 'OpenID'))
            ->add(TextFilter::new('userId', '用户ID'))
            ->add(EntityFilter::new('alipayUser', '支付宝用户'))
            ->add(ChoiceFilter::new('scope', '授权范围')->setChoices($choices))
        ;
    }

    private function formatDuration(int $seconds): string
    {
        if ($seconds < 60) {
            return sprintf('%d秒', $seconds);
        }
        if ($seconds < 3600) {
            return sprintf('%d分钟', intval($seconds / 60));
        }
        if ($seconds < 86400) {
            return sprintf('%d小时', intval($seconds / 3600));
        }

        return sprintf('%d天', intval($seconds / 86400));
    }
}
