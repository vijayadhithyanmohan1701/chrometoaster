<?php

class SpecsSystemModelAdmin extends CatalogPageAdmin {

    private static $page_length = 100;

    private static $managed_models = array(
        'SpecPage'     => ['title' => 'Specs'],
        'SpecCategory' => ['title' => 'Product/Service Categories'],
        'LicensePage'          => ['title' => 'Licenses'],
    );

    private static $url_segment = 'Specs';

    private static $menu_title = 'Specs';

    private static $menu_icon = 'mysite/icons/Spec.png';

    public function getEditForm($id = null, $fields = null) {
        $form = parent::getEditForm($id, $fields);

        $gridFieldName = $this->sanitiseClassName($this->modelClass);
        $gridField = $form->Fields()->fieldByName($gridFieldName);

        return $form;
    }

    public function getSearchContext() {
        /** @var SearchContext $context */
        $context = parent::getSearchContext();

        if ($this->modelClass === 'Spec') {

            $context->getFields()->fieldByName('q[Category__ID]')->setEmptyString('(any)');

            $context->getFields()->push(
                DropdownField::create('q[Status]', 'Status', ['Published' => 'Published', 'Draft' => 'Draft'])
                             ->setHasEmptyDefault(true)->setEmptyString('(any)')
            );
        }

        return $context;
    }

    public function getList() {
        /** @var DataList $list */
        $list = parent::getList();

        if ($this->modelClass === 'Spec' && ($q = $this->getRequest()->requestVar('q'))) {
            if (isset($q['Status']) && $q['Status'] === 'Draft') {
                $list = $list->where('ReviewDate IS NOT NULL');
            } elseif (isset($q['Status']) && $q['Status'] === 'Published') {
                $list = $list->where('ReviewDate IS NULL');
            }
        }

        return $list;
    }


}