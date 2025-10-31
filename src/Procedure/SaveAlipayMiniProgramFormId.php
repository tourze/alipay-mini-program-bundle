<?php

namespace AlipayMiniProgramBundle\Procedure;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Service\FormIdService;
use AlipayMiniProgramBundle\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodTag(name: '支付宝小程序')]
#[MethodDoc(summary: '上报支付宝小程序表单ID')]
#[MethodExpose(method: 'SaveAlipayMiniProgramFormId')]
#[Log]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class SaveAlipayMiniProgramFormId extends LockableProcedure
{
    #[MethodParam(description: '小程序ID')]
    public string $miniProgramId;

    #[MethodParam(description: '表单ID')]
    public string $formId;

    public function __construct(
        private readonly FormIdService $formIdService,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserService $userService,
        private readonly Security $security,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $miniProgram = $this->entityManager->find(MiniProgram::class, $this->miniProgramId);
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
            $this->formId,
        );

        return [
            'id' => $formId->getId(),
            'expiredTime' => $formId->getExpireTime()?->format('Y-m-d H:i:s'),
        ];
    }
}
