<?php

namespace Freezemage\DBAL\Query;

final class Navigation
{
    public function __construct(public ?int $limit = null, public ?int $offset = null)
    {
    }
}