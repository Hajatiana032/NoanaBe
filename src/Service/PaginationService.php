<?php

namespace App\Service;

use Doctrine\DBAL\Query\Limit;
use Knp\Component\Pager\PaginatorInterface;

class PaginationService
{
    public function __construct(private PaginatorInterface $paginator) {}

    public function paginate($query, $request, $limit = 9)
    {
        return $this->paginator->paginate($query, $request->query->getInt('page', 1), $limit);
    }
}
