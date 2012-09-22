<?php

class Faq_Category extends Db_ActiveRecord
{
    public $table_name = 'faq_categories';
    public $implement = 'Db_AutoFootprints, Db_Act_As_Tree, Db_Sortable';

    public $act_as_tree_parent_key = 'parent_id';
    public $act_as_tree_sql_filter = null;
    public $auto_footprints_visible = true;

    public $has_many = array(
        'questions' => array('class_name'=>'Faq_Question', 'delete'=>true, 'order'=>'sort_order', 'foreign_key'=>'category_id')
    );

    public $belongs_to = array(
        'parent' => array('class_name' => 'Faq_Category', 'foreign_key'=>'parent_id')
    );

    public $calculated_columns = array(
        'question_num'=>array('sql'=>"(select count(*) from faq_questions where faq_questions.category_id=faq_categories.id)", 'type'=>db_number),
    );

    public static function create()
    {
        return new self();
    }

    public function define_columns($context = null)
    {
        $this->define_relation_column('parent', 'parent', 'Parent', db_varchar, '@name')->defaultInvisible();
        $this->define_column('name', 'Name')->order('asc')->validation()->fn('trim')->required("Please specify the category name.");
        $this->define_column('description', 'Description')->validation()->fn('trim');
        $this->define_column('question_num', 'Question Number');
        $this->define_column('sort_order', 'Sort Order');
        $this->define_column('code', 'API Code')->defaultInvisible()->validation()->fn('trim')->fn('mb_strtolower')->unique('Category with the specified  API code already exists.');

        $this->define_multi_relation_column('questions', 'questions', 'Questions', '@id')->invisible();
    }

    public function define_form_fields($context = null)
    {
        $this->add_form_field('name', 'left');
        $this->add_form_field('parent','right')->emptyOption('<none>')->optionsHtmlEncode(true);
        $this->add_form_field('description')->renderAs(frm_textarea)->size('small');

        $this->add_form_field('code', 'left')->comment('Use this code for accessing the category in the API calls.', 'above');
        $this->add_form_section('Specify the question order for this category');
        $this->add_form_field('questions');
    }

    // Events
    //

    public function before_delete($id=null)
    {
        if ($this->question_num)
            throw new Phpr_ApplicationException('This category cannot be deleted because it contains '.$this->question_num.' questions(s).');
    }

    public function after_create()
    {
        Db_DbHelper::query('update faq_categories set sort_order=:sort_order where id=:id', array(
            'sort_order'=>$this->id,
            'id'=>$this->id
        ));

        $this->sort_order = $this->id;
    }

    // Options
    //

    public function get_parent_options($key_value = -1, $max_level = 100)
    {
        return $this->get_page_tree_options($key_value, $max_level);
    }

    private function list_parent_options($items, &$result, $level, $ignore, $maxLevel, $url_key = false)
    {
        if ($maxLevel !== null && $level > $maxLevel)
            return;

        foreach ($items as $item)
        {
            if ($ignore !== null && $item->id == $ignore)
                continue;

            $key = $url_key ? $item->url_title : $item->id;

            $result[$key] = str_repeat("&nbsp;", $level*3).h($item->name);
            $this->list_parent_options($item->list_children('faq_categories.sort_order'), $result, $level+1, $ignore, $maxLevel, $url_key);
        }
    }

    public function get_page_tree_options($key_value, $max_level = 100)
    {
        $obj = new self();
        $result = array();
        if ($key_value == -1)
        {
            $this->list_parent_options($obj->list_root_children('faq_categories.sort_order'), $result, 0, $this->id, $max_level);
        }
        else
        {
            if ($key_value == null)
                return $result;

            $obj = AhoyWiki_Page::create();
            $obj = $obj->find($key_value);

            if ($obj)
                return h($obj->name);
        }
        return $result;
    }
}

