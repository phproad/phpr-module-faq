<?php

class Faq_Module extends Core_Module_Base
{
    protected function set_module_info()
    {
        return new Core_Module_Detail(
            "FAQ",
            "Manage frequently asked questions",
            "Scripts Ahoy!",
            "http://scriptsahoy.com/"
        );
    }

    public function build_admin_menu($menu)
    {
        $top = $menu->add('faq', 'FAQ', 'faq/categories');
    }

    public function build_admin_permissions($host)
    {
        $host->add_permission_field($this, 'manage_faq', 'Manage FAQs')->renderAs(frm_checkbox)->comment('Create or delete FAQs');
    }    
}