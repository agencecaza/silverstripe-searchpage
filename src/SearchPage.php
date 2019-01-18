<?php

use SilverStripe\Forms\Form;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FormAction;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Versioned\Versioned;
SilverStripe\Security\Member

class SearchPage extends Page {

	private static $description = 'Search page';

}

class SearchPageController extends PageController {

	private static $allowed_actions = array (
		'SearchForm'
	);

	
	
	protected function init()
	{
		parent::init();

			if (isset($_GET['job']) == 'indexcontent') {
			if (Member::currentUser() && Member::currentUser()->inGroup(2)) {
				$pages = Page::get();
				foreach ($pages as $page) {
					$page->ContentSearch = $page->Title." ".$page->MenuTitle." ".$page->Content;
					$page->write();
					$page->publish('Stage', 'Live');
				}
				echo _t('SearchPage.JOBDONE',"The index Title and Content inside ContentSearch is done.");
				exit;
			} else {
				echo _t('SearchPage.JOBCANTDOACTION',"You can't do this operation. You must be loggued as administrator.");
				exit;
			}
		}
	}

	
	
	public function SearchForm() {

		$form = Form::create(
			$this,
			'SearchForm',
			FieldList::create(
					TextField::create('Keywords','')
						->setAttribute('placeholder', _t('SearchPage.KEYWORDS','Keywords...'))
			),
			FieldList::create(
					FormAction::create('SearchFormSubmit', _t('SearchPage.SEARCH','Search'))
			)
		);
		$form->setFormMethod('GET')
				 ->disableSecurityToken();

		return $form;

	}


	public function SearchFormSubmit($data, $form) {

		$partialmatchArray=array();

		$keywords = explode(" ", $data['Keywords']);

		foreach ($keywords as $k => $val) {
			array_push($partialmatchArray, $val);
		}

		$results = new ArrayList();

		$array = array( 'ContentSearch:PartialMatch' => $partialmatchArray );
		$pages = Versioned::get_by_stage('Page','Live')->filter( $array );

		foreach ($pages as $page) {

			$count=0;

			foreach ($partialmatchArray as $string) {
				if (stripos($page->ContentSearch, $string) !== false) {
					$count++;
				}
			}

			if ($count>0){
				$results->push(new ArrayData(
					array(
						'Title' => $page->Title,
						'Link' => $page->Link(),
						'Rank' => $count
					)
				));
			}
		}

		$resultslist = $results->sort('Rank')->reverse();

		$paginatedProperties = PaginatedList::create(
       		$resultslist,
       		$this->getRequest()
    		)->setPageLength(25);

    		return array (
       			'Results' => $paginatedProperties
    		);

	}

	public function Keywords() {
		if (empty($_GET['Keywords'])) {
			return true;
		}
	}

	public function KeywordsGet() {
		return $_GET['Keywords'];
	}



}
