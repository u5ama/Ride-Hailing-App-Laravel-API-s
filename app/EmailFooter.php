<?php

namespace App;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class EmailFooter extends Model
{
    use Translatable;

    public $translatedAttributes = ['emb_company_contacts','emb_company_address','emb_company_name'];
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'email_footers';
}
