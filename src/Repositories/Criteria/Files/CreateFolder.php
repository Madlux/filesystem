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
			$this->root='';
		$this->dir_root = Config::get('madlux_files_settings.file_root').Auth::user()['username'];
		$this->dir=$this->dir_root.'/'.$this->root.'/'.$this->foldername;
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
			->where('filename','=',$this->foldername)
			->where('href','=',$this->root)
			->where('type','=','folder')
			->get()->toArray();
		
		if(!isset($folder[0]['id'])){
			$a=mkdir($this->dir);
			if($a){
				$model->insert([
					'user_id' => $this->id_user,
					'filename' => $this->foldername,
					'type' => 'folder',
					'href' => $this->root,
				]);
			}
		}
		
		$this->setError('');

        return $model;
    }

}
