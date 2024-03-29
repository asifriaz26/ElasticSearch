<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $connection = 'fantacydb';
    
    protected $fillable = [
        'skuid',
        'user_id',
        'shop_id',
        'item_title',
        'item_title_url',
        'item_description',
        'recipient',
        'occasion',
        'price',
        'market_price',
        'quantity',
        'quantity_sold',
        'category_id',
        'share_coupon',
        'share_discountAmount',
        'super_catid',
        'sub_catid',
        'ship_from_country',
        'currencyid',
        'countryid',
        'processing_time',
        'processing_min',
        'processing_max',
        'processing_option',
        'size_options',
        'status',
        'item_color',
        'item_color_method',
        'fav_count',
        'bm_redircturl',
        'videourrl',
        'featured',
        'comment_count',
        'report_flag',
        'cod',
        'dailydeal',
        'dealdate',
        'discount',
        'grade',
        'shelf_location',
        'purchase_price',
        'po_number',
        'ean_upc',
        'tags',
        'brand_id',
        'item_title_ar',
        'item_description_ar',
        'ship_to_uae',
        'ship_to_ksa',
        'weight_class_id',
        'import_id',
        'is_approved',
        'source',
        'image',
        'online_price',
        'mrp_price',
        'atp_enable',
        'atp_quantity',
        'atp_available_quantity',
        'min_buyable_quantity',
        'max_buyable_quantity',
        'current_active_supplier',
        'current_active_supplier_notes',
        'disable_promo',
        'deleted',
        'is_mapped',
        'mapped_by',
        'mapped_at',
        'is_migrated',
        'migrated_by',
        'migrated_at',
        'item_modified_on',
        'b2b_enabled',
        'b2b_price',
        'b2b_minimum_order_quantity',
        'family_number',
        'variation_code',
        'updated_by',
        'validated',
        'promotion_name',
        'b2c_enabled',
        'is_fbc',
        'is_fbs',
        'fbc_quantity',
        'fbc_status'

    ];

    protected $table = 'fc_items';
}
