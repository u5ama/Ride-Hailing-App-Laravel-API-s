<?php

namespace App;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class EmailBody extends Model
{
    use Translatable;

    public $translatedAttributes = ['emb_title_text_bf_name','	emb_title_text_after_name','emb_body_text_bf_button','emb_body_text_after_button'];
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'email_bodies';
}
