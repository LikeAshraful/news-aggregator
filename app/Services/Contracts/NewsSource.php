<?php

namespace App\Services\Contracts;

interface NewsSource
{
    public function normalize(array $rawData) : array;
}
