<?php

namespace AlipayMiniProgramBundle\Controller\Admin;

use AlipayMiniProgramBundle\Entity\FormId;
use AlipayMiniProgramBundle\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<FormId>
 */
#[AdminCrud(routePath: '/alipay-mini-program/form-id', routeName: 'alipay_mini_program_form_id')]
final class FormIdCrudController extends AbstractCrudController
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
            ->setSearchFields(['formId'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id', 'ID')
            ->onlyOnIndex()
        ;

        yield AssociationField::new('miniProgram', '关联小程序')
            ->setRequired(true)
        ;

        yield AssociationField::new('user', '关联用户')
            ->setRequired(true)
            ->formatValue(function (mixed $value): string {
                return $value instanceof User ? sprintf('%s (%s)', $value->getNickName() ?? '未知', $value->getOpenId()) : '';
            })
        ;

        yield TextField::new('formId', '表单ID')
            ->setRequired(true)
            ->setHelp('小程序表单提交时生成的formId')
        ;

        yield DateTimeField::new('expireTime', '过期时间')
            ->hideOnForm()
            ->formatValue(function (mixed $value): string {
                if (!($value instanceof \DateTimeInterface)) {
                    return '';
                }
                $now = new \DateTime();
                $isExpired = $value < $now;
                $status = $isExpired ? '已过期' : '有效';
                $class = $isExpired ? 'text-danger' : 'text-success';

                return sprintf('<span class="%s">%s (%s)</span>', $class, $value->format('Y-m-d H:i:s'), $status);
            })
        ;

        yield IntegerField::new('usedCount', '使用次数')
            ->setHelp('已使用次数，最多可使用3次')
            ->formatValue(function (mixed $value): string {
                $maxCount = 3;
                $usedCount = is_int($value) ? $value : 0;
                $isUsed = $usedCount >= $maxCount;
                $class = $isUsed ? 'text-danger' : 'text-success';

                return sprintf('<span class="%s">%d/%d %s</span>', $class, $usedCount, $maxCount, $isUsed ? '(已用完)' : '');
            })
        ;

        yield BooleanField::new('isUsed', '是否已用完')
            ->onlyOnIndex()
            ->formatValue(function (mixed $value, object $entity): bool {
                return $entity instanceof FormId ? $entity->isUsed() : false;
            })
        ;

        yield TextField::new('createdBy', '创建人')
            ->hideOnForm()
        ;

        yield TextField::new('updatedBy', '更新人')
            ->hideOnForm()
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
            ->disable(Action::NEW) // 表单ID只能通过小程序提交生成，不允许手动创建
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('formId', '表单ID'))
            ->add(EntityFilter::new('miniProgram', '关联小程序'))
            ->add(EntityFilter::new('user', '关联用户'))
            ->add(NumericFilter::new('usedCount', '使用次数'))
            ->add(BooleanFilter::new('isUsed', '是否已用完'))
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fieldCollection, FilterCollection $filterCollection): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fieldCollection, $filterCollection);

        // 添加关联查询以优化性能
        $qb->leftJoin('entity.miniProgram', 'miniProgram')
            ->leftJoin('entity.user', 'user')
            ->addSelect('miniProgram')
            ->addSelect('user')
        ;

        return $qb;
    }
}
