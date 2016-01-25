<?php

namespace Madlux\Filesystem\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Madlux\Filesystem\Models\Files;
use Madlux\Filesystem\Repositories\FilesRepository as FilesRepository;
use Madlux\Filesystem\Repositories\Criteria\Files\SaveFile;
use Madlux\Filesystem\Repositories\Criteria\Files\DeleteFileById;
use Request;
use Config;

class FilesController extends Controller
{
	private $files;
	
	public function __construct(FilesRepository $files){
		$this->files=$files;
	}
	
	public function indexAction()
	{
		if (!Auth::check()){
			return redirect('/auth/login');
		}
		
		$user = Auth::user();
		
		$files=Files::where('user_id','=',$user['id'])->get()->toArray();
		
		$config=Config::get('madlux_files_settings');
		
		return view('users::files_index',[
			'user' => $user,
			'files' => $files,
			'config' => $config
		]);
	}
	
	public function saveFileAction(Request $request)
	{
		if(Request::hasFile('input')){
			$criteria = new SaveFile($_FILES['input']);
			$model = $this->files->getByCriteria($criteria);
		}
		
		if (!Request::ajax())
		{
			return redirect()->action('\Packages\Users\Controllers\FilesController@indexAction', ['errors' => $criteria->getError()]);
		}
	}
	
	public function deleteFileAction(){
		if (Auth::check()){
			$criteria = new DeleteFileById(Request::get('id'));
			$model = $this->files->getByCriteria($criteria);
		}
		
		if (!Request::ajax())
		{
			return redirect()->action('\Packages\Users\Controllers\FilesController@indexAction', ['errors' => null]);
		}
		
		return $criteria->getError();
	}
	
}