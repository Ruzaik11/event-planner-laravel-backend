<?php

namespace App\Contracts;

interface EventPlanGeneratorInterface
{
    public function generate(array $data): array;
}