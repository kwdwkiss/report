<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/12/5
 * Time: 下午2:19
 */

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaxonomyResource;
use App\Taxonomy;

class TaxonomyController extends Controller
{
    public function allData()
    {
        return ['data' => Taxonomy::allData()];
    }

    public function allDisplay()
    {
        return ['data' => Taxonomy::allDisplay()];
    }
}