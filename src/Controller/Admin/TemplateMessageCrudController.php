<?php

namespace AlipayMiniProgramBundle\Controller\Admin;

use AlipayMiniProgramBundle\Entity\TemplateMessage;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<TemplateMessage>
 */
#[AdminCrud(routePath: '/alipay-mini-program/template-message', routeName: 'alipay_mini_program_template_message')]
final class TemplateMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TemplateMessage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('模板消息')
            ->setEntityLabelInPlural('模板消息')
            ->setPageTitle('index', '模板消息列表')
            ->setPageTitle('new', '新建模板消息')
            ->setPageTitle('edit', '编辑模板消息')
            ->setPageTitle('detail', '模板消息详情')
            ->setHelp('index', '管理支付宝小程序模板消息发送记录')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['templateId', 'toOpenId'])
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

        yield AssociationField::new('toUser', '接收用户')
            ->setRequired(true)
            ->formatValue(function (mixed $value): string {
                return $value instanceof User ? sprintf('%s (%s)', $value->getNickName() ?? '未知', $value->getOpenId()) : '';
            })
        ;

        yield TextField::new('templateId', '模板ID')
            ->setRequired(true)
            ->setHelp('支付宝小程序模板消息的模板ID')
        ;

        yield TextField::new('toOpenId', '接收者OpenID')
            ->setRequired(true)
            ->setHelp('消息接收者的OpenID')
        ;

        yield TextField::new('page', '页面路径')
            ->setHelp('点击模板消息后跳转的页面路径')
        ;

        yield CodeEditorField::new('data', '模板数据')
            ->setLanguage('javascript')
            ->setRequired(true)
            ->setHelp('模板消息的数据内容，JSON格式')
            ->onlyOnForms()
        ;

        yield TextareaField::new('dataPreview', '模板数据预览')
            ->onlyOnIndex()
        ;

        yield BooleanField::new('isSent', '是否已发送')
            ->formatValue(function (mixed $value): string {
                return is_bool($value) && $value ? '已发送' : '待发送';
            })
        ;

        yield DateTimeField::new('sentTime', '发送时间')
            ->hideOnForm()
            ->formatValue(function (mixed $value): ?string {
                return ($value instanceof \DateTimeInterface) ? $value->format('Y-m-d H:i:s') : null;
            })
        ;

        yield TextField::new('sendResult', '发送结果')
            ->hideOnForm()
            ->formatValue(function (mixed $value): string {
                if (!is_string($value)) {
                    return '';
                }
                $isSuccess = 'success' === $value;
                $class = $isSuccess ? 'text-success' : 'text-danger';
                $text = $isSuccess ? '成功' : $value;

                return sprintf('<span class="%s">%s</span>', $class, $text);
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
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('templateId', '模板ID'))
            ->add(TextFilter::new('toOpenId', '接收者OpenID'))
            ->add(EntityFilter::new('miniProgram', '关联小程序'))
            ->add(EntityFilter::new('toUser', '接收用户'))
            ->add(BooleanFilter::new('isSent', '是否已发送'))
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fieldCollection, FilterCollection $filterCollection): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fieldCollection, $filterCollection);

        // 添加关联查询以优化性能
        $qb->leftJoin('entity.miniProgram', 'miniProgram')
            ->leftJoin('entity.toUser', 'toUser')
            ->addSelect('miniProgram')
            ->addSelect('toUser')
        ;

        return $qb;
    }
}
