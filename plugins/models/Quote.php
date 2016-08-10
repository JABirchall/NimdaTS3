<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 10/08/2016
 * Time: 01:13
 */

namespace Plugin\Models;


use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{

    protected $fillable = ['username', 'text', 'added_by'];

}