<?php namespace Madlux\Filesystem\Repositories\Criteria\Files;

use Madlux\Filesystem\Repositories\Criteria\Files\MyCriteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Auth;
use Config;

class SaveFile extends MyCriteria 
{
	public function __construct($files)
    {
        $this->files = $files;
    }

    /**
     * @param $model
     * @param Repository $repository
     *
     * @return mixed
     */
    public function apply( $model, Repository $repository )
    {
		if(isset($this->files)){
			$id_user=Auth::user()['id'];
			foreach($this->files as $key1 => $value1)
				foreach($value1 as $key2 => $value2) 
					$result[$key2][$key1] = $value2;
			foreach($result as $file){
				
				$href=url('users/files/'.Auth::user()['username']);
				
				$dir_root=Config::get('madlux_files_settings.file_root').Auth::user()['username'];
				$fileroot=$dir_root.'/'.$file['name'];
				
				$filenamedb=$file['name'];
				
				$id_file=$model->where('href','=',$href)
					->where('file_name','=',$filenamedb)->get()->toArray();
					
				
				if(!isset($id_file[0]['id'])){
					if(!is_dir($dir_root)) mkdir($dir_root);
					touch( $fileroot );
					
					$fp=fopen($fileroot, "w");
					fputs( $fp, file_get_contents($file['tmp_name']));
					
					$filesize=filesize($file['tmp_name']);
					$date=date('U');
					
					$model->insert([
						'href' => $href,
						'file_name' => $filenamedb,
						'filesize' => $filesize,
						'user_id' => $id_user,
						'fileroot' => $fileroot,
					]);
					
					$errors=false;
				}else{
					$this->setError("Файл с названием $filenamedb уже существует");
				}
			}
		}

        return $model;
    }

}
