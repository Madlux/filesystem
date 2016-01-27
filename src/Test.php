<?php 
	namespace Otaku\Test\Assets\Classes;
	
	use Otaku\Test\Models\Tests;
	use Otaku\Test\Models\Cheirs;
	use Otaku\Test\Models\Questions;
	use Otaku\Test\Models\QuestionsInTest;
	use Otaku\Test\Assets\Classes\Question;
	use Otaku\Test\Assets\Classes\MyUsersTests;
	use Session;

    class Test{
        public $idCheir;
        public $nameCheir;
        public $idTest;
        public $nameTest;
        public $questions;
        public $timeTest;
		public $usersTest;
        
        function createTest($idCheir,$idTest,$nameCheir,$myUser,$isMain,$category){
		
			$this->idCheir=$idCheir;
			$this->nameCheir=$nameCheir;
			$this->idTest=$idTest;
			
			//$nameTest=Tests::where("id","=",$idTest)->get()->toArray();
			//$this->nameTest=$nameTest[0]['test'];
			
			if($idTest==''){
				$id_tests=Tests::select('id')->where('id_cheir','=',$idCheir)->get()->toArray();
			}else{
				$this->idCheir=Tests::select('id_cheir')->where('id','=',$idTest)->get()->toArray();
				$this->idCheir=$this->idCheir[0]['id_cheir'];
				$id_tests=Tests::select('id')->where('id','=',$idTest)->get()->toArray();
			}
			
			$this->timeTest=Cheirs::select('time')->where('id','=',$this->idCheir)->get()->toArray();
			$this->timeTest=intval($this->timeTest[0]['time']);
			
			if(isset($id_tests[0])){
				$questionsId=QuestionsInTest::select('id_question')->whereIn('id_test',$id_tests)->get()->toArray();
				if($myUser!=null){
					if($isMain=="0"){
						if($myUser->hasPermission(array($this->idCheir),'permissions','')==false){
							if(isset($questionsId[0])){
								$questions=$this->selectQuestions($category,$questionsId)->orderByRaw('Rand()')->take(20)->get()->toArray();
							}
						}else{
							if(isset($questionsId[0])){
								$questions=$this->selectQuestions($category,$questionsId)->orderByRaw('Rand()')->get()->toArray();
							}
						}
					}else{
						if(isset($questionsId[0])){
							$questions=$this->selectQuestions($category,$questionsId)->orderByRaw('Rand()')->get()->toArray();
						}
					}
				}else{
					if(isset($questionsId[0])){
						$questions=$this->selectQuestions($category,$questionsId)->orderByRaw('Rand()')->take(20)->get()->toArray();
					}
				}
			}
			
			if(isset($questions)){
				for($i=0;$i<sizeof($questions);$i++){
					$question=new Question();
					$question->createQuestions($questions[$i]['id_question'], $questions[$i]['question']);
					$this->questions[]=$question;
				}
			}
			
			$this->usersTest=new MyUsersTests();
			$this->usersTest->createEmptyUsersTest($this->idCheir,$category,$isMain);
			
		}
        
        function __construct() {
            $this->idCheir=null;
            $this->nameCheir=null;
            $this->idTest=null;
            $this->nameTest=null;
            $this->questions=null;
        }
		
		private function selectQuestions($category,$questionsId){
			if($category=='' || !isset($category) || $category=='Выберите категорию'){
				return Questions::whereIn('id_question',$questionsId)->where('published','=','1');
			}elseif($category=='1'){
				return Questions::whereIn('id_question',$questionsId)->where('published','=','1')->where('first_category','=','1');
			}elseif($category=='2'){
				return Questions::whereIn('id_question',$questionsId)->where('published','=','1')->where('second_category','=','1');
			}elseif($category=='Высшая'){
				return Questions::whereIn('id_question',$questionsId)->where('published','=','1')->where('third_category','=','1');
			}else{
				return Questions::whereIn('id_question',$questionsId)->where('published','=','1');
			}
		}
    } 
?>
