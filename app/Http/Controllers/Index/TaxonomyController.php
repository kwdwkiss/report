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
    public function all()
    {
        return TaxonomyResource::collection(Taxonomy::get());
    }

    public function reportTypeList()
    {
        return TaxonomyResource::collection(Taxonomy::reportType());
    }

    public function accountTypeList()
    {
        return TaxonomyResource::collection(Taxonomy::accountType());
    }

    public function accountStatusList()
    {
        return TaxonomyResource::collection(Taxonomy::accountStatus());
    }

    public function articleTypeList()
    {
        return TaxonomyResource::collection(Taxonomy::articleType());
    }

    public function userTypeList()
    {
        return TaxonomyResource::collection(Taxonomy::userType());
    }
}