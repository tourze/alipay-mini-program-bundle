<?php

namespace AlipayMiniProgramBundle\Controller\Admin;

use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

#[AdminCrud(routePath: '/alipay-mini-program/user-phone', routeName: 'alipay_mini_program_user_phone')]
class AlipayUserPhoneCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AlipayUserPhone::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('用户手机号绑定')
            ->setEntityLabelInPlural('用户手机号绑定')
            ->setPageTitle('index', '用户手机号绑定列表')
            ->setPageTitle('new', '新建用户手机号绑定')
            ->setPageTitle('edit', '编辑用户手机号绑定')
            ->setPageTitle('detail', '用户手机号绑定详情')
            ->setHelp('index', '管理支付宝用户与手机号码的绑定关系')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields([]);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('id', 'ID')
            ->setMaxLength(9999)
            ->onlyOnIndex();

        yield AssociationField::new('user', '支付宝用户')
            ->setRequired(true)
            ->formatValue(function ($value) {
                return $value ? sprintf('%s (%s)', $value->getNickName() ?: '未知', $value->getOpenId()) : '';
            });

        yield AssociationField::new('phone', '手机号码')
            ->setRequired(true)
            ->formatValue(function ($value) {
                return $value ? $this->formatPhoneNumber($value->getNumber()) : '';
            });

        yield TextField::new('verifiedTime', '验证时间')
            ->hideOnForm()
            ->setHelp('手机号码绑定验证的时间')
            ->formatValue(function ($value) {
                return $value?->format('Y-m-d H:i:s');
            });

        yield TextField::new('createTime', '创建时间')
            ->hideOnForm()
            ->formatValue(function ($value) {
                return $value?->format('Y-m-d H:i:s');
            });

        yield TextField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->formatValue(function ($value) {
                return $value?->format('Y-m-d H:i:s');
            });
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::DELETE])
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT); // 绑定关系不允许手动创建或编辑
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('user', '支付宝用户'))
            ->add(EntityFilter::new('phone', '手机号码'));
    }

    /**
     * 格式化手机号码显示
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        if (strlen($phoneNumber) == 11 && preg_match('/^1[3-9]\d{9}$/', $phoneNumber)) {
            // 中国手机号码格式化：138****1234
            return substr($phoneNumber, 0, 3) . '****' . substr($phoneNumber, -4);
        }
        
        return $phoneNumber; // 其他格式直接显示
    }

    public function createIndexQueryBuilder($searchDto, $entityDto, $fieldCollection, $filterCollection): \Doctrine\ORM\QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fieldCollection, $filterCollection);
        
        // 添加关联查询以优化性能
        $qb->leftJoin('entity.user', 'user')
           ->leftJoin('entity.phone', 'phone')
           ->addSelect('user')
           ->addSelect('phone');

        return $qb;
    }
} 