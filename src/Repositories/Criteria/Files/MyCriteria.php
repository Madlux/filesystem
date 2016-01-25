<?php namespace Madlux\Filesystem\Repositories\Criteria\Files;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

class MyCriteria extends Criteria 
{
	private $error;
	
	public function getError(){
		return $this->error;
	}
	
	public function setError($error){
		$this->error=$error;
	}
	
	public function apply( $model, Repository $repository ){}
}
