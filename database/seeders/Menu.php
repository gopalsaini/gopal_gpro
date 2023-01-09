<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ranium\SeedOnce\Traits\SeedOnce;

class Menu extends Seeder
{
    use SeedOnce;
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
    public function run(){

        $menu = [
            ['id'=>'1','label' => 'Dashboard','link' => 'admin/dashboard','active_link' => 'admin/dashboard','icon' => 'fa fa-desktop','parent' => 0,'sort' => 1,'status'=>'1'],
            ['id'=>'2','label' => 'Slider','link' => '#','active_link' => 'admin/slider/*','icon' => 'fa fa-image','parent' => 0,'sort' => 2,'status'=>'1'],
            ['id'=>'3','label' => 'Add','link' => 'admin/slider/add','active_link' => 'admin/slider/add','icon' => '','parent' => 2,'sort' => 1,'status'=>'1'],
            ['id'=>'4','label' => 'List','link' => 'admin/slider/list','active_link' => 'admin/slider/list','icon' => '','parent' => 2,'sort' => 2,'status'=>'1'],
            ['id'=>'5','label' => 'Users','link' => 'admin/user/list','active_link' => 'admin/user/*','icon' => 'fa fa-users','parent' => 0,'sort' => 3,'status'=>'1'],
            ['id'=>'6','label' => 'Catalog','link' => '#','active_link' => 'admin/catalog/*','icon' => 'fa fa-desktop','parent' => 0,'sort' => 4,'status'=>'1'],
			['id'=>'7','label' => 'Category','link' => '#','active_link' => 'admin/catalog/category/*','icon' => '','parent' => 6,'sort' => 1,'status'=>'1'],
            ['id'=>'8','label' => 'Add','link' => 'admin/catalog/category/add','active_link' => 'admin/catalog/category/add','icon' => '','parent' =>7 ,'sort' => 1,'status'=>'1'],
            ['id'=>'9','label' => 'List','link' => 'admin/catalog/category/list','active_link' => 'admin/catalog/category/list','icon' => '','parent' => 7,'sort' => 2,'status'=>'1'],
            ['id'=>'10','label' => 'Products','link' => '#','active_link' => 'admin/catalog/product/*','icon' => '','parent' => 6,'sort' => 2,'status'=>'1'],
			['id'=>'11','label' => 'Add','link' => 'admin/catalog/product/add','active_link' => 'admin/catalog/product/add','icon' => '','parent' =>10 ,'sort' => 1,'status'=>'1'],
            ['id'=>'12','label' => 'List','link' => 'admin/catalog/product/list','active_link' => 'admin/catalog/product/list','icon' => '','parent' => 10,'sort' => 3,'status'=>'1'],
			['id'=>'13','label' => 'Variant Attribute','link' => '#','active_link' => 'admin/catalog/variant-attribute/*','icon' => '','parent' => 6,'sort' => 2,'status'=>'1'],
			['id'=>'14','label' => 'Add','link' => 'admin/catalog/variant-attribute/add','active_link' => 'admin/catalog/variant-attribute/add','icon' => '','parent' =>13 ,'sort' => 1,'status'=>'1'],
            ['id'=>'15','label' => 'List','link' => 'admin/catalog/variant-attribute/list','active_link' => 'admin/catalog/variant-attribute/list','icon' => '','parent' => 13,'sort' => 2,'status'=>'1'],
			['id'=>'16','label' => 'Coupon','link' => '#','active_link' => 'admin/catalog/coupon/*','icon' => '','parent' => 6,'sort' => 4,'status'=>'1'],
			['id'=>'17','label' => 'Add','link' => 'admin/catalog/coupon/add','active_link' => 'admin/catalog/variant-attribute/add','icon' => '','parent' =>16 ,'sort' => 1,'status'=>'1'],
            ['id'=>'18','label' => 'List','link' => 'admin/catalog/coupon/list','active_link' => 'admin/catalog/variant-attribute/list','icon' => '','parent' => 16,'sort' => 2,'status'=>'1'],
			['id'=>'19','label' => 'Sales','link' => '#','active_link' => 'admin/sales/*','icon' => 'fa fa-cart-arrow-down','parent' => 0,'sort' => 5,'status'=>'1'],
			['id'=>'20','label' => 'Pending','link' => 'admin/sales/list/pending','active_link' => 'admin/sales/list/pending','icon' => '','parent' => 19,'sort' => 2,'status'=>'1'],
			['id'=>'21','label' => 'Reject','link' => 'admin/sales/list/rejected','active_link' => 'admin/sales/list/rejected','icon' => '','parent' => 19,'sort' => 3,'status'=>'1'],
			['id'=>'22','label' => 'Confirmed','link' => 'admin/sales/list/confirmed','active_link' => 'admin/sales/list/confirmed','icon' => '','parent' => 19,'sort' => 4,'status'=>'1'],
			['id'=>'23','label' => 'Shipped','link' => 'admin/sales/list/shipped','active_link' => 'admin/sales/list/shipped','icon' => '','parent' => 19,'sort' => 5,'status'=>'1'],
			['id'=>'24','label' => 'Delivered','link' => 'admin/sales/list/delivered','active_link' => 'admin/sales/list/delivered','icon' => '','parent' => 19,'sort' => 6,'status'=>'1'],
			['id'=>'25','label' => 'Testimonials','link' => '#','active_link' => 'admin/testimonial/*','icon' => 'fa fa-quote-left','parent' => 0,'sort' => 6,'status'=>'1'],
			['id'=>'26','label' => 'Add','link' => 'admin/testimonial/add','active_link' => 'admin/testimonial/add','icon' => '','parent' =>25 ,'sort' => 1,'status'=>'1'],
            ['id'=>'27','label' => 'List','link' => 'admin/testimonial/list','active_link' => 'admin/testimonial/list','icon' => '','parent' => 25,'sort' => 2,'status'=>'1'],
			['id'=>'28','label' => 'Transaction History','link' => 'admin/transaction-history','active_link' => 'admin/transaction-history','icon' => 'fa fa-rupee-sign','parent' => 0,'sort' => 7,'status'=>'1'],
			['id'=>'29','label' => 'Information','link' =>'#','active_link' => 'admin/information/*','icon' => 'fa fa-info-circle','parent' => 0,'sort' => 8,'status'=>'1'],
			['id'=>'30','label' => 'Terms & Condition','link' => 'admin/information/term-and-condition','active_link' => 'admin/information/term-and-condition','icon' => '','parent' =>29 ,'sort' => 1,'status'=>'1'],
			['id'=>'31','label' => 'Privacy Policy','link' => 'admin/information/privacy-policy','active_link' => 'admin/information/privacy-policy','icon' => '','parent' =>29 ,'sort' => 2,'status'=>'1'],
			['id'=>'32','label' => 'About Us','link' => 'admin/information/about-us','active_link' => 'admin/information/about-us','icon' => '','parent' =>29 ,'sort' => 3,'status'=>'1'],
			['id'=>'33','label' => 'Return and Refund policy','link' => 'admin/information/return-and-refund-policy','active_link' => 'admin/information/return-and-refund-policy','icon' => '','parent' =>29 ,'sort' => 4,'status'=>'1'],
			['id'=>'34','label' => 'Shipping Policy','link' => 'admin/information/shipping-policy','active_link' => 'admin/information/shipping-policy','icon' => '','parent' =>29 ,'sort' => 5,'status'=>'1'],
			['id'=>'35','label' => 'Cancellation Policy','link' => 'admin/information/cancellation-policy','active_link' => 'admin/information/cancellation-policy','icon' => '','parent' =>29 ,'sort' => 6,'status'=>'1'],
			['id'=>'36','label' => 'Sub Admin','link' => '#','active_link' => 'admin/subadmin/*','icon' => 'fa fa-user','parent' =>'0' ,'sort' => 7,'status'=>'1'],
			['id'=>'37','label' => 'Add','link' => 'admin/subadmin/add','active_link' => 'admin/subadmin/add','icon' => '','parent' =>'36' ,'sort' => 1,'status'=>'1'],
			['id'=>'38','label' => 'List','link' => 'admin/subadmin/list','active_link' => 'admin/subadmin/list','icon' => '','parent' =>'36' ,'sort' => 2,'status'=>'1'],		
			['id'=>'39','label' => 'Newsletter','link' => 'admin/newsletter/send','active_link' => 'admin/newsletter/send','icon' => 'fa fa-envelope','parent' =>'0' ,'sort' => 8,'status'=>'1'],
			['id'=>'40','label' => 'SEO','link' => '#','active_link' => 'admin/seo/*','icon' => 'fa fa-file','parent' =>'0' ,'sort' => 9,'status'=>'1'],
			['id'=>'41','label' => 'Home Page','link' => 'admin/seo/home-page','active_link' => 'admin/seo/home-page','icon' => '','parent' =>'40' ,'sort' => 1,'status'=>'1'],
			['id'=>'42','label' => 'Failed','link' => 'admin/sales/list/failed','active_link' => 'admin/sales/list/failed','icon' => '','parent' =>'19' ,'sort' => 1,'status'=>'1'],
			['id'=>'43','label' => 'Ondemand Enquiry','link' => 'admin/sales/ondemand-enquiry','active_link' => 'admin/sales/ondemand-enquiry','icon' => '','parent' =>'19' ,'sort' => 7,'status'=>'1'],
			['id'=>'44','label' => 'Settings','link' => '#','active_link' => 'admin/settings/*','icon' => 'fa fa-cogs','parent' =>'0' ,'sort' => 9,'status'=>'1'],
			['id'=>'45','label' => 'Update Price','link' => 'admin/settings/update-price','active_link' => 'admin/settings/update-price','icon' => '','parent' =>'44' ,'sort' => 1,'status'=>'1'],
			['id'=>'46','label' => 'Update Currency Price','link' => 'admin/settings/currency','active_link' => 'admin/settings/update-price','icon' => '','parent' =>'44' ,'sort' => 2,'status'=>'1'],
			['id'=>'47','label' => 'Mannual Orders','link' => '#','active_link' => 'admin/sales/mannual-orders/*','icon' => '','parent' =>'19' ,'sort' => 8,'status'=>'1'],
			['id'=>'48','label' => 'Create Orders','link' => 'admin/sales/mannual-orders/create-order','active_link' => 'admin/sales/mannual-orders/create-order','icon' => '','parent' =>'47' ,'sort' => 1,'status'=>'1'],
			['id'=>'49','label' => 'Orders List','link' => 'admin/sales/mannual-orders/orders-list','active_link' => 'admin/sales/mannual-orders/orders-list','icon' => '','parent' =>'47' ,'sort' => 2,'status'=>'1'],
		 ];
		
		\App\Models\Menu::insert($menu);
    }
}
