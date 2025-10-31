<?php

namespace AlipayMiniProgramBundle\Controller\Admin;

use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use AlipayMiniProgramBundle\Entity\Phone;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

/**
 * @extends AbstractCrudController<AlipayUserPhone>
 */
#[AdminCrud(routePath: '/alipay-mini-program/user-phone', routeName: 'alipay_mini_program_user_phone')]
final class AlipayUserPhoneCrudController extends AbstractCrudController
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
            ->setSearchFields([])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
        ;

        yield AssociationField::new('user', '支付宝用户')
            ->setRequired(true)
            ->formatValue(function (mixed $value): string {
                return $value instanceof User
                    ? sprintf('%s (%s)', $value->getNickName() ?? '未知', $value->getOpenId())
                    : '';
            })
        ;

        yield AssociationField::new('phone', '手机号码')
            ->setRequired(true)
            ->formatValue(function (mixed $value): string {
                if (!($value instanceof Phone)) {
                    return '';
                }
                $phoneNumber = $value->getNumber();

                return null !== $phoneNumber ? $this->formatPhoneNumber($phoneNumber) : '';
            })
        ;

        yield DateTimeField::new('verifiedTime', '验证时间')
            ->hideOnForm()
            ->setHelp('手机号码绑定验证的时间')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT) // 绑定关系不允许手动创建或编辑
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('user', '支付宝用户'))
            ->add(EntityFilter::new('phone', '手机号码'))
        ;
    }

    /**
     * 格式化手机号码显示
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        if (11 === strlen($phoneNumber) && 1 === preg_match('/^1[3-9]\d{9}$/', $phoneNumber)) {
            // 中国手机号码格式化：138****1234
            return substr($phoneNumber, 0, 3) . '****' . substr($phoneNumber, -4);
        }

        return $phoneNumber; // 其他格式直接显示
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fieldCollection, FilterCollection $filterCollection): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fieldCollection, $filterCollection);

        // 添加关联查询以优化性能
        $qb->leftJoin('entity.user', 'user')
            ->leftJoin('entity.phone', 'phone')
            ->addSelect('user')
            ->addSelect('phone')
        ;

        return $qb;
    }
}
