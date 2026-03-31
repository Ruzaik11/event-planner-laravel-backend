<?php

namespace App\Contracts;

interface ImageSearchInterface
{
    public function search(string $query): ? array;
}