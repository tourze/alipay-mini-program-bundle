<?php

namespace AlipayMiniProgramBundle\Procedure;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Param\SaveAlipayMiniProgramFormIdParam;
use AlipayMiniProgramBundle\Service\FormIdService;
use AlipayMiniProgramBundle\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodTag(name: '支付宝小程序')]
#[MethodDoc(summary: '上报支付宝小程序表单ID')]
#[MethodExpose(method: 'SaveAlipayMiniProgramFormId')]
#[Log]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class SaveAlipayMiniProgramFormId extends LockableProcedure
{
    public function __construct(
        private readonly FormIdService $formIdService,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserService $userService,
        private readonly Security $security,
    ) {
    }

    /**
     * @phpstan-param SaveAlipayMiniProgramFormIdParam $param
     */
    public function execute(SaveAlipayMiniProgramFormIdParam|RpcParamInterface $param): ArrayResult
    {
        $miniProgram = $this->entityManager->find(MiniProgram::class, $param->miniProgramId);
        if (null === $miniProgram) {
            throw new NotFoundHttpException('小程序不存在');
        }

        $bizUser = $this->security->getUser();
        if (null === $bizUser) {
            throw new NotFoundHttpException('用户未登录');
        }

        $alipayUser = $this->userService->getAlipayUser($bizUser);
        if (null === $alipayUser) {
            throw new NotFoundHttpException('未找到对应的支付宝用户');
        }

        $formId = $this->formIdService->saveFormId(
            $miniProgram,
            $alipayUser,
            $param->formId,
        );

        return new ArrayResult([
            'id' => $formId->getId(),
            'expiredTime' => $formId->getExpireTime()?->format('Y-m-d H:i:s'),
        ]);
    }
}
