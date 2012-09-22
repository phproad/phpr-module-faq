function make_questions_sortable()
{
    if ($('category_questions_list'))
        $('category_questions_list').makeListSortable('onSetQuestionOrders', 'question_order', 'question_id', 'sort_handle');
}

jQuery(document).ready(function($) {
    make_questions_sortable();
});