<?php namespace Madlux\Filesystem\Repositories\Criteria\Files;

use Madlux\Filesystem\Repositories\Criteria\Files\MyCriteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

use Auth;

class DeleteFileById extends MyCriteria {

	public function __construct($id_file)
    {
        $this->id_file = $id_file;
		$this->id_user = Auth::user()['id'];
    }

    /**
     * @param $model
     * @param Repository $repository
     *
     * @return mixed
     */
    public function apply( $model, Repository $repository )
    {
		$model = $model->where('id','=',$this->id_file)->where('user_id','=',$this->id_user);
		$fileroot=$model->get()->toArray();
		unlink($fileroot[0]['fileroot']);
		$model->delete();
		
		$this->setError('false');

        return $model;
    }

}
