<?php namespace Madlux\Filesystem\Repositories\Criteria\Files;

use Madlux\Filesystem\Repositories\Criteria\Files\MyCriteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Auth;

class DeleteAllUserFiles extends Criteria {

	public function __construct()
    {
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
		$model = $model->where('user_id','=',$this->id_user);
		$files=$model->get()->toArray();
		
		foreach($files as $file){
			unlink($file['fileroot']);
		}
		
		$model->delete();

        return $model;
    }

}
