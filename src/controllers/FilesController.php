<?php

namespace Madlux\Filesystem\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Madlux\Filesystem\Models\Files;
use Madlux\Filesystem\Repositories\FilesRepository as FilesRepository;
use Madlux\Filesystem\Repositories\Criteria\Files\SaveFile;
use Madlux\Filesystem\Repositories\Criteria\Files\DeleteFileById;
use Madlux\Filesystem\Repositories\Criteria\Files\CreateFolder;
use Madlux\Filesystem\Repositories\Criteria\Files\UpdateName;
use Request;
use Config;

class FilesController extends Controller
{
	private $files;
	private $folder;
	
	public function __construct(FilesRepository $files){
		$this->files = $files;
		$this->folder = $this->hasInputFolder();
	}
	
	public function indexAction()
	{	
		$config=Config::get('madlux_files_settings');
		
		$user = Auth::user();
		
		$pattern="/\/[a-zа-я_0-9]*$|[a-zа-я_0-9]*$/i";
		
		if($this->folder===''){
			$slash='';
		}else{
			$slash='/';
			$this->folder='/'.$this->folder;
		}
		
		$files=Files::where('user_id','=',$user['id'])
			->where('href','=',$this->folder)
			->orderBy('type','desc')->get()->toArray();
		
		return view('files::files_index',[
			'user' => $user,
			'files' => $files,
			'config' => $config,
			'folder' => $this->folder,
			'pattern' => $pattern,
			'slash' => $slash,
		]);
	}
	
	public function updateAction()
	{
		$id=Request::get('id');
		$new_name=Request::get('name');
		
		$criteria = new UpdateName($id,$new_name);
		$model = $this->files->getByCriteria($criteria);
		
		return json_encode($criteria->getMessage());
	}
	
	public function saveFileAction()
	{
		if(Request::hasFile('input')){
			$criteria = new SaveFile($_FILES['input'],$this->folder);
			$model = $this->files->getByCriteria($criteria);
		}
		
		if (!Request::ajax())
		{
			return redirect()->action('\Packages\Users\Controllers\FilesController@indexAction', ['errors' => $criteria->getError()]);
		}
		
		return json_encode($criteria->files_responce);
	}
	
	public function deleteFileAction(){
		$criteria = new DeleteFileById(Request::get('id'),$this->folder);
		$model = $this->files->getByCriteria($criteria);
		
		if (!Request::ajax())
		{
			return redirect()->action('\Madlux\Filesystem\Controllers\FilesController@indexAction', ['errors' => $model]);
		}
		
		return $criteria->getError();
	}
	
	public function createFolderAction(){
		$criteria = new CreateFolder(Request::get('foldername'),$this->folder);
		$model = $this->files->getByCriteria($criteria);
		
		return var_export($criteria->getError(), true);
	}
	
	private function hasInputFolder(){
		if(Request::has('f')){
			return preg_replace('/^\//','',Request::get('f'));
		}else{
			return '';
		}
	}
	
}