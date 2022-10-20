<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Oms\InventoryManagement\Attribute\AttributeModel;
use App\Models\Oms\InventoryManagement\Attribute\AttributeGroupModel;
use App\Models\Oms\InventoryManagement\Attribute\AttributePresetModel;
use App\Models\Oms\InventoryManagement\Attribute\AttributeTemplateModel;
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
    public function attributes(){
      $data = AttributeModel::all();
      return view(self::VIEW_DIR.'attribute_listing',compact('data'));
    }
    public function attributeTemplates(){
      $data = AttributeTemplateModel::all();
      return view(self::VIEW_DIR.'attribute_template_listing',compact('data'));
    }
}
