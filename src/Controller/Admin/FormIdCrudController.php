<?php

namespace AlipayMiniProgramBundle\Controller\Admin;

use AlipayMiniProgramBundle\Entity\FormId;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

#[AdminCrud(routePath: '/alipay-mini-program/form-id', routeName: 'alipay_mini_program_form_id')]
class FormIdCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FormId::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('表单ID')
            ->setEntityLabelInPlural('表单ID')
            ->setPageTitle('index', '表单ID列表')
            ->setPageTitle('new', '新建表单ID')
            ->setPageTitle('edit', '编辑表单ID')
            ->setPageTitle('detail', '表单ID详情')
            ->setHelp('index', '管理支付宝小程序表单ID，用于发送模板消息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['formId']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('id', 'ID')
            ->setMaxLength(9999)
            ->onlyOnIndex();

        yield AssociationField::new('miniProgram', '关联小程序')
            ->setRequired(true);

        yield AssociationField::new('user', '关联用户')
            ->setRequired(true)
            ->formatValue(function ($value) {
                return $value ? sprintf('%s (%s)', $value->getNickName() ?: '未知', $value->getOpenId()) : '';
            });

        yield TextField::new('formId', '表单ID')
            ->setRequired(true)
            ->setHelp('小程序表单提交时生成的formId');

        yield TextField::new('expireTime', '过期时间')
            ->hideOnForm()
            ->formatValue(function ($value) {
                if (!$value) return '';
                $now = new \DateTime();
                $isExpired = $value < $now;
                $status = $isExpired ? '已过期' : '有效';
                $class = $isExpired ? 'text-danger' : 'text-success';
                return sprintf('<span class="%s">%s (%s)</span>', $class, $value->format('Y-m-d H:i:s'), $status);
            });

        yield IntegerField::new('usedCount', '使用次数')
            ->setHelp('已使用次数，最多可使用3次')
            ->formatValue(function ($value) {
                $maxCount = 3;
                $isUsed = $value >= $maxCount;
                $class = $isUsed ? 'text-danger' : 'text-success';
                return sprintf('<span class="%s">%d/%d %s</span>', $class, $value, $maxCount, $isUsed ? '(已用完)' : '');
            });

        yield BooleanField::new('isUsed', '是否已用完')
            ->onlyOnIndex()
            ->formatValue(function ($value, $entity) {
                return $entity->isUsed();
            });

        yield TextField::new('createdBy', '创建人')
            ->hideOnForm();

        yield TextField::new('updatedBy', '更新人')
            ->hideOnForm();

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
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE])
            ->remove(Crud::PAGE_INDEX, Action::NEW); // 表单ID只能通过小程序提交生成，不允许手动创建
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('formId', '表单ID'))
            ->add(EntityFilter::new('miniProgram', '关联小程序'))
            ->add(EntityFilter::new('user', '关联用户'))
            ->add(NumericFilter::new('usedCount', '使用次数'))
            ->add(BooleanFilter::new('isUsed', '是否已用完'));
    }

    public function createIndexQueryBuilder($searchDto, $entityDto, $fieldCollection, $filterCollection): \Doctrine\ORM\QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fieldCollection, $filterCollection);
        
        // 添加关联查询以优化性能
        $qb->leftJoin('entity.miniProgram', 'miniProgram')
           ->leftJoin('entity.user', 'user')
           ->addSelect('miniProgram')
           ->addSelect('user');

        return $qb;
    }
} 