<?php namespace Madlux\Filesystem\Repositories\Criteria\Files;

use Madlux\Filesystem\Repositories\Criteria\Files\MyCriteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Auth;
use Config;

class CreateFolder extends MyCriteria 
{
	public function __construct($foldername,$root)
    {
		$this->id_user = Auth::user()['id'];
        $this->foldername = $foldername;
		$this->root=$root;
		if(!isset($this->root))
			$this->root="";
    }

    /**
     * @param $model
     * @param Repository $repository
     *
     * @return mixed
     */
    public function apply( $model, Repository $repository )
    {
		$folder = $model
			->where('user_id','=',$this->id_user)
			->where('file_name','=',$this->foldername)
			->where('href','=',$this->root)
			->where('type','=','folder')
			->get()->toArray();
		
		if(!isset($folder[0]['id'])){
			$model->insert([
				'user_id' => $this->id_user,
				'file_name' => $this->foldername,
				'type' => 'folder',
				'href' => $this->root,
			]);
		}
		
		$this->setError($this->root);

        return $model;
    }

}
