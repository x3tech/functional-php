<?php
namespace x3\Functional;

use Illuminate\Support\Collection;

class Laravel
{
    public static function collectionSum(Collection $collection)
    {
        return array_sum($collection->toArray());
    }
}
