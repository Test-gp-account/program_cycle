
<?php
/* 
		File Name:		pagination.php
		Purposr :		To paginate Records
		Project:		gari point
		Created By:		Pankaj
		Created On:		19 APR 2010		
		
*/
class Pagination2 {
 

		
		private $totalPerPage;
		private $totalPagePerScreen;
    	private $currentPage;	
    	private $startPage;			
		private $firstResult; //only get
		private $nextPage; //only get
		private $previousPage;//only get		
		
		
		// The total values.
		private $totalPages;
		private $totalResults;
		private $totalPageArray;	//get	
  		
		public function  _construct(){
			 $this->totalPerPage=10;  //
			 $this->totalPagePerScreen=5;//
			 $this->previousPage=1;//
			 $this->$currentPage=1; //
			 $this->$startPage=1;	//
		 		 
		}

		/**
		 *	Setter / getter methods
		 *
		**/		
		
		public function setTotalPerPage($totalPerPage = 10) {
			if(is_numeric($totalPerPage)) {
				$this->totalPerPage = $totalPerPage;
			}
		}
		
		public function getTotalPerPage() {
			return (is_numeric($this->totalPerPage))? $this->totalPerPage : 0 ;
		}	
		
		
		public function setCurrentPage($currentPage = 1) {
			if(is_numeric($currentPage)) {
				$this->currentPage = $currentPage;
			}
		}		
		public function getCurrentPage() {
			return (is_numeric($this->currentPage))? $this->currentPage : 0 ;
		}
		
		public function setStartPage($startPage = 1) {
			if(is_numeric($startPage)) {
				$this->startPage = $startPage;
				$this->previousPage =$startPage;
			}
		}		
		public function getStartPage() {
			return (is_numeric($this->startPage))? $this->startPage : 0 ;
		}		
						
		

		
		public function getPreviousPage(){
				return $this->previousPage;
		}
		
		public function getNextPage(){
				return $this->nextPage;
		}		
		
		public function setTotalPagePerScreen($totalPagePerScreen = 5) {
 
			if(is_numeric($totalPagePerScreen)) {
 
				$this->totalPagePerScreen = $totalPagePerScreen;
 
			}
 
		}

		public function getTotalPagePerScreen() {
			return (is_numeric($this->totalPagePerScreen))? $this->totalPagePerScreen : 0 ;
		}
		
		public function getFirstResult() {
 
 			$page=$this->currentPage;
			if(is_numeric($page)) {
 
				$this->currentPage = mysql_real_escape_string($page);
				$this->firstResult = (($this->currentPage * $this->totalPerPage) - $this->totalPerPage);
				return $this->firstResult;
 
			}
 
		}				
		
		/**
		 *	Generates how many pages based on the total amount of results
		 *
		 *	@param array $results, an array of all the results
		 *	@return array of pages
		**/
		public function getPaginationResult($results) {
 
			$this->totalResults = $results;
			$totalPages = $this->totalResults / $this->totalPerPage;
			$this->totalPages = ceil($totalPages); 
			$x = $this->previousPage;
			$this->totalPageArray = array();
			if($x>$this->totalPagePerScreen ){
			 	$this->previousPage=($x-$this->totalPagePerScreen );
				$this->totalPageArray[] = "<<";
			} 
			$ctr=1;			
			while($x <= $this->totalPages) {
				 if($ctr<=$this->totalPagePerScreen){
				 
					$this->totalPageArray[] = $x;
				 }else if($ctr==$this->totalPagePerScreen+1){
				    $this->nextPage=$x;
					$this->totalPageArray[] = ">>";
					$this->nextPage=$x;
				 }	
				 $x++;
				 $ctr++;
			}
			return $this->totalPageArray;
		}
		
		
		function getTotalPageArray(){
		
		  return $this->totalPageArray;
		
		}
	
		/**
		 *	Generate the Pagination Text
		 *
		 *	@return text
		**/
		
		public function getPaginationLink($link,$style){


			$p = $this->currentPage;
			$preScreen=$this->startPage;
			$pagination="";	
			$firstPage="1";		
		
		
		
			foreach($this->totalPageArray as $pageNumber)
			{

				$firstPage=$pageNumber;
				if($pageNumber == $p)
				{
					
						$pagination .= '<a href="'.  $link . 'page='. $pageNumber . '&startpage=' . $preScreen . '"><strong>&nbsp;&nbsp;'.$pageNumber .'</strong></a>';
				}else if($pageNumber == ">>"){
				
						$pagination .= '<a href="' .  $link . 'page='. $this->nextPage .'&startpage=' . $this->nextPage  . '" class="' . $style . '" >&nbsp;&nbsp;Next >> </a>';
				
				 
				}else if($pageNumber == "<<"){
				
						$pagination .= '<a href="' .  $link . 'page='. $this->previousPage  . '&startpage=' . $this->previousPage . '" class="' . $style . '" >&nbsp;&nbsp;<< Previous</a>';
				
							 
				}
				else
				{
						$pagination .= '<a href="' .  $link . 'page='. $pageNumber  . '&startpage=' . $preScreen  . '" class="' . $style . '" >&nbsp;&nbsp;'.$pageNumber.'</a>';
				}

			}
		   
		   return ($firstPage==1)? "": $pagination ;
		}
		
	}

?>
