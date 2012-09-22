<?php

class Faq_Categories extends Admin_Controller
{
    public $implement = 'Db_ListBehavior, Db_FormBehavior';
    public $list_model_class = 'Faq_Category';
    public $list_record_url = null;
    public $list_render_as_sliding_list = true;
    public $list_root_level_label = 'Categories';
    public $list_handle_row_click = false;
    public $list_no_sorting = false;
    public $list_no_pagination = false;
    public $list_no_setup_link = false;
    public $list_custom_body_cells = null;
    public $list_custom_head_cells = null;

    public $form_preview_title = 'Category';
    public $form_create_title = 'New Category';
    public $form_edit_title = 'Edit Category';
    public $form_model_class = 'Faq_Category';
    public $form_not_found_message = 'Category not found';
    public $form_redirect = null;

    public $form_flash_id = 'form_flash';
    public $form_edit_save_flash = 'The category has been successfully saved';
    public $form_create_save_flash = 'The category has been successfully added';
    public $form_edit_delete_flash = 'The category has been successfully deleted';
    public $form_edit_save_auto_timestamp = true;

    protected $required_permissions = array('faq:manage_faq');

    protected $globalHandlers = array(
        'onSetQuestionOrders',
        'onLoadCategoryQuestionForm',
        'onAddCategoryQuestion',
        'updateCategoryQuestionList',
        'onDeleteCategoryQuestion'
    );

    public function __construct()
    {
        parent::__construct();
        $this->app_menu = 'faq';
        $this->app_module_name = 'FAQ';

        $this->list_record_url = url('/faq/categories/edit/');
        $this->form_redirect = url('/faq/categories');
        $this->app_page = 'categories';
    }

    public function index()
    {
        $this->app_page_title = 'Categories';
    }

    // Category sorting
    //

    public function sort_order()
    {
        $this->app_page_title = 'Manage Category Order';
        $this->setup_reorder_categories_list();
    }

    protected function setup_reorder_categories_list()
    {
        $this->list_record_url = null;
        $this->list_no_sorting = true;
        $this->list_no_pagination = true;
        $this->list_no_setup_link = true;
        $this->list_search_enabled = false;
        $this->list_custom_head_cells = PATH_APP.'/modules/faq/controllers/faq_categories/_handle_head_col.htm';
        $this->list_custom_body_cells = PATH_APP.'/modules/faq/controllers/faq_categories/_handle_body_col.htm';
    }

    protected function sort_order_onSetOrders()
    {
        try
        {
            Faq_Category::create()->set_item_orders(post('item_ids'), post('sort_orders'));
        }
        catch (Exception $ex)
        {
            Phpr::$response->ajaxReportException($ex, true, true);
        }
    }

    public function listOverrideSortingColumn($sorting_column)
    {
        if (Phpr::$router->action == 'sort_order')
        {
            $result = array('field'=>'sort_order', 'direction'=>'asc');
            return (object)$result;
        }

        return $sorting_column;
    }


    // Questions
    //

    public function formAfterEditSave($model, $session_key)
    {
        $model = $this->viewData['form_model'] = Faq_Category::create()->find($model->id);

        $this->renderMultiple(array(
            'form_flash'=>flash()
        ));

        return true;
    }

    protected function onLoadCategoryQuestionForm()
    {
        try
        {
            $id = post('category_question_id');
            $question = $id ? Faq_Question::create()->find($id) : Faq_Question::create();
            if (!$question)
                throw new Phpr_ApplicationException('Attribute not found');

            $question->define_form_fields();

            $this->viewData['question'] = $question;
            $this->viewData['session_key'] = post('edit_session_key');
            $this->viewData['question_id'] = post('category_question_id');
            $this->viewData['trackTab'] = false;
        }
        catch (Exception $ex)
        {
            $this->handlePageError($ex);
        }

        $this->renderPartial('category_question_form');
    }

    protected function onAddCategoryQuestion($parent_id = null)
    {
        try
        {
            $id = post('question_id');
            $question = $id ? Faq_Question::create()->find($id) : Faq_Question::create();
            if (!$question)
                throw new Phpr_ApplicationException('Question not found');

            $category = $this->getCategoryObj($parent_id);
            $question->init_columns_info();
            $question->define_form_fields();
            $question->save(post('Faq_Question'), $this->formGetEditSessionKey());

            if (!$id)
                $category->questions->add($question, post('category_session_key'));
        }
        catch (Exception $ex)
        {
            Phpr::$response->ajaxReportException($ex, true, true);
        }
    }

    protected function updateCategoryQuestionList($parent_id = null)
    {
        try
        {
            $this->viewData['form_model'] = $this->getCategoryObj($parent_id);
            $this->renderPartial('question_list');
        }
        catch (Exception $ex)
        {
            Phpr::$response->ajaxReportException($ex, true, true);
        }
    }

    protected function onDeleteCategoryQuestion($parent_id = null)
    {
        try
        {
            $category = $this->getCategoryObj($parent_id);

            $id = post('category_question_id');
            $question = $id ? Faq_Question::create()->find($id) : Faq_Question::create();
            if ($question)
                $category->questions->delete($question, $this->formGetEditSessionKey());

            $this->viewData['form_model'] = $category;
            $this->renderPartial('question_list');
        }
        catch (Exception $ex)
        {
            Phpr::$response->ajaxReportException($ex, true, true);
        }
    }

    private function getCategoryObj($id)
    {
        return strlen($id) ? $this->formFindModelObject($id) : $this->formCreateModelObject();
    }

    protected function onSetQuestionOrders($parent_id = null)
    {
        Faq_Question::create()->set_item_orders(post('item_ids'), post('sort_orders'));
    }
}

