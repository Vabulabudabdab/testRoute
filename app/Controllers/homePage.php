<?php
namespace app\homepage;

use App\QueryBuilder;

$db = new QueryBuilder();

$db->update(
    ['title' => 'test '
], 2, 'posts');


