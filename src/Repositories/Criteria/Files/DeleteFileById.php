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
		
		$this->files_root=$this->root;
		
		if($this->root=='')
			$this->root='/'.$this->root;
		
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
		$first_model=$model;
		$second_model=$model;
		
		$first_model=$first_model
			->where('id','=',$this->id_file)
			->where('user_id','=',$this->id_user);
			
		$file = $first_model->get()->toArray();
		
		$href=$file[0]['href'].'/'.$file[0]['filename'];
		
		$fileroot=$this->dir_root.$this->root.$href;
		
		if($file[0]['type']!=='folder'){	
			$first_model->delete();
			
			unlink($fileroot);
		}else{
			$second_model=$second_model
				->where('href','like',$this->files_root.$href.'%')
				->where('user_id','=',$this->id_user);
				
			$first_model->delete();
			$second_model->delete();
			
			$this->removeDirectory($fileroot);
		}
		
		$this->setError('false');

        return $model;
    }
	
	private function removeDirectory($dir) {
		if ($objs = glob($dir."/*")) {
			foreach($objs as $obj) {
				is_dir($obj) ? $this->removeDirectory($obj) : unlink($obj);
			}
		}
		rmdir($dir);
	}

}
