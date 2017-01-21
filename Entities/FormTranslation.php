<?php namespace Modules\Form\Entities;

use Illuminate\Database\Eloquent\Model;

class FormTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [];
    protected $table = 'form__form_translations';
}
