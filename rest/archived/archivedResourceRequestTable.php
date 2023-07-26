<?php
namespace rest\archived;

use itdq\DbTable;
use rest\traits\resourceRequestTableTrait;
use rest\traits\tableTrait;

class archivedResourceRequestTable extends DbTable
{
    use tableTrait, resourceRequestTableTrait;
}