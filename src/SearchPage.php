<?php

use SilverStripe\Forms\Form;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FormAction;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Security\Member;

class SearchPage extends Page
{
    private static $description = 'Search page';
}

class SearchPageController extends PageController
{
    private static $allowed_actions = array(
        'SearchForm'
    );

    public function SearchForm()
    {
        $form = Form::create(
            $this,
            'SearchForm',
            FieldList::create(
                TextField::create('Keywords', '')
                        ->setAttribute('placeholder', _t('SearchPage.KEYWORDS', 'Keywords...'))
            ),
            FieldList::create(
                FormAction::create('SearchFormSubmit', _t('SearchPage.SEARCH', 'Search'))
            )
        );
        $form->setFormMethod('GET')
                 ->disableSecurityToken();

        return $form;
    }


    public function SearchFormSubmit($data, $form)
    {
        $rank[0] = 0;

        $strings = array(
        );

        /*
        *	Check if exist a perfect match and add a first rank
        *
        */
        $pages = Versioned::get_by_stage('Page', 'Live')->filterAny(
            array(
                'Title:PartialMatch:nocase' => $data['Keywords'],
                'Content:PartialMatch:nocase' => $data['Keywords'],
                'ContentSearch:PartialMatch:nocase' => $data['Keywords'],
            )
        );
        if (strlen($data['Keywords']) > 2) {
            if ($pages) {
                foreach ($pages as $page) {
                    if ($page) {
                        if (!isset($rank[$page->ID])) {
                            $rank[$page->ID]=0;
                        }
                        $rank[$page->ID] ++;
                    }
                }
            }
        }

        /*
        *	Check every words submitted and remove words containing 2 caracters or less. Return less results.
        *
        */
        $keywords = explode(" ", str_replace($strings, "", $data['Keywords']));

        foreach ($keywords as $value => $val) {
            if (strlen($val) > 2) {
                $pages = Versioned::get_by_stage('Page', 'Live')->filterAny(
                    array(
                        'Title:PartialMatch:nocase' => $val,
                        'Content:PartialMatch:nocase' => $val,
                        'ContentSearch:PartialMatch:nocase' => $val,
                    )
                );

                if ($pages) {
                    foreach ($pages as $page) {
                        if ($page) {
                            if (!isset($rank[$page->ID])) {
                                $rank[$page->ID]=0;
                            }
                            $rank[$page->ID] ++;
                        }
                    }
                }
            }
        }

        /*
        * Check pages add per rank.
        *
        */
        $results = new ArrayList();

        foreach ($rank as $value => $key) {
            $page = Versioned::get_by_stage('Page', 'Live')->exclude('ClassName', 'SilverStripe\ErrorPage\ErrorPage')->filter('ID', $value)->first();

            if ($page) {
                $results->push(
                    ArrayData::create(
                        array(
                            'Title' => $page->Title,
                            'Link' => $page->Link(),
                            'Rank' => $rank[$value],
                        )
                    )
                );
            }
        }

        /*
        * Sort higher is the score on top.
        *
        */
        $resultslist = $results->sort('Rank')->reverse();



        /*
        * Create a paginated pages results
        *
        */
        $paginatedProperties = PaginatedList::create(
            $resultslist,
            $this->getRequest()
        )->setPageLength(25);

        return array(
            'Results' => $paginatedProperties
        );
    }

    public function Keywords()
    {
        if (empty($_GET['Keywords'])) {
            return true;
        }
    }

    public function KeywordsGet()
    {
        return $_GET['Keywords'];
    }
}
