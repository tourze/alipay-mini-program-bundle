<?php

namespace AlipayMiniProgramBundle\Tests\Integration\Repository;

use AlipayMiniProgramBundle\Repository\FormIdRepository;
use PHPUnit\Framework\TestCase;

class FormIdRepositoryTest extends TestCase
{
    public function testRepositoryExists(): void
    {
        $this->assertTrue(class_exists(FormIdRepository::class));
    }
}