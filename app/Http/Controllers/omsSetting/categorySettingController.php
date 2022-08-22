<?php

namespace App\Http\Controllers\omsSetting;

use App\Http\Controllers\Controller;
use App\Models\Oms\GroupCategoryModel;
use App\Models\Oms\GroupSubCategoryModel;
use App\Models\Oms\ProductGroupModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class categorySettingController extends Controller
{
    const VIEW_DIR = 'oms_setting';
    const PER_PAGE = 20;

    public function __construct()
    {
        
    }

    public function categorySetting() {
        $groups = ProductGroupModel::select(DB::raw('GROUP_CONCAT(DISTINCT id) AS group_ids'))->groupBY(DB::raw("substr(name,1,2)"))->get();
        $categories = GroupCategoryModel::all();
        $subcategories = GroupSubCategoryModel::all();
        foreach($groups as $g) {
            $id = explode(",",$g->group_ids);
            $gd = ProductGroupModel::find($id[0]); // group detail
            $g['id'] = $gd->id;
            $g['product_type_id'] = $gd->product_type_id;
            $g['name'] = $gd->name;
            $g['code'] = $gd->code;
            $g['category_name'] = $gd->category_name;
            $g['category_id'] = $gd->category_id;
            $g['sub_category_id'] = $gd->sub_category_id;
            if(strpos($g->name, '-') !== false) {
                $ar = explode('-', $g->name);
                $name = $ar[0];
            }else {
                $name = $g->name;
            }
            $g['name'] = $name;
        }
        return view(self::VIEW_DIR. '.categorySetting')->with(compact('categories','subcategories','groups'));
    }

    public function saveMainCategory(Request $request) {
        $this->validate($request, [
            'cate_name.*' => 'required'
        ]);
        $group = $request->group;
        $categories = $request->cate_name;
        $sub_cate = $request->sub_cate;
        $category_id = $request->category_id;
        $code = $request->cate_code; 
        for($i = 0; $i < count($categories); $i++) {
            $request_data = array(
                'name' => $categories[$i],
                'code' => $code[$i] ? $code[$i] : null
            );
            $cate = GroupCategoryModel::updateOrCreate(
                ['id' => $category_id[$i]],
                $request_data
            );
        }

        return response()->json([
            'status' => true,
            'mesg' => 'Category saved successfully.'
        ]);
    }

    public function saveSubCategory(Request $request) {
        $this->validate($request, [
            'main_cate.*' => 'required',
            'sub_cate.*' => 'required'
        ]);
        $main_cate = $request->main_cate;
        $sub_cate = $request->sub_cate;
        $sub_category_id = $request->sub_category_id;
        $sub_cate_code = $request->sub_cate_code;
        for($i = 0; $i < count($sub_cate); $i++) {
            $request_data = array(
                'group_main_category_id' => $main_cate[$i],
                'name' => $sub_cate[$i],
                'code' => @$sub_cate_code[$i] ? $sub_cate_code[$i] : null,
            );
            $cate = GroupSubCategoryModel::updateOrCreate(
                ['id' => $sub_category_id[$i]],
                $request_data
            );
        }

        return response()->json([
            'status' => true,
            'mesg' => 'Sub category saved successfully.'
        ]);
    }
}
