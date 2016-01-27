<?php namespace Madlux\Filesystem\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Config;

class Files extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['id', 'user_id','type','file_name','href','created_at','updated_at','filesize'];
	
	protected $table = 'users_files';
}
