<?php
namespace App\Lib;

class Permissions 
{
    private $Permissions = array(
        'Admins'=>array(
            'add_admin'=>'Add Admin', 
            'edit_admin'=>'Edit Admin',
            'change_admin_password'=>'Change Admin Password',
            'delete_admin'=>'Delete Admin'
        ),
        'Clients'=>array(
            'add_client'=>'Add Client',
            'edit_client'=>'Edit Client',
            'delete_client'=>'Delete Client'
        ),
        'Agents'=>array(
            'add_agent'=>'Add Agent', 
            'edit_agent'=>'Edit Agent',
            'delete_agent'=>'Delete Agent'
        ),
        'Categories'=>array(
            'add_category'=>'Add Categories', 
            'edit_category'=>'Edit Categories',
            'delete_category'=>'Delete Categories'
        ),
        'Products'=>array(
            'add_product'=>'Add Products', 
            'edit_product'=>'Edit Products',
            'copy_product'=>'Copy Products',
            'delete_product'=>'Delete Products',
            'tags' => 'Products Tags'
        ),
        'Selling Orders'=>array(
            'add_selling_order'=>'Add Selling Orders', 
            'edit_selling_orders'=>'Edit Selling Orders',
            'delete_selling_order'=>'Delete Selling Order',
            'update_moderator'=>'Update Moderator',
            //'view_all_selling_orders'=>'View All Orders (REP)'
        ),
        'Buying Orders'=>array(

            'add_buying_order'=>'Add Buying Orders', 

            'edit_buying_orders'=>'View Buying Orders',

            'delete_buying_order'=>'Delete Buying Order'

        ),

        'Reports'=>array(

            'main_reports'=>'Main Reports'

        ),

        'Reps Delivery'=>array(

            'reps_delivery'=>'Reps Delivery'

        ),

        'Inventory'=>array(

            'show_inventory'=>'Show Inventory',
            'edit_inventory'=>'Edit Inventory',
            'ruined_item'=>'Ruined Item',


        ),

        'Fullfillment'=>array(

            'fulfillment'=>'Fullfillment'

        ),

        'Expanses Category'=>array(

            'add_expanses_category'=>'Add Expanses Categories',

            'edit_expanses_category'=>'Edit Expanses Categories',

            'delete_expanses_category'=>'Delete Expanses Categories'

        ),

        'Expanses'=>array(

            'add_expanses'=>'Add Expanses',

            'edit_expanses'=>'Edit Expanses',

            'delete_expanses'=>'Delete Expanses'

        ),

        'Partners Category'=>array(

            'add_partners_category'=>'Add Partners Categories',

            'edit_partners_category'=>'Edit Partners Categories',

            'delete_partners_category'=>'Delete Partners Categories'

        ),

        'Partners'=>array(

            'add_partners'=>'Add Partners',

            'edit_partners'=>'Edit Partners',

            'delete_partners'=>'Delete Partners'

        ),

        'Profit & Loss'=>array(

            'profit_loss'=>'Profit & Loss'

        ),

        'Cities'=>array(

            'add_city'=>'Add City',

             'edit_city'=>'Edit City',

             'delete_city'=>'Delte City'

        ),

        'Payment Methods'=>array(

            'add_pay_method'=>'Add Payment Methods',

            'edit_pay_method'=>'Edit Payment Methods',

            'delete_pay_method'=>'Delete Payment Methods'

        ),

        'Colors'=>array(

            'add_color'=>'Add Colors',

            'edit_color'=>'Edit Colors',

            'delete_color'=>'Delete Colors'

        ),

        'Sizes'=>array(

            'add_size'=>'Add Sizes',

            'edit_size'=>'Edit Sizes',

            'delete_size'=>'Delete Sizes'

        ),

        'Order Status'=>array(

            'add_status'=>'Add Status',

            'edit_status'=>'Edit Status',

            'delete_status'=>'Delete Status'

        ),

        'Order Category'=>array(

            'add_order_category'=>'Add Category',

            'edit_order_category'=>'Edit Category',

            'delete_order_category'=>'Delete Category'

        ),

        'Order Notes'=>array(

            'order_notes'=>'Order Notes',

            'all_order_notes'=>'All Order Notes'

        ),

        'Ads'=>array(

            'show_ads'=>'Show Ads',
            'add_ads'=>'Add Ads',
            'edit_ads'=>'Edit Ads',
            'delete_ads'=>'Delete Ads',

        ),

        'File Gard'=>array(
            'show_file_gard'=>'Show File Gard',
            'delete_file_gard'=>'Delete File Gard',
        ),

        'Logistic'=>array(
            'show_logistic'=>'Show Logistic',
        ),

        'Active Ads'=>array(
            'show_active_ads'=>'Show Active Ads',
        ),



    );

    

    public function all_permissions()

    {

        return $this->Permissions;

    }

    

    public function permissions_group($permission)

    {

        return $this->Permissions[$permission];

    }

}