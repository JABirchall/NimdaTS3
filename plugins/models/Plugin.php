<?php
/**
 * Created by PhpStorm.
 * User: Jake
 * Date: 12/08/2016
 * Time: 09:42
 */

namespace Plugin\Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends model
{
    protected $fillable = ['name', 'version',];
}