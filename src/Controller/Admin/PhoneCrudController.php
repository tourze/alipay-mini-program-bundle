<?php

namespace AlipayMiniProgramBundle\Controller\Admin;

use AlipayMiniProgramBundle\Entity\Phone;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

#[AdminCrud(routePath: '/alipay-mini-program/phone', routeName: 'alipay_mini_program_phone')]
class PhoneCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Phone::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('手机号码')
            ->setEntityLabelInPlural('手机号码')
            ->setPageTitle('index', '手机号码列表')
            ->setPageTitle('new', '新建手机号码')
            ->setPageTitle('edit', '编辑手机号码')
            ->setPageTitle('detail', '手机号码详情')
            ->setHelp('index', '管理支付宝用户绑定的手机号码')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['number']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('id', 'ID')
            ->setMaxLength(9999)
            ->onlyOnIndex();

        yield TextField::new('number', '手机号码')
            ->setRequired(true)
            ->setHelp('用户绑定的手机号码')
            ->formatValue(function ($value) {
                return $value ? $this->formatPhoneNumber($value) : '';
            });

        yield IntegerField::new('userPhonesCount', '绑定用户数')
            ->onlyOnIndex()
            ->formatValue(function ($value, $entity) {
                return $entity->getUserPhones()->count();
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
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE])
            ->remove(Crud::PAGE_INDEX, Action::NEW); // 手机号码只能通过用户绑定创建，不允许手动创建
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('number', '手机号码'));
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
        $qb->leftJoin('entity.userPhones', 'userPhones')
           ->addSelect('userPhones');

        return $qb;
    }
} 