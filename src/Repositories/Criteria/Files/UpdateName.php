<?php namespace Madlux\Filesystem\Repositories\Criteria\Files;

use Madlux\Filesystem\Repositories\Criteria\Files\MyCriteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Auth;
use Config;
use DB;

class UpdateName extends MyCriteria 
{
	public function __construct($id_file,$new_name)
    {
        $this->id_file = $id_file;
		$this->new_name=$new_name;
		$this->id_user=Auth::user()['id'];
		
		$this->dir_root=Config::get('madlux_files_settings.file_root').Auth::user()['username'];
    }

    /**
     * @param $model
     * @param Repository $repository
     *
     * @return mixed
     */
    public function apply( $model, Repository $repository )
    {
		try{	
			$old_name=$model->where('user_id','=',$this->id_user)
				->where('id','=',$this->id_file)
				->get()->toArray();
			
			$model->where('user_id','=',$this->id_user)
				->whereRaw('CONCAT(href,"/",filename) like "'.$old_name[0]['href'].'/'.$old_name[0]['filename'].'%"')
				->update(array(
					'href' => DB::raw('REPLACE(href,"'.$old_name[0]['filename'].'","'.$this->new_name.'")')
				));
				
			$model->where('user_id','=',$this->id_user)
				->where('id','=',$this->id_file)
				->update(array(
					'filename' => $this->new_name,
				));
			
			rename($this->dir_root.$old_name[0]['href'].'/'.$old_name[0]['filename'],$this->dir_root.$old_name[0]['href'].'/'.$this->new_name);
		
			$this->setMessage($old_name[0]['href'].'/'.$this->new_name);
		
		}catch(Exception $e){
			$this->setError($e->getMessage());
		}
		
        return $model;
    }

}
