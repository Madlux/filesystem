<?php namespace Madlux\Filesystem\Repositories\Criteria\Files;

use Madlux\Filesystem\Repositories\Criteria\Files\MyCriteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

use Config;
use Auth;

class DeleteFileById extends MyCriteria {

	public function __construct($id_file,$root)
    {
        $this->id_file = $id_file;
		$this->id_user = Auth::user()['id'];
		$this->root = $root;
		
		if($this->root=='')
			$this->root='/'.$this->root.'/';
		
		$this->dir_root = Config::get('madlux_files_settings.file_root').Auth::user()['username'];
    }

    /**
     * @param $model
     * @param Repository $repository
     *
     * @return mixed
     */
    public function apply( $model, Repository $repository )
    {
		$model=$model->where('id','=',$this->id_file)
			->where('user_id','=',$this->id_user);
			
		$filename = $model->get()->toArray();
		
		$fileroot=$this->dir_root.$this->root.$filename[0]['file_name'];
		
		unlink($fileroot);
		$model->delete();
		
		$this->setError('false');

        return $model;
    }

}
