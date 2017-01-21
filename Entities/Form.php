<?php namespace Modules\Form\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use Translatable;

    protected $table = 'form__forms';
    public $translatedAttributes = [];
    protected $fillable = [];
}
