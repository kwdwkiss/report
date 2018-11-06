<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/12/5
 * Time: 下午2:19
 */

namespace Modules\Index\Http\Controllers;

use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\TaxonomyResource;
use Modules\Common\Entities\Taxonomy;

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