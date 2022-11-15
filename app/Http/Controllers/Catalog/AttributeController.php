<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Oms\GroupCategoryModel;
use App\Models\Oms\InventoryManagement\Attribute\AttributeCategoryModel;
use App\Models\Oms\InventoryManagement\Attribute\AttributeModel;
use App\Models\Oms\InventoryManagement\Attribute\AttributePresetCategoryModel;
use App\Models\Oms\InventoryManagement\Attribute\AttributePresetModel;
use App\Models\Oms\ProductGroupModel;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    const VIEW_DIR = 'Catalog.Attributes.';
    function __construct(){

    }
    public function attributeGroupsAdd(){
        return view(self::VIEW_DIR.'attribute_group_add');
    }
    public function attributeGroupsSave(Request $request){
      //   validation start
      $validated = $request->validate([
        'name' => 'required|unique:attribute_groups|max:255'
      ]);
      // validation end
      $add_new = new AttributeGroupModel();
      $add_new->name = $request->name;
      $add_new->sort_order = $request->sort_order;
      if( $add_new->save() ){
        return redirect()->back()->with('success','Attribute Group Added Successfully.');
      }

    }
    public function attributeGroupsEdit(AttributeGroupModel $row){
        return view(self::VIEW_DIR.'attribute_group_edit',compact('row'));
    }
    public function attributeGroupsUpdate(Request $request){
        //   validation start
        $validated = $request->validate([
          'name' => 'required|unique:attribute_groups|max:255'
        ]);
        // validation end
        // $add_new = AttributeGroupModel::where("");
        $add_new->name = $request->name;
        $add_new->sort_order = $request->sort_order;
        if( $add_new->save() ){
          return redirect()->back()->with('success','Attribute Group Added Successfully.');
        }

    }
    public function attributeGroups(){
        $data = AttributeGroupModel::all();
        return view(self::VIEW_DIR.'attribute_group_listing',compact('data'));
    }
    // Attribute group work end==========================================
    public function attributes(Request $request){
      $whereCluase = [];
      if($request->name) {
        $whereCluase[] = array('name', $request->name);
      }
      $attributes = AttributeModel::with(['attributeCategories.category','presets'])->where($whereCluase)->get();
        //   dd($attributes->toArray());
      return view(self::VIEW_DIR.'attribute_listing',compact('attributes'));
    }
    public function addAttribute() {
      $categories = GroupCategoryModel::all();
      return view(self::VIEW_DIR. '.addAttribute', compact('categories'));
    }
    public function saveAttribute(Request $request) {
    //   dd($request->all());
      $this->validate($request, [
        'category' => 'required',
        'name'     => 'required',
        'category' => 'required',
        'status'   => 'required'
      ]);
      $prests               = $request->prests;
      $prests_ar            = $request->prests_ar;
      $attribute_categories = $request->category;
      $preset_categories    = $request->preset_category;
    //   dd($preset_categories);
      //
      $attribute = new AttributeModel();
      $attribute->name    = $request->name;
      $attribute->name_ar = $request->name_ar;
      $attribute->status  = $request->status;
      if($attribute->save()) {
        $last_inserted_attribute_id = $attribute->id;
        foreach($attribute_categories as $k=>$cate_value){
           $new_attribute_category = new AttributeCategoryModel();
           $new_attribute_category->attribute_id = $last_inserted_attribute_id;
           $new_attribute_category->category_id  = $cate_value;
           $new_attribute_category->save();
        }
        //preset insertion start
        if( $prests ){
            foreach($prests as $key => $prest) {
                $prst = new AttributePresetModel();
                $prst->name         = $prest;
                $prst->name_ar      = $prests_ar[$key];
                $prst->attribute_id = $last_inserted_attribute_id;
                if( $prst->save() ){
                  $last_inserted_preset_id = $prst->id;
                  if( is_array($preset_categories[$key]) && count($preset_categories[$key]) > 0 ){
                    foreach( $preset_categories[$key] as $key_pc => $val_pc ){
                        $new_prese_cat = new AttributePresetCategoryModel();
                        $new_prese_cat->attribute_preset_id = $last_inserted_preset_id;
                        $new_prese_cat->category_id = $val_pc;
                        $new_prese_cat->save();
                    }
                  }
                }
            }
        }
      }
      return redirect()->back()->with('success', 'Attribute added successfully');
    }
    public function destoryPreset(Request $request) {
      $preset = AttributePresetModel::find($request->preset);
      if($preset) {
        $preset->delete();
        return response()->json([
          'status' => true
        ]);
      }else {
        return response()->json([
          'status' => false
        ]);
      }
    }

    public function updateAttribute(Request $request) {
        // dd($request->all());
      $this->validate($request, [
        'category' => 'required',
        'name' => 'required',
        'status' => 'required'
      ]);
      $prests = $request->prests ? $request->prests : [];
      $prests_ar            = $request->prests_ar;
      $attribute_categories = $request->category;
      $preset_categories    = $request->preset_category;
      $attribute_id = $request->attribute_id;
      $prestsId     = $request->prestsId;
      $attribute = AttributeModel::find($attribute_id);
      $attribute->name    = $request->name;
      $attribute->name_ar = $request->name_ar;
      $attribute->status = $request->status;
      $attribute->update();
      if($attribute_categories){
        AttributeCategoryModel::where('attribute_id',$attribute_id)->delete();
        foreach($attribute_categories as $k=>$cate_value){
           $new_attribute_category = new AttributeCategoryModel();
           $new_attribute_category->attribute_id = $attribute_id;
           $new_attribute_category->category_id  = $cate_value;
           $new_attribute_category->save();
        }
      }
      //preset insertion start
      if( $prests ){
        AttributePresetModel::where('attribute_id',$attribute_id)->delete();
        if( is_array($prestsId) && count($prestsId) > 0 ){
            AttributePresetCategoryModel::whereIn('attribute_preset_id',$prestsId)->delete();
        }
        foreach($prests as $key => $prest) {
            $prst = new AttributePresetModel();
            $prst->name         = $prest;
            $prst->name_ar      = $prests_ar[$key];
            $prst->attribute_id = $attribute_id;
            if( $prst->save() ){
              $last_inserted_preset_id = $prst->id;
              if( is_array($preset_categories[$key]) && count($preset_categories[$key]) > 0 ){
                AttributePresetCategoryModel::where("",$attribute_id)->delete();
                foreach( $preset_categories[$key] as $key_pc => $val_pc ){
                    $new_prese_cat = new AttributePresetCategoryModel();
                    $new_prese_cat->attribute_preset_id = $last_inserted_preset_id;
                    $new_prese_cat->category_id = $val_pc;
                    $new_prese_cat->save();
                }
              }
            }
        }
      }
      return redirect()->back()->with('success', 'Attribute updated successfully.');
    }
    public function editAttribute(AttributeModel $attribute) {
        // dd($attribute->toArray());
      $categories = GroupCategoryModel::all();
      $attribute = AttributeModel::with(['attributeCategories.category','presets.presetCategories.category'])->where('id',$attribute->id)->first();
    //   dd($attribute->toArray());
      return view(self::VIEW_DIR. '.editAttribute', compact('attribute','categories'));
    }
    public function assignAttributeForm($group, $cate) {
      $group = ProductGroupModel::with('attributes')->find($group);

      $attributes = AttributeModel::with('presets')->where('category_id', $cate)->get();
      // dd($group->attributes);

      return view(self::VIEW_DIR. '.AssignAttributes', compact('attributes','group'));
    }
    public function saveAssignAttribute(Request $request) {
      // dd($request->all());
      $this->validate($request, [
        'attributes.*' => 'required',
        'presets.*' => 'required',
        'preset_text.*' => 'required',
      ]);
      $attributes = $request['attributes'];
      $presets = $request['presets'];
      $preset_text = $request['preset_text'];
      $old_id = $request['old_id'];
      foreach($attributes as $k => $attribute) {
        ProductGroupAttribute::updateOrCreate(
          [
            'id' => $old_id[$k]
          ],
          [
            'group_id' => $request->group,
            'attribute_id' => $attribute,
            'attribute_preset_id' => $presets[$k],
            'text' => $preset_text[$k],
            'created_at' => date('Y-m-d')
          ]
          );
      }

      return redirect()->back()->with('success', 'Attribute assigned successfully.');
    }

    public function destoryAttribute(Request $request) {
      $attribute = ProductGroupAttribute::find($request->id);
      if($attribute) {
        $attribute->delete();
        return response()->json([
          'status' => true
        ]);
      }else {
        return response()->json([
          'status' => false
        ]);
      }
    }

    public function fetchPresetValues($attribute) {
      $presets = AttributePresetModel::where('attribute_id', $attribute)->get();
      return response()->json([
        'status' => true,
        'presets' => $presets
      ]);
    }
    public function attributeTemplates(){
      $data = AttributeTemplateModel::all();
      return view(self::VIEW_DIR.'attribute_template_listing',compact('data'));
    }
    public function getPresetCategory(Request $request){
        // dd($request->all());
        $category_ids = $request->category_ids;
        $data = GroupCategoryModel::whereIn("id",$category_ids)->get();
        return response()->json(
            [
                'status'=>true,
                'data' => $data
            ]
        );
    }
}
