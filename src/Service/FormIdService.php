<?php

namespace AlipayMiniProgramBundle\Service;

use AlipayMiniProgramBundle\Entity\FormId;
use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Repository\FormIdRepository;
use Doctrine\ORM\EntityManagerInterface;

class FormIdService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FormIdRepository $formIdRepository,
    ) {
    }

    /**
     * 保存formId
     */
    public function saveFormId(MiniProgram $miniProgram, User $user, string $formId): FormId
    {
        $formIdEntity = new FormId();
        $formIdEntity->setMiniProgram($miniProgram);
        $formIdEntity->setUser($user);
        $formIdEntity->setFormId($formId);
        // formId有效期为7天
        $formIdEntity->setExpireTime(new \DateTime('+7 days'));
        $formIdEntity->setUsedCount(0);

        $this->entityManager->persist($formIdEntity);
        $this->entityManager->flush();

        return $formIdEntity;
    }

    /**
     * 获取一个可用的formId
     *
     * 规则:
     * 1. 未过期
     * 2. 使用次数小于3次
     * 3. 未被标记为已使用
     */
    public function getAvailableFormId(MiniProgram $miniProgram, User $user): ?FormId
    {
        $formId = $this->formIdRepository->findAvailableFormId($miniProgram, $user);
        if ($formId) {
            $formId->incrementUsedCount();
            $this->entityManager->flush();
        }

        return $formId;
    }

    /**
     * 清理过期的formId
     */
    public function cleanExpiredFormIds(): int
    {
        return $this->formIdRepository->cleanExpiredFormIds();
    }
}
