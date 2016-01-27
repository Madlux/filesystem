<?php namespace Madlux\Filesystem\Repositories\Criteria\Files;

use Madlux\Filesystem\Repositories\Criteria\Files\MyCriteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Auth;
use Config;

class SaveFile extends MyCriteria 
{
	public function __construct($files,$root)
    {
        $this->files = $files;
		$this->root=$root;
		if(!isset($this->root))
			$this->root="";
		$this->id_user=Auth::user()['id'];
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
			foreach($this->files as $key1 => $value1)
				foreach($value1 as $key2 => $value2) 
					$result[$key2][$key1] = $value2;
			foreach($result as $file){
				
				$dir_root=Config::get('madlux_files_settings.file_root').Auth::user()['username'];
				$fileroot=$dir_root.'/'.$file['name'];
				
				$filenamedb=$file['name'];
				
				$id_file=$model
					->where('href','=',$this->root)
					->where('filename','=',$filenamedb)
					->where('user_id','=',$this->id_user)
					->where('type','=','file')
					->get()->toArray();
					
				
				if(!isset($id_file[0]['id'])){
					if(!is_dir($dir_root)) mkdir($dir_root);
					touch( $fileroot );
					
					$fp=fopen($fileroot, "w");
					fputs( $fp, file_get_contents($file['tmp_name']));
					
					$filesize=filesize($file['tmp_name']);
					$date=date('U');
					
					$model->insert([
						'href' => $this->root,
						'filename' => $filenamedb,
						'filesize' => $filesize,
						'user_id' => $this->id_user,
						'type' => 'file',
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
