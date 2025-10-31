<?php

namespace AlipayMiniProgramBundle\Controller\Admin;

use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * @extends AbstractCrudController<User>
 */
#[AdminCrud(routePath: '/alipay-mini-program/user', routeName: 'alipay_mini_program_user')]
final class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('支付宝用户')
            ->setEntityLabelInPlural('支付宝用户')
            ->setPageTitle('index', '支付宝用户列表')
            ->setPageTitle('new', '新建支付宝用户')
            ->setPageTitle('edit', '编辑支付宝用户')
            ->setPageTitle('detail', '支付宝用户详情')
            ->setHelp('index', '管理支付宝小程序用户信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['openId', 'nickName'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
        ;

        yield AssociationField::new('miniProgram', '关联小程序')
            ->setRequired(true)
        ;

        yield TextField::new('openId', '用户OpenID')
            ->setRequired(true)
            ->setHelp('支付宝用户的唯一标识')
        ;

        yield TextField::new('nickName', '用户昵称')
            ->setHelp('用户在支付宝的昵称')
        ;

        yield ImageField::new('avatar', '用户头像')
            ->setBasePath('/')
            ->onlyOnIndex()
            ->formatValue(function (mixed $value): string {
                return is_string($value) ? sprintf('<img src="%s" style="max-width:50px;max-height:50px;" />', $value) : '';
            })
        ;

        yield TextField::new('avatar', '头像地址')
            ->onlyOnForms()
            ->setHelp('用户头像图片地址')
        ;

        yield TextField::new('province', '省份')
            ->setHelp('用户所在省份')
        ;

        yield TextField::new('city', '城市')
            ->setHelp('用户所在城市')
        ;

        yield ChoiceField::new('gender', '性别')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => AlipayUserGender::class])
            ->formatValue(function (mixed $value): string {
                return $value instanceof AlipayUserGender ? match ($value) {
                    AlipayUserGender::MALE => '男',
                    AlipayUserGender::FEMALE => '女',
                } : '';
            })
        ;

        yield DateTimeField::new('lastInfoUpdateTime', '最后更新信息时间')
            ->hideOnForm()
            ->formatValue(function (mixed $value): ?string {
                return ($value instanceof \DateTimeInterface) ? $value->format('Y-m-d H:i:s') : null;
            })
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
            ->disable(Action::NEW) // 用户只能通过授权创建，不允许手动创建
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $choices = [];
        foreach (AlipayUserGender::cases() as $case) {
            $label = match ($case) {
                AlipayUserGender::MALE => '男',
                AlipayUserGender::FEMALE => '女',
            };
            $choices[$label] = $case->value;
        }

        return $filters
            ->add(TextFilter::new('openId', '用户OpenID'))
            ->add(TextFilter::new('nickName', '用户昵称'))
            ->add(EntityFilter::new('miniProgram', '关联小程序'))
            ->add(ChoiceFilter::new('gender', '性别')->setChoices($choices))
        ;
    }
}
