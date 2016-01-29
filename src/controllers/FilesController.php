<?php

namespace Madlux\Filesystem\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Madlux\Filesystem\Models\Files;
use Madlux\Filesystem\Repositories\FilesRepository as FilesRepository;
use Madlux\Filesystem\Repositories\Criteria\Files\SaveFile;
use Madlux\Filesystem\Repositories\Criteria\Files\DeleteFileById;
use Madlux\Filesystem\Repositories\Criteria\Files\CreateFolder;
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
		
		if (!Auth::check()){
			return redirect($config['redirect_where_not_login']);
		}
		
		$user = Auth::user();
		
		$files=Files::where('user_id','=',$user['id'])
			->where('href','=',$this->folder)
			->orderBy('type','desc')->get()->toArray();
		
		/*
		//get folders-->
		$folders = array();
		$skip = array('.', '..');
		$files_in_dir = scandir($config['file_root'].$user['username']);
		
		foreach($files_in_dir as $file) {
			if(is_dir($config['file_root'].$user['username'].'/'.$file) && $file!=='.' && $file !=='..')
				$folders[]=$file;
		}
		//<--get folders
		*/
		
		$pattern="/\/[a-zа-я_0-9]*$|[a-zа-я_0-9]*$/i";
		
		if($this->folder==='')
			$slash='';
		else
			$slash='/';
		
		return view('files::files_index',[
			'user' => $user,
			'files' => $files,
			'config' => $config,
			'folder' => $this->folder,
			'pattern' => $pattern,
			'slash' => $slash,
		]);
	}
	
	public function saveFileAction(Request $request)
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
		if (Auth::check()){
			$criteria = new DeleteFileById(Request::get('id'),$this->folder);
			$model = $this->files->getByCriteria($criteria);
		}
		
		if (!Request::ajax())
		{
			return redirect()->action('\Madlux\Filesystem\Controllers\FilesController@indexAction', ['errors' => $model]);
		}
		
		return $criteria->getError();
	}
	
	public function createFolderAction(){
		if (Auth::check()){
			$criteria = new CreateFolder(Request::get('foldername'),$this->folder);
			$model = $this->files->getByCriteria($criteria);
			
			return var_export($criteria->getError(), true);
		}
	}
	
	private function hasInputFolder(){
		if(Request::has('f')){
			return Request::get('f');
		}else{
			return '';
		}
	}
	
}