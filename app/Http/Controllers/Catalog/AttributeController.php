<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Oms\GroupCategoryModel;
use App\Models\Oms\InventoryManagement\Attribute\AttributeModel;
use App\Models\Oms\InventoryManagement\Attribute\AttributeGroupModel;
use App\Models\Oms\InventoryManagement\Attribute\AttributePresetModel;
use App\Models\Oms\InventoryManagement\Attribute\AttributeTemplateModel;
use App\Models\Oms\InventoryManagement\Attribute\ProductGroupAttribute;
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
      $attributes = AttributeModel::with('category')->where($whereCluase)->get();
      return view(self::VIEW_DIR.'attribute_listing',compact('attributes'));
    }
    public function addAttribute() {
      $categories = GroupCategoryModel::all();
      return view(self::VIEW_DIR. '.addAttribute', compact('categories'));
    }
    public function saveAttribute(Request $request) {
      $this->validate($request, [
        'category' => 'required',
        'name' => 'required'
      ]);
      $prests = $request->prests;
      $attribute = new AttributeModel();
      $attribute->name = $request->name;
      $attribute->category_id = $request->category;
      $attribute->status = 1;
      if($attribute->save()) {
        foreach($prests as $prest) {
          $prst = new AttributePresetModel(); 
          $prst->attribute_id = $attribute->id;
          $prst->name = $prest;
          $prst->save();
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
        'name' => 'required'
      ]);
      $prests = $request->prests ? $request->prests : [];
      $prestsOld = $request->prests_old ? $request->prests_old : [];
      $attribute = AttributeModel::find($request->attribute_id);
      $attribute->name = $request->name;
      $attribute->category_id = $request->category;
      $attribute->update();
      foreach($prests as $prest) {
        $prst = new AttributePresetModel(); 
        $prst->attribute_id = $attribute->id;
        $prst->name = $prest;
        $prst->save();
      }
      foreach($prestsOld as $id => $oprest) {
        $oprst = AttributePresetModel::find($id);
        $oprst->name = $oprest;
        $oprst->save();
      }
      return redirect()->back()->with('success', 'Attribute updated successfully.');
    }
    public function editAttribute(AttributeModel $attribute) {
      $categories = GroupCategoryModel::all();
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
}
