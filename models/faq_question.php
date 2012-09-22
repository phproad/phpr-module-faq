<?php

class Faq_Question extends Db_ActiveRecord
{
    public $table_name = 'faq_questions';
    public $implement = "Db_Sortable";

    protected $api_added_columns = array();

    public $custom_columns = array(
        'description_plain' => db_varchar
    );

    public static function create()
    {
        return new self();
    }

    public function define_columns($context = null)
    {
        $this->define_column('created_at', 'Added')->order('desc');
        $this->define_column('title', 'Question')->validation()->fn('trim')->required("Please enter a question.");
        $this->define_column('description', 'Answer')->invisible()->validation()->fn('trim')->required("Please enter an answer.");
        $this->define_column('description_plain', 'Answer');
    }

    public function define_form_fields($context = null)
    {
        $this->add_form_field('title');
        $this->add_form_field('description')->renderAs(frm_html)->size('medium');
    }

    public function after_create()
    {
        Db_DbHelper::query('update faq_questions set sort_order=:sort_order where id=:id', array(
            'sort_order'=>$this->id,
            'id'=>$this->id
        ));

        $this->sort_order = $this->id;
    }

    public function eval_description_plain()
    {
        if (strlen($this->description))
            return Phpr_Html::deparagraphize($this->description);
        else
            return null;
    }

}