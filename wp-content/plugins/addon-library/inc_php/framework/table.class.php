<?php

/**
 * 
 * outputs all related to table with paging
 *
 */
class UniteTableUC{
	
	private $isPaging = false;
	private $page;
	private $inPage;
	private $total;
	private $numPages;
	private $baseUrl;
	
	private $defaultInPage = 10;
		
	
	/**
	 * validate that the paging is inited
	 */
	private function validatePaging(){
		
		if($this->isPaging == false)
			UniteFunctionsUC::throwError("The paging should be available");
		
	}
	
	/**
	 * get paging options from get and default
	 */
	public function getPagingOptions(){
		
		$page = UniteFunctionsUC::getGetVar("table_page",1,UniteFunctionsUC::SANITIZE_ID);
		$page = (int)$page;
		
		$inpage = UniteFunctionsUC::getGetVar("table_inpage", $this->defaultInPage,UniteFunctionsUC::SANITIZE_ID);
		$inpage = (int)$inpage;
		
		$output = array();
		$output["page"] = $page;
		$output["inpage"] = $inpage;
		
		return($output);
	}
	
	
	/**
	 * set paging data
	 */
	public function setPagingData($baseURl, $data){
		
		$this->baseUrl = $baseURl;
		
		$this->total = UniteFunctionsUC::getVal($data, "total");
		$this->page = UniteFunctionsUC::getVal($data, "page");
		$this->inPage = UniteFunctionsUC::getVal($data, "inpage");
		$this->numPages = UniteFunctionsUC::getVal($data, "num_pages");
		
		UniteFunctionsUC::validateNotEmpty($this->inPage, "in page");
		if($this->total > 0){
			UniteFunctionsUC::validateNotEmpty($this->page, "page");
			UniteFunctionsUC::validateNotEmpty($this->numPages, "num pages");
		}
		
		$this->isPaging = true;
	}
	
	/**
	 * get page url
	 */
	private function getUrlPage($page){
		
		$urlPage = UniteFunctionsUC::addUrlParams($this->baseUrl, "table_page=".$page);
		
		return($urlPage);
	}
	
	
	/**
	 * get pagination html
	 */
	public function getPaginationHtml(){
		
		$this->validatePaging();
		
		$item_per_page = $this->inPage;
		$current_page = $this->page;
		$total_records = $this->total;
		$total_pages = $this->numPages;
		$page_url = $this->baseUrl;
		
		
		//num pages to show links
		$pagesForShowLinks = 5;
		//$isShowExtras = ($total_pages > $pagesForShowLinks);
		
		$isShowExtras = true;
		
		$pagination = '';
		if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
			$pagination .= '<ul class="unite-pagination">';
			
			$right_links    = $current_page + 8;
			$previous       = $current_page - 1; //previous link
			$next           = $current_page + 1; //next link
			$first_link     = true; //boolean var to decide our first link
			
			//put first and previous
			if($current_page > 1 && $isShowExtras == true){
				$previous_link = ($previous==0)?1:$previous;
				
				$urlFirst = $this->getUrlPage(1);
				$urlPrev = $this->getUrlPage($previous_link);
				
				$titleFirst = __("First", ADDONLIBRARY_TEXTDOMAIN);
				$titlePrev = __("Previous", ADDONLIBRARY_TEXTDOMAIN);
				
				$textFirst = "";
				$textPrev = "";
				
				$pagination .= '<li class="unite-first"><a href="'.$urlFirst.'" title="'.$titleFirst.'" > &laquo; '.$textFirst.'</a></li>'; //first link
				$pagination .= '<li><a href="'.$urlPrev.'" title="'.$titlePrev.'">&lt; '.$textPrev.'</a></li>'; //previous link
				
				for($i = ($current_page-3); $i < $current_page; $i++){ //Create left-hand side links
					if($i > 0){
						$urlPage = $this->getUrlPage($i);
						
						$pagination .= '<li><a href="'.$urlPage.'">'.$i.'</a></li>';
					}
				}
				$first_link = false; //set first link to false
			}
		
			if($first_link){ //if current active page is first link
				$pagination .= '<li class="unite-first unite-active"><a href="javascript:void(0)">'.$current_page.'</a></li>';
			}elseif($current_page == $total_pages){ //if it's the last active link
				$pagination .= '<li class="unite-last unite-active"><a href="javascript:void(0)">'.$current_page.'</a></li>';
			}else{ //regular current link
				$pagination .= '<li class="unite-active"><a href="javascript:void(0)">'.$current_page.'</a></li>';
			}
			
			for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
				if($i<=$total_pages){
					
					$urlPage = UniteFunctionsUC::addUrlParams($page_url, "table_page={$i}");
					
					$pagination .= '<li><a href="'.$urlPage.'">'.$i.'</a></li>';
				}
			}
			
			//show first / last
			if($current_page < $total_pages && $isShowExtras == true){
				
				//next and last pages
				$next_link = ($i > $total_pages)? $total_pages : $i;
				
				$urlNext = $this->getUrlPage($next_link);
				$urlLast = $this->getUrlPage($total_pages);
				
				$titleNext = __("Next Page", ADDONLIBRARY_TEXTDOMAIN);
				$titleLast = __("Last Page", ADDONLIBRARY_TEXTDOMAIN);
				
				$textNext = "";
				$textLast = "";
				
				$pagination .= "<li><a href=\"{$urlNext}\" title=\"$titleNext\" >{$textNext} &gt;</a></li>"; 
				$pagination .= "<li class=\"unite-last\"><a href=\"{$urlLast}\" title=\"$titleLast\" >{$textLast} &raquo; </a></li>"; 
			}
			
			$pagination .= '</ul>';
		}
		
		return($pagination);
	}
	
	
	/**
	 * draw table pagination
	 */
	public function putPaginationHtml(){
		
		$this->validatePaging();
		$html = $this->getPaginationHtml();
		
		echo $html;
	}
	
	
}