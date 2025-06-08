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

#[MethodTag('支付宝小程序')]
#[MethodDoc('上报支付宝小程序表单ID')]
#[MethodExpose('SaveAlipayMiniProgramFormId')]
#[Log]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class SaveAlipayMiniProgramFormId extends LockableProcedure
{
    #[MethodParam('小程序ID')]
    public string $miniProgramId;

    #[MethodParam('表单ID')]
    public string $formId;

    public function __construct(
        private readonly FormIdService $formIdService,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserService $userService,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $miniProgram = $this->entityManager->find(MiniProgram::class, $this->miniProgramId);
        if (!$miniProgram) {
            throw new NotFoundHttpException('小程序不存在');
        }

        $formId = $this->formIdService->saveFormId(
            $miniProgram,
            $this->userService->getAlipayUser($this->security->getUser()),
            $this->formId,
        );

        return [
            'id' => $formId->getId(),
            'expiredTime' => $formId->getExpireTime()?->format('Y-m-d H:i:s'),
        ];
    }
}
