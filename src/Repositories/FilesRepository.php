<?php namespace Madlux\Filesystem\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;

class FilesRepository extends Repository 
{
    public function model()
    {
        return 'Madlux\Filesystem\Models\Files';
    }
}
