<?php

class Faq_Actions extends Cms_Action_Base
{

    public function faq()
    {
        $categories = Faq_Category::create()->order('sort_order')->find_all();

        if (!$categories)
        {
            $this->data['categories'] = null;
            return;
        }

        $this->data['categories'] = $categories;
    }

    public function category()
    {

        $category_code = $this->request_param(0);
        $category = Faq_Category::find_by_code($category_code);

        if (!$category)
        {
            $this->data['category'] = null;
            $this->data['faq'] = null;
            return;
        }

        $this->data['category'] = $category;
        $this->data['faq'] = $category->questions;
    }

}