<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DressFairOms\OmsUserModel;
use App\Models\Oms\CustomDutyCommentReplyModel;
use App\Models\Oms\DoneDutyHistroryModel;
use App\Models\Oms\DutyAssignedUserModel;
use App\Models\Oms\DutyListsModel;
use App\Models\Oms\EmployeeCustomDutyCommentModel;
use App\Models\Oms\EmployeeCustomDutyFileModel;
use App\Models\Oms\EmployeeCustomeDutiesModel;
use App\Models\Oms\EmployeeDutyStatusHistoryModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\OpenCart\Products\ProductsDescriptionModel;
use App\Models\OpenCart\Products\ProductsModel;
use App\Models\OpenCart\Products\OptionModel;
use App\Models\OpenCart\Products\OptionValueModel;
use App\Models\OpenCart\Products\ProductSpecialModel;
use App\Models\OpenCart\Products\PromotionProductModel;
use App\Models\DressFairOpenCart\Products\ProductsDescriptionModel AS DFProductsDescriptionModel;
use App\Models\DressFairOpenCart\Products\ProductsModel AS DFProductsModel;
use App\Models\DressFairOpenCart\Products\ProductSpecialModel AS DFProductSpecialModel;
use App\Models\DressFairOpenCart\Products\PromotionProductModel AS DFPromotionProductModel;
use App\Models\DressFairOpenCart\Products\OptionModel AS DFPOptionModel;
use App\Models\DressFairOpenCart\Products\OptionValueModel AS DFPOptionValueModel;
use App\Models\Oms\MobileApp\HomeBannerImageModel;
use App\Models\Oms\MobileApp\HomeBannerModel;
use App\Models\OpenCart\Products\CategoryModel;
use App\Models\Oms\EmployeePerformanceModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionValueModel;
use App\Models\Oms\InventoryManagement\OmsOptions;
use App\Models\Oms\OmsLedger;
use App\Models\Oms\OmsLedgerDetail;
use App\Models\Oms\OmsNotificationModel;
use App\Models\Oms\OmsSubDutyListModel;
use App\Models\Oms\OmsUserGroupModel;
use App\Models\Oms\OmsUserModel as OmsOmsUserModel;
use App\Models\Oms\PaidAdsCampaign;
use App\Models\Oms\SmartLookModel;
use App\Models\OpenCart\Orders\OrdersModel;
use App\Models\DressFairOpenCart\Orders\OrdersModel AS DFOrders;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
// use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use DB;
use DateTime;
use DatePeriod;
use DateInterval;
use function Symfony\Component\VarDumper\Dumper\esc;

class CustomDutiesController extends Controller
{
    const VIEW_DIR = 'settings';
    const PER_PAGE = 20;
    public $image_extensions = ['jpeg','JPEG','png','PNG','gif','GIF', 'webp', 'WEBP', 'svg', 'SVG'];
    public $extensions = ['jpeg','JPEG','png','PNG','gif','GIF', 'gif', 'webp','PDF','svg', 'SVG', 'pdf', 'xlsx', 'txt', 'docx', 'pptx', 'HTML', 'php', 'CSV', 'EXE', 'exe', 'ZIP', 'XLS'];
    private $DB_BAOPENCART_DATABASE = '';
    private $DB_DFOPENCART_DATABASE = '';
    function __construct(){
      $this->DB_BAOPENCART_DATABASE = env('DB_BAOPENCART_DATABASE');
      $this->DB_DFOPENCART_DATABASE = env('DB_DFOPENCART_DATABASE');
    }
    public function customDuties(Request $request, $args = null) {
      //  dd($request->all());
      // dd($args);
       $whereClause = [];
       if($request->title) {
         $whereClause[] = ['title', 'LIKE%', $request->title];
       }
       if($request->progress) {
         // dd($whereClause);
         $whereClause[] = ['progress', $request->progress];
       }
       if($request->is_close) {
         $whereClause[] = ['is_close', $request->is_close];
       }
       if($request->date_from) {
         $whereClause[] = array('start_date', '>=', $request->date_from);
       }
       if($request->date_to) {
         $whereClause[] = ['end_date', '<=', $request->date_to];
       }
       if($request->user) {
         $whereClause[] = ['user_id', '<=', $request->user];
       }
       if($args) {
         $whereClause[] = ['assigned_by', $args]; 
       }
      //  dd($whereClause);
       $old_input = $request->all();
       $users = OmsOmsUserModel::whereNotIn('user_group_id', [1,2,4,5])->get();
       $customDuties = EmployeeCustomeDutiesModel::with('user', 'duty_list')->where('is_regular', 0)->where($whereClause)->orderBy('id','DESC')->paginate(10);
      //   dd($customDuties);
       return view(SELF::VIEW_DIR. '.custom_duties')->with(compact('customDuties', 'users', 'old_input'));
    }

    public function assignCustomDuties($redirect = null) {
      //   $users = OmsOmsUserModel::where('status', 1)->get();
      // dd($redirect);
        $user_group = OmsUserGroupModel::all();
        $dutyLists = [];  
      //   $dutyLists = DutyListsModel::where('status', 1)->where('is_custom', 1)
      //                                ->whereHas('assignedUsersDuties', function($q) {
      //                                   $q->where('duration', 0);
      //                                })->get();
      //   dd($dutyLists)
        return view(SELF::VIEW_DIR. '.custom_duty_form')->with(compact('dutyLists','user_group', 'redirect'));
     }


     public function getGroupUsers($user_group) {
      //   dd($user_group);
       $users = OmsOmsUserModel::where('user_group_id', $user_group)->get();
       return response()->json([
          'status' => true,
          'users'  => $users
       ]);
     }
     public function saveAssignCustomDuty(Request $request) {
      //   dd($request->all());
         $this->validate($request, [
            'user' => 'required|numeric',
            'duty' => 'required|numeric',
            'title' => 'required',
            // 'quantity' => 'required',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'date_event' => 'required|date',
         ]);
         $customDuty = ($request->id) ? EmployeeCustomeDutiesModel::find($request->id) : new EmployeeCustomeDutiesModel();
         $customDuty->user_id = $request->user;
         $customDuty->user_group_id = $request->user_group;
         $customDuty->duty_list_id = $request->duty;
         $customDuty->sub_duty_list_id = $request->sub_duty;
         $customDuty->title = $request->title;
         $customDuty->quantity = $request->quantity ? $request->quantity : 0;
         $customDuty->description = $request->description;
         $customDuty->start_date = $request->date_from;
         $customDuty->end_date = $request->date_to;
         $customDuty->event_date = $request->date_event;
         $customDuty->is_close = $request->is_close;
         $customDuty->is_regular = $request->irregular;
         $customDuty->assigned_by = session('user_id');
         $customDuty->created_at = date('Y-m-d H:i:s');
         
         if($customDuty->save()) {
            if($request->hasFile('file')) {
               $files = $request->file;
               // EmployeeCustomDutyFileModel::where('custom_duty_id',$customDuty->id)->delete();
               $i = 0;
                foreach($files as $file) {
                     // dd($file->originalName());
                     $filee = new EmployeeCustomDutyFileModel();
                     $filePath = Storage::putFile('public/uploads/custom_duties_file', $file);
                     $filee->custom_duty_id = $customDuty->id;
                     $filee->file = $filePath;
                     $filee->extension = $file->extension();
                     $filee->save();
                     $i++;
                }
            }
            if(!$request->id) {
               $enttity = [
                  'id' => $customDuty->id,
                  'title' => $customDuty->title,
                  'user' => $customDuty->user_id,
                  'date' => $customDuty->created_at,
              ];
               createNotification('custom_duty', $enttity, $request->user);
            }
            
         }
         if($request->redirect_action) {
            return redirect()->route('user.custom.duties', $request->redirect_action)->with('message', 'Duty assigned successfully.');
         }else{
            // dd("ok");
            if($request->irregular == 1) {
               // $redirect_page = 'assigned.custom.duties'.$request->action;
               $redirect_page = 'update.assign.custom.duties';
               
            }else {
               $redirect_page = 'custom.duties';
            }
            
            return redirect()->route($redirect_page, $request->action)->with('message', 'Duty assigned successfully.');
         }
         
         
     }

     public function editAssignCustomDuty($duty) {
      $users = OmsOmsUserModel::where('status', 1)->get();
      $user_group = OmsUserGroupModel::all();
      // $dutyLists = DutyListsModel::where('status', 1)->where('is_custom', 1)->get();
      $duty_details = EmployeeCustomeDutiesModel::with(['files'=> function($q) {
         $q->where('is_attachment', 0);
      }])->find($duty);
      $dutyLists = DutyListsModel::where('status', 1)->where('is_custom', 1)->whereHas('assignedUsersDuties', function($q) use($duty_details) {
         $q->where('user_id', $duty_details->user_id)->where('duration', 0);
      })->get();
      $extensions = $this->image_extensions;
      foreach($duty_details->files as $file) {
            $file->file = Storage::url($file->file);
      }
      return view(SELF::VIEW_DIR. '.custom_duty_form')->with(compact('users', 'dutyLists', 'duty_details','extensions','user_group'));
     }
     
     public function irregularDutyForm($action, $user_id = null) {
      // dd($action);
      $user = '';
      $whereClause = [];
      $irregularDutyLists = [];
      if($user_id) {
         $user = OmsOmsUserModel::find($user_id);
         $users = OmsOmsUserModel::where('user_group_id', $user->user_group_id)->get();
         $irregularDutyLists = DutyAssignedUserModel::with(['customDuty'=> function($q) {
            $q->where('status', 1);
         }])->where('user_id', $user_id)->where('duration', 0)->get();
      }else {
         $users = OmsOmsUserModel::all();
      }

      
      
      // $irregularDutyLists = DutyListsModel::where('status', 1)->where('is_custom', 1)->get();
      // dd($irregularDutyLists);
      // foreach($userGroups as $userGroup) {
      //    dd($userGroup);
      // }
      
      if($action && $action == 'w_developer') {
         $userGroups = OmsUserGroupModel::where('id', [18])->get();
         $directory = 'employee_performance.it_developer';
      }elseif($action == 'a_developer') {
         $userGroups = OmsUserGroupModel::where('id', [19])->get();
         $directory = 'employee_performance.it_developer';
      }elseif($action && $action == 'designer') {
         $userGroups = OmsUserGroupModel::whereIn('id', [13,14])->get();
         // dd($userGroups);
         $directory = 'employee_performance.it_developer';
      }else {
         $userGroups = OmsUserGroupModel::all();
         $directory = 'employee_performance.custom_duty'; 
      }
      return view($directory.'.irregularDutyForm')->with(compact('userGroups', 'users', 'user', 'irregularDutyLists', 'action'));
     }

     public function getUserIrregularDuties($user_id) {
      if($user_id) {
         $irregularDutyLists = DutyAssignedUserModel::with(['customDuty'=> function($q) {
            $q->where('status', 1);
         }])->where('user_id', $user_id)->where('duration', 0)->whereHas('customDuty')->get();
         // dd($irregularDutyLists);
         return response()->json([
          'status' => true,
          'duties'  => $irregularDutyLists
       ]);
      }
     }

     function getIrregularSubDuties($duty) {
      if($duty) {
         $subDuties = OmsSubDutyListModel::where('duty_list_id', $duty)->get();
         
         return response()->json([
            'status' => true,
            'duties' => $subDuties
         ]);
      }
     }

     public function getUserRegularCustomDuties($user_id) {
      if($user_id) {
         $regularDutyLists = DutyAssignedUserModel::with(['customDuty'=> function($q) {
            $q->where('status', 1)->where('is_custom', 1);
         }])->where('user_id', $user_id)->where('duration', '>', 0)->get();
         return response()->json([
          'status' => true,
          'duties'  => $regularDutyLists
       ]);
      }
     }

     public function removeCustomDutyFile(EmployeeCustomDutyFileModel $file) {
        if($file) {
           if($file->delete()) {
              return response()->json([
                 'status' => true
              ]);
           }
        }
     }

     public function destroyCustomDuty(EmployeeCustomeDutiesModel $duty, $ac, $redirect) {
      //   dd($redirect);
        if($duty->delete()) {
         EmployeeCustomDutyFileModel::where('custom_duty_id', $duty->id)->delete();
         if($ac == 1) {
            if($redirect && $redirect == 'marketer') {
               $redirection = 'update.assign.custom.duties';
            }else {
               $redirection = 'assigned.custom.duties.'.$redirect; 
            }
            return redirect()->route($redirection, $redirect)->with('message', 'Duty deleted successfully.');
         }else {
            return redirect()->route('custom.duties')->with('message', 'Duty deleted successfully.');
         }
         
        }
     }

     public function employeeCustomDuties(Request $request, $argc = null) {
      //   $autoDuty = autoAssignDuties(session('user_id'));
      // dd(array_key_exists('employee-performance/designer/custom/duties', json_decode(session('access'),true)));
      //  dd($argc);
        $old_input = '';
        $whereclause = [];
        
        $orderClause = [];
        
        $orderField = 'end_date';
         $orderaction = 'ASC';
        if(count($request->all()) > 0){
         if($request->user) {
            // $whereclause[] = array('user_id', Input::get('user'));
            array_push($whereclause, ['user_id', $request->user]);
            }
            if($request->duty) {
               array_push($whereclause, ['duty_list_id', $request->duty]);
            }
            if($request->sub_duty) {
               array_push($whereclause, ['sub_duty_list_id', $request->sub_duty]);
            }
            $orderField = 'end_date';
            $orderaction = 'ASC';
            $old_input = $request->all();
        }
        
         // dd(json_decode(session('access'),true));
        if(session('role') != 'ADMIN' && !array_key_exists('employee-performance/designer/custom/duties', json_decode(session('access'),true)) 
        && !array_key_exists('marketer/custom/duties/marketer', json_decode(session('access'),true)) && !array_key_exists('employee-performance/web/developer/custom/duties', json_decode(session('access'),true)) 
        && !array_key_exists('employee-performance/app/developer/custom/duties', json_decode(session('access'),true))) {
         // dd("ok");
         array_push($whereclause, ['user_id', session('user_id')]);
         array_push($whereclause, ['is_close', 0]);
         $orderField = 'end_date';
         $orderaction = 'ASC';
        }
        if($argc == 'marketer') {
         $orderField = 'start_date';
         $orderaction = 'DESC';

        }
        if($argc && $argc != 'marketer' && $argc != 0 && $argc != 'designer') {
         $ar = explode('_', $argc);
         $argc = $ar[0];
         $orderField = 'end_date';
         $orderaction = 'DESC';
         
      //   dd(count($ar));
         array_push($whereclause, ['user_id', session('user_id')]);
         array_push($whereclause, ['id', $argc]);
         if(count($ar) > 1) {
            $no = OmsNotificationModel::find($ar[1]);
            $no->is_viewed = 1;
            $no->update();
         }
         
        }
        if($argc && $argc == 'designer') {
         $user_groups = [13,14];
        }elseif($argc && $argc == 'w_developer') {
         $user_groups = [18];

        }elseif($argc && $argc == 'a_developer') {
         $user_groups = [19];
        }else {
         $user_groups = [8,13,14,18,19];
        }
      //   dd($whereclause);
        $not_started = [];
        $started = [];
        $in_testing = [];
        $completed = [];
        $duty_users = EmployeeCustomeDutiesModel::whereIn('user_group_id', $user_groups)->groupBy('user_id')->select('user_id')->get();
        $users = OmsOmsUserModel::whereIn('user_id', $duty_users->toArray())->get();
        $duty_lists = DutyListsModel::where('status', 1)->get();
        $sub_duty_lists = OmsSubDutyListModel::where('is_active', 1)->get();
      //   $customDuties = EmployeeCustomeDutiesModel::with('attachmentFiles')->where($whereclause)->where('is_close', 0)->get();
        $customDuties = EmployeeCustomeDutiesModel::with('attachmentFiles')->whereIn('user_group_id', $user_groups)->where($whereclause)->orderby($orderField, $orderaction)->get();
      //   dd($customDuties->toArray());
        foreach($customDuties as $duty) {
         foreach($duty->attachmentFiles as $file) {
            $file->file = Storage::url($file->file);
            }
         if($duty->progress == 0) {
            array_push($not_started, $duty);
         }
         if($duty->progress == 1) {
            array_push($started, $duty);
         }
         if($duty->progress == 2) {
            array_push($in_testing, $duty);
            $keys = array_column($in_testing, 'end_date');
		      array_multisort($keys, SORT_DESC, $in_testing);
         }
         if($duty->progress == 5) {
            array_push($completed, $duty);
            $keys = array_column($completed, 'end_date');
		      array_multisort($keys, SORT_DESC, $completed);
         }
         
        }
      $extensions = $this->image_extensions;
      if($argc && $argc == 0 && $argc != 'marketer' && $argc != 'designer' && $argc != 'w_developer' && $argc != 'a_developer') {
         $array = [
            'not_started' => $not_started,
            'started' => $started,
            'in_testing' => $in_testing,
            'completed' => $completed,
            'extensions' => $extensions
         ];
         return $array;
      }else {
         if($argc && $argc == 'w_developer' || $argc == 'a_developer') {
            $directory = 'employeePeerformance.it_developer'; 
         }else {
            $directory = 'employeePeerformance.custom_duty'; 
         }
         
         return view($directory.'.custom_duties')->with(compact('not_started','started','in_testing','completed', 'extensions','users', 'old_input', 'argc', 'duty_lists', 'sub_duty_lists'));

      }
        
     }
     
     public function changeDutyStatus(Request $request) {
      //   dd($request->status);
        $this->validate($request, [
           'status' => 'required'
        ]);
      //   if($request->duty_list_id == 25 && $request->status == 5) {
      //      $todayChat = $this->checkTodayChat();
      //      if(count($todayChat) > 0) {
      //       return response()->json([
      //                'status' => 5
      //             ]);
      //      }
      //   }
        $duty = EmployeeCustomeDutiesModel::find($request->duty_id);
        $duty_progress = $duty->progress;
        $duty->progress = $request->status;
        if(($duty_progress == 2 || $duty_progress == 5) && ($request->status == 0 || $request->status == 1)) {
         $duty->repeated = $duty->repeated + 1;
        }
        $duty->changed_by = session('user_id');
        $duty->comment = $request->comment ? $request->comment : null;
        if($duty->update()) {
           if($request->status == 5 && $duty->quantity > 0) {
            for($i = 0; $i < $duty->quantity; $i++) {
               $history = array(
                  'user' => $duty->user_id,
                  'duty_id' => $duty->duty_list_id,
                  'duty_name' => $duty->title,
                  'done_date' => date('Y-m-d'),
                  'created_at' => date('Y-m-d H:i:s'),
                  'is_regular' => $duty->is_regular
                );
                  // doneDutyHistory($history);
            }
            
           }
           if($duty->smart_look_id) { // update smart look status against duty
              $smart_look = SmartLookModel::find($duty->smart_look_id);
              if($smart_look) {
               $smart_look->progress = $request->status;
               $smart_look->update();
              }
           }
           
           $statusHistory = new EmployeeDutyStatusHistoryModel();
           $statusHistory->duty_id = $request->duty_id;
           $statusHistory->status = $request->status;
           $statusHistory->user_id = session('user_id');
           $statusHistory->created_at = date('Y-m-d');
           $statusHistory->save();
         //   if($request->detail_move) {
            $view = $this->employeeCustomDuties($request->action_by ? $request->action_by : 0);
            $not_started = $view['not_started'];
            $started = $view['started'];
            $in_testing = $view['in_testing'];
            $completed = $view['completed'];
            $extensions = $view['extensions'];
            // dd($view['in_testing']);
            if(session('user_id') != $duty->user_id) {
               $status = '';
               if($request->status == 0) {$status = '"To Do list"';}elseif($request->status == 1){$status = '"Doing list"';}elseif($request->status == 2){$status = '"Testing list"';}else{$status = '"Completed list"';}
               $enttity = [
                  'id' => $request->duty_id,
                  'title' => session('firstname'). ' '. session('lastname'). ' move your duty to '. $status,
                  'user' => $duty->user_id,
                  'date' => date('Y-m-d'),
              ];
              createNotification('custom_duty', $enttity, $duty->user_id);
            }
            $argc = $request->action_by; 
            return view('employee_performance.custom_duty.custom_duties_ajax')->with(compact('not_started','started','in_testing','completed', 'extensions', 'argc'));
            
         //   }else {
         //    return response()->json([
         //       'status' => true,
         //       'progress' => $request->status
         //    ]);
         //   }
           
        }
     }

     private function checkTodayChat() {
      $pendingChats = [];
      $paid_ads_chat = PaidAdsCampaign::with('chatResults')->where('status', 1)->where('start_date', '<=', date('Y-m-d'))->get();
            $today = date('Y-m-d');
            if(count($paid_ads_chat) > 0) {
                foreach($paid_ads_chat as $chat) {
                    $exist = false;
                    foreach($chat->chatResults as $result) {
                        if($result->date == $today) {
                            $exist = true;
                        }
                    }

                    if(!$exist) {
                        $ch = [
                            'campaign' => $chat->campaign_name
                        ];
                        array_push($pendingChats, $ch);
                    }
                    // PromotionProductPostModel::where('main_setting_id', $chat->main_setting_id)->where('') 
                }
            }

            return $pendingChats;
     }

     public function getCustomDutyDetails($duty, $action, $notification = null) {
        $details = EmployeeCustomeDutiesModel::with(['attachmentFiles.comments','statusHistories','duty_list','sub_duty_list','comments' => function($q) {
         $q->where('is_general', 0);
      }])->find($duty);
      if($details) {
         foreach($details->attachmentFiles as $file) {
            $file->file = Storage::url($file->file);
            if($file->cover == 1) {
               $details->cover = $file->file;
            }
         }
         foreach($details->comments as $comment) {
            if($comment->file) {
 
               $comment->file->file = Storage::url($comment->file->file);
          }
         }
      }
        
        $dable = $this->userViewedDuty($details->id);
        
        $image_extensions = $this->image_extensions;
        $extensions = $this->extensions;
      //   dd($details);
      if($notification) {
         return $details;
      }else {
         return view('employeePeerformance.custom_duty.dutyDetails')->with(compact('details','extensions', 'image_extensions', 'dable', 'action'));
      }
        
     }

     public function userViewedDuty($duty) {
        $duty = EmployeeCustomeDutiesModel::find($duty);
      if($duty->user_id == session('user_id')) {
         $duty->is_view = 1;
         $duty->update();
      }
      if(session('role') != 'ADMIN' && !array_key_exists('marketer/custom/duties/marketer', json_decode(session('access'),true)) && !array_key_exists('employee-performance/web/developer/custom/duties', json_decode(session('access'),true)) && date('Y-m-d') > date('Y-m-d', strtotime($duty->end_date))) {
         $dable = 'disabled';
      }else {
         $dable = '';
      }

      return $dable;
     }
     public function getAttachmentComment($file) {
        $attachment = EmployeeCustomDutyFileModel::with('comments.replies')->find($file);
        $attachment->file = Storage::url($attachment->file);
      //   dd($attachment);
      $image_extensions = $this->image_extensions;
        $extensions =  $this->extensions;
        return view('employeePeerformance.custom_duty.attachmentComments')->with(compact('attachment', 'extensions'));
     }

     public function saveDutyDescription(Request $request) {
        $c_duty = EmployeeCustomeDutiesModel::find($request->duty_id);
        $c_duty->description = $request->description;
        if($c_duty->update())
        {
           return response()->json([
              'status' => true,
              'contents' =>$request->description
           ]);
        }
     }

     public function makeCoverPhoto(EmployeeCustomDutyFileModel $file, $duty) {
        if($file) {
           EmployeeCustomDutyFileModel::where('custom_duty_id', $duty)->update(['cover' => 0]);
           $file->cover = 1;
           $file->update();

           return response()->json([
              'status' => true
           ]);
        }else {
           return response()->json([
              'status' => false
           ]);
        }
     }

     public function removeAttachment(EmployeeCustomDutyFileModel $attachment) {
      // public/uploads/custom_duties_file/qacV88lfF0PC15ALtBnb0R0wjKRTskjMVOYNpPng.png
        $commentId = $attachment->comment_id;
        $file = $attachment->file;
        if($attachment && $attachment->delete()) {
           if(Storage::exists($file)) {
             Storage::delete($file);
           }
            $comments = EmployeeCustomDutyCommentModel::where('file_id',$attachment->id)->get();
            foreach($comments as $comment) {
               $replies = CustomDutyCommentReplyModel::where('comment_id', $comment->id)->delete();
               $comment->delete();
            }
           return response()->json([
              'status' => true,
              'comment' => $commentId 
           ]);
        }else {
           return response()->json([
              'status' => false
           ]);
        }
     }
     
     public function addAttachment(Request $request) {
      // dd("Ok");
      if($request->hasFile('attachment')) {
         $comment = new EmployeeCustomDutyCommentModel();
         $comment->user_id = session('user_id');
         $comment->employee_custom_duty_id = $request->duty_id;
         if($comment->save()) {
            $filee = new EmployeeCustomDutyFileModel();

            // $file = $request->attachment;
            // $extension = $file->getClientOriginalExtension();
            // $filename = md5(uniqid(rand(), true)).'.'.$extension;
            // $file->move(public_path('uploads/'), $filename);
            // $image = $filename;
            // dd($request->file('attachment'));
            $filePath = Storage::putFile('public/uploads', $request->file('attachment'));
            $filee->custom_duty_id = $request->duty_id;
            $filee->comment_id = $comment->id;
            $filee->file = $filePath;
            $filee->cover = 0;
            $filee->created_at = date('Y-m-d H:i:s');
            $filee->extension = $request->file('attachment')->extension();
            $filee->save();
         }
         
         $filee->file = Storage::url($filee->file);
         $comment_data = array(
            'user' => ucwords(session('firstname'))." ".ucwords(session('lastname')),
            'attachment' => $filee,
            'comment' => $comment,
            'base' => url('/')
         );
         // dd($comment_data);
         $image_extensions = $this->image_extensions;
         $extensions = $this->extensions;
         return response()->json([
            'status' => true,
            'attachment_data' => $comment_data,
            'extensions' => $extensions,
            'image_extensions' => $image_extensions
         ]);
         
      }
     }

     public function saveComment(Request $request) {
        $file_id = null;
        $filee = null;
        if($request->hasFile('comment_file')) {
            $filee = new EmployeeCustomDutyFileModel();
            $filePath = Storage::putFile('public/uploads', $request->file('comment_file'));
            $filee->custom_duty_id = $request->duty_id;
            $filee->file = $filePath;
            $filee->cover = 0;
            $filee->is_attachment = 1;
            $filee->created_at = date('Y-m-d H:i:s');
            $filee->extension = $request->file('comment_file')->extension();
            $filee->save();
            $file_id = $filee->id;
        }
        $comment = new EmployeeCustomDutyCommentModel();
        $comment->user_id = session('user_id');
        $comment->employee_custom_duty_id = $request->duty_id;
        $comment->comment = $request->comment;
        $comment->file_id = $file_id;
        $comment->is_general = 0;
        $comment->created_at = date('Y-m-d H:i:s');
        if($comment->save()) {
           if($file_id) {
            $filee->file = Storage::url($filee->file);
           }
           $enttity = [
            'id' => $request->duty_id,
            'comment_id' => $comment->id,
            'title' => session('firstname'). ' '. session('lastname'). ' comment on duty '. $comment->custom_duty->title,
            'user' => session('user_id'),
            'date' => date('Y-m-d'),
        ];
        $u = (session('user_id') == $comment->custom_duty->user->user_id) ? null : $comment->custom_duty->user->user_id;
      //   createNotification('comment', $enttity, $u);
           return response()->json([
              'status' => true,
               'file' => $filee,
               'comment' => $comment,
               'user' => ucwords(session('firstname'))." ".ucwords(session('lastname')),
               'base' => url('/')
           ]);
        }
     }

     public function saveAttachmentComment(Request $request) {
      $comment = new EmployeeCustomDutyCommentModel();
      $comment->user_id = session('user_id');
      $comment->employee_custom_duty_id = $request->duty_id;
      $comment->comment = $request->comment;
      $comment->file_id = $request->file_id;
      $comment->is_general = 1;
      $comment->created_at = date('Y-m-d H:i:s');
      if($comment->save()) {
         return response()->json([
            'status' => true,
             'file' => null,
             'comment' => $comment,
             'user' => ucwords(session('firstname'))." ".ucwords(session('lastname')),
             'base' => url('/')
         ]);
      }
   }

     public function saveCommentReply(Request $request) {
      // dd($request->all());
      
   //   $u = (session('user_id') == $reply->custom_duty->user->user_id) ? null : $comment->custom_duty->user->user_id;
     
      $file_id = null;
      $filee = null;
      $reply = new CustomDutyCommentReplyModel();
      $reply->user_id = session('user_id');
      $reply->comment_id = $request->comment_id;
      $reply->reply_comment = $request->comment;
      $reply->parent_id = $request->reply_id ? $request->reply_id : null;
      $reply->created_at = date('Y-m-d H:i:s');
      if($reply->save()) {
         if($request->hasFile('comment_file')) {
            $filee = new EmployeeCustomDutyFileModel();
            $filePath = Storage::putFile('public/uploads/custom_duties_file', $request->file('comment_file'));
            $filee->custom_duty_id = $request->duty_id;
            $filee->comment_reply_id = $reply->id;
            $filee->file = $filePath;
            $filee->cover = 0;
            $filee->is_attachment = 1;
            $filee->created_at = date('Y-m-d H:i:s');
            $filee->extension = $request->file('comment_file')->extension();
            $filee->save();
            $file_id = $filee->id;
        }
        
        $reply->comment = $reply->reply_comment;
         if($file_id) {
          $filee->file = Storage::url($filee->file);
         }
         
         if($request->reply_id) {
            $rply = CustomDutyCommentReplyModel::find($request->reply_id);
         }else{
            $rply = EmployeeCustomDutyCommentModel::find($request->comment_id);
         }
         $u = (session('user_id') == $rply->user_id) ? null : $rply->user_id;
         // dd($rply);
         $enttity = [
            'id' => $request->duty_id,
            'comment_id' => $reply->id,
            'title' => session('firstname'). ' '. session('lastname'). ' reply to your comment',
            'user' => session('user_id'),
            'date' => date('Y-m-d'),
        ];
        createNotification('comment_reply', $enttity, $u);
         return response()->json([
            'status' => true,
             'file' => $filee,
             'comment' => $reply,
             'user' => ucwords(session('firstname'))." ".ucwords(session('lastname')),
             'base' => url('/')
         ]);
      }
     }

     public function updateComment(Request $request) {
        $comment = EmployeeCustomDutyCommentModel::where('id', $request->comment_id)->
                                                   where('employee_custom_duty_id', $request->duty_id)
                                                   ->first();
        $comment->comment = $request->edit_comment;
        if($comment->update()) {
           return response()->json([
              'status' => true,
              'comment' => $request->edit_comment
           ]);
        }
     }

     public function updateCommentReply(Request $request) {
        $reply = CustomDutyCommentReplyModel::find($request->reply_id);
        $reply->reply_comment = $request->edit_reply;
        if($reply->update()) {
           return response()->json([
              'status' => true,
              'reply' => $request->edit_reply
           ]);
        }
     }

     public function changeDutyActiveAction(EmployeeCustomeDutiesModel $duty, $status) {
         $duty->is_close = $status;
         if($duty->save()) {
            $enttity = [
               'id' => $duty->id,
               'title' => session('firstname'). ' '. session('lastname'). ' assinged duty to you please check.',
               'user' => $duty->user_id,
               'date' => date('Y-m-d'),
           ];
         //   if($status == 0) {
         //    createNotification('assgined_custom_duty', $enttity, $duty->user_id);
         //   } 
          
            return response()->json([
               'status' => true
            ]);
         }
     }

     public function getDutyFiles($duty,$select_file) {
      $extensions = ['PDF','pdf', 'xlsx', 'txt', 'docx', 'pptx', 'HTML', 'php', 'CSV'. 'EXE'. 'exe', 'ZIP', 'XLS','sql'];
        $files = EmployeeCustomDutyFileModel::where('custom_duty_id', $duty)->where('is_attachment', 0)->whereNotIn('extension', $extensions)->orderBy('id', 'DESC')->get();
         foreach($files as $file) {
            $file->file = Storage::url($file->file);
         }
         $file_extensions = $this->extensions;
        return view('employee_performance.custom_duty.attachmentPreviewCarousel')->with(compact('files','select_file','file_extensions'));
     }

     public function viewNewDutyDetail($notification, $duty) {
        if($duty) {
         $details = $this->getCustomDutyDetails($duty, 'nothimg', $notification);
         // dd($details);
         if(session('role') == 'ADMIN') {
            $viewer = ['is_admin_viewed' => 1];
         }else {
            $viewer = ['is_viewed' => 1];
         }
         OmsNotificationModel::where('id', $notification)->update($viewer);
         $image_extensions = $this->image_extensions;
         $extensions = $this->extensions;
         $dable = $this->userViewedDuty($duty);
         return view('employee_performance.custom_duty.singleDutyDetail')->with(compact('details','extensions', 'image_extensions','dable'));
        }
     }

     public function viewNewDutyComment($notification, $duty, $comment_id) {
      if($duty) {
       $details = $this->getCustomDutyDetails($duty, 'nothimg', $notification);
      //  dd($comment);
      if(session('role') == 'ADMIN') {
         $viewer = ['is_admin_viewed' => 1];
      }elseif(array_key_exists('employee-performance/designer/custom/duties', json_decode(session('access'),true)) || array_key_exists('employee-performance/custom/duties', json_decode(session('access'),true))){
         // dd("Yse");
         $viewer = ['assigned_by_viewed' => 1];
      }else {
         $viewer = ['is_viewed' => 1];
      }
       OmsNotificationModel::where('id', $notification)->update($viewer);
       $image_extensions = $this->image_extensions;
       $extensions = $this->extensions;
      //  dd($duty);
       $dable = $this->userViewedDuty($duty);
       return view('employee_performance.custom_duty.singleDutyDetail')->with(compact('details','extensions', 'image_extensions', 'comment_id', 'dable'));
      }
   }
   public function countOfDutyComment() {
      OmsInventoryProductModel::truncate();
      ProductsModel::truncate();
      DFProductsModel::truncate();
      ProductsDescriptionModel::truncate();
      DFProductsDescriptionModel::truncate();
      OmsInventoryProductOptionModel::truncate();
      OptionModel::truncate();
      OptionValueModel::truncate();
      DFPOptionModel::truncate();
      DFPOptionValueModel::truncate();
      OmsLedger::truncate();
		OmsLedgerDetail::truncate();
      OmsOptions::truncate();
      OmsInventoryOptionValueModel::trucate();
      OrdersModel::truncate();
      DFOrders::truncate();
   }
   public function employeeCustomDutiesReport(Request $request, $user = null) {
      // dd(Input::all());
      $filterWhereClause = [];
      if($request->user_id) {
         $filterWhereClause[] = array('user_id', $request->user_id);
      }
      if($request->progress != null && ($request->progress || $request->progress == 0)) {
         $filterWhereClause[] = array('progress', $request->progress);
      }
      if($request->date_from && !$request->date_to) {
         $filterWhereClause[] = array('start_date', '>=', $request->date_from);
      }
      if($request->date_to && !$request->date_from) {
         $filterWhereClause[] = array('end_date', '<=', $request->date_to);
      }
      if($request->date_from && $request->date_to) {
         array_push($filterWhereClause, ['start_date', '>=', $request->date_from]);
         array_push($filterWhereClause, ['end_date', '<=', $request->date_to]);
      }
      // dd($filterWhereClause);
      $old_input = $request->all();
      $whereclause = [];
      if(session('role') != 'ADMIN' && !array_key_exists('employee-performance/designer/custom/duties', json_decode(session('access'),true))) {
         $whereclause[] = array('user_id', session('user_id'));
        }
      $reports = EmployeeCustomeDutiesModel::with('user', 'statusHistories.user')->where($whereclause)->where($filterWhereClause)->orderBy('id', 'DESC')->paginate(20);
      // dd($data);
      $users = OmsOmsUserModel::select('user_id', 'username')->whereIn('user_group_id', [13,14])->where('status',1)->get();
      return view('employeePeerformance.custom_duty.dutiesReport')->with(compact('reports', 'users', 'old_input'));
   }

   public function DutySalaryReports() {
      $flag = false;
      $user = null;
      $date_flag = false;
      $filterWhereClause = [];
      $customDWhereClause = [];
      $performanceWhereClause = [];
      $postsWhereClause = [];

      // dd(Input::all());
      if(Input::get('user_id')) {
         $flag = true;
         $filterWhereClause[] = array('user_id', Input::get('user_id'));
      }
      
      if(Input::get('date_from') && !Input::get('date_to')) {
         $flag = true;
         $date_flag = true;
         $filterWhereClause[] = array('done_date', '>=', Input::get('date_from'));
      }
      if(Input::get('date_to') && !Input::get('date_from')) {
         $flag = true;
         $date_flag = true;
         $filterWhereClause[] = array('done_date', '<=', Input::get('date_to'));
      }
      if(Input::get('date_from') && Input::get('date_to')) {
         $flag = true;
         $date_flag = true;
         $startd = Input::get('date_from');
         $endd = Input::get('date_to');

         // array_push($filterWhereClause, ['done_date', '>=', Input::get('date_from')]);
         // array_push($filterWhereClause, ['done_date', '<=', Input::get('date_to')]);
        
         array_push($customDWhereClause, ['start_date', '>=', Input::get('date_from')]);
         array_push($customDWhereClause, ['end_date', '<=', Input::get('date_to')]);

         array_push($performanceWhereClause, ['created_at', '>=', Input::get('date_from')]);
         array_push($performanceWhereClause, ['created_at', '<=', Input::get('date_to')]);
         
         array_push($postsWhereClause, ['done_date', '>=', Input::get('date_from')]);
         array_push($postsWhereClause, ['done_date', '<=', Input::get('date_to')]);
         // array_push($postsWhereClause, ['designed', 1]);
      }
      // dd($filterWhereClause);
      $old_input = Input::all();
      $whereclause = [];
      $pWhereclause = [];
      $pstWhereclause = [];
      $done_histories = [];
      if(session('role') != 'ADMIN') {
        //  dd("!Admin");
         $ar = [];
         if($date_flag) {
            $wClause = $customDWhereClause;
            $pWClause = $performanceWhereClause;
            $pstWClause = $postsWhereClause;
           }else {
            $startd = date('Y-m-01');
            $endd = date('Y-m-t');
            array_push($whereclause, ['start_date', '>=', $startd]);
            array_push($whereclause, ['end_date', '<=', $endd]);
            array_push($pWhereclause, ['created_at', '>=', $startd]);
            array_push($pWhereclause, ['created_at', '<=', $endd]);
            array_push($pstWhereclause, ['done_date', '>=', $startd]);
            array_push($pstWhereclause, ['done_date', '<=', $endd]);
            // array_push($pstWhereclause, ['designed', 1]);
            $wClause = $whereclause;
            $pWClause = $pWhereclause;
            $pstWClause = $pstWhereclause;
           }
          //   array_push($whereclause, ['user_id', session('user_id')]);
           $flag = true;
           $user = OmsOmsUserModel::with(['assignedCustomeDuties.customDuty', 'customDuties' => function($q) use($wClause) {
            //  $q->where($wClause)->where('progress', 5);
              $q->select("employee_custom_duties.id","employee_custom_duties.user_id","employee_custom_duties.duty_list_id",DB::RAW("COUNT(*) AS achieved"))->where($wClause)->where('progress', 5)->groupBy("employee_custom_duties.duty_list_id","employee_custom_duties.user_id");
           }, 'performance_sales' => function($p) use($pWClause) {
            //  $p->where($pWClause);
            $p->select('employee_performances.duty_list_id','employee_performances.user_id',DB::RAW('SUM(employee_performances.achieved) AS achieved'),DB::RAW('SUM(employee_performances.target) AS target'))->where($pWClause)->groupBy('employee_performances.duty_list_id','employee_performances.user_id');
           },'doneDutyHistories' => function($post) use($pstWClause) {
            // $post->select('*',DB::RAW('COUNT(*) AS achieved'))->where($pstWClause)->groupBy('done_duty_histories.duty_id','done_duty_histories.user_id');
            $post->select('*',DB::RAW('COUNT(*) AS achieved'))->where($pstWClause)->groupBy('done_duty_histories.duty_id','done_duty_histories.user_id');
           }])->find(session('user_id'));
           
           array_push($ar, $user->toArray());
           $user = $ar;
        }else{
          //   dd("User");
          $ar = [];
          if($date_flag) {
              $wClause = $customDWhereClause;
              $pWClause = $performanceWhereClause;
              $pstWClause = $postsWhereClause;
          }else {
            $startd = date('Y-m-01');
            $endd = date('Y-m-t');
            array_push($whereclause, ['start_date', '>=', $startd]);
            array_push($whereclause, ['end_date', '<=', $endd]);
            array_push($pWhereclause, ['created_at', '>=', $startd]);
            array_push($pWhereclause, ['created_at', '<=', $endd]);
            array_push($pstWhereclause, ['done_date', '>=', $startd]);
            array_push($pstWhereclause, ['done_date', '<=', $endd]);
            $wClause = $whereclause;
            $pWClause = $pWhereclause;
            $pstWClause = $pstWhereclause;
          }
          // dd($pWClause);
          if(!$date_flag) {
            $startd = date('Y-m-01');
            $endd = date('Y-m-t'); 
            array_push($whereclause, ['done_date', '>=', $startd]);
            array_push($whereclause, ['done_date', '<=', $endd]);
          }
          $user = OmsOmsUserModel::with(['assignedCustomeDuties.customDuty', 'customDuties' => function($q) use($wClause) {
            $q->select("employee_custom_duties.id","employee_custom_duties.user_id","employee_custom_duties.duty_list_id",DB::RAW("COUNT(*) AS achieved"))->where($wClause)->where('progress', 5)->groupBy("employee_custom_duties.duty_list_id","employee_custom_duties.user_id");
          }, 'performance_sales' => function($p) use($pWClause) {
              $p->select('employee_performances.duty_list_id','employee_performances.user_id',DB::RAW('SUM(employee_performances.achieved) AS achieved'),DB::RAW('SUM(employee_performances.target) AS target'))->where($pWClause)->groupBy('employee_performances.duty_list_id','employee_performances.user_id');
          },'doneDutyHistories' => function($post) use($pstWClause) {
              $post->select('*',DB::RAW('COUNT(*) AS achieved'))->where($pstWClause)->groupBy('done_duty_histories.duty_id','done_duty_histories.user_id');
          }])
          ->whereHas('assignedCustomeDuties')
          ->whereNotIn('user_group_id',[18,19])
          ->where('status',1);
          if( Input::get('user_id') ){
            $user = $user->where('user_id',Input::get('user_id'));
          }
          if( Input::get('user_group_id') ){
            $user = $user->where('user_group_id',Input::get('user_group_id'));
          }
          $user = $user->get()->toArray();
      }
      $daysWithoutHoliday = $this->daysWithoutHoliday($startd,$endd);
      //   $done_histories = $this->CalculateSalary($user, $whereclause, $filterWhereClause);
      foreach($user as $key=>$usr){
         $pints = [];
         $t_points = 0;
         $performance_sales     = $usr['performance_sales'];
         $assigned_custome_duty = $usr['assigned_custome_duties'];
         if( is_array($performance_sales) && count($performance_sales) > 0 ){
           foreach( $performance_sales as $key1 => $performance_sale ){
             if( $performance_sale['duty_list_id'] == 2 ){
               $order_data = $this->saleOrders( $usr['user_id'] );
               if( $order_data ){
                 $user[$key]['performance_sales'][$key1]['achieved'] = $order_data->total_order;
                 $user[$key]['performance_sales'][$key1]['target']   = $order_data->daysWithoutHoliday;
                 $user[$key]['performance_sales'][$key1]['live_orders'] = 1; 
               }
             }
           }
         }
         if( count($assigned_custome_duty) > 0 ){
            foreach( $assigned_custome_duty as $key1 => $duty ){
                  
                  $total_points = ($duty['duration'] > 0) ? $daysWithoutHoliday/$duty['duration']*$duty['point']*$duty['quantity'] : $duty['point']*$duty['quantity'];
                  if( $duty['activity_id'] == 4 || $duty['activity_id'] == 1 || $duty['activity_id'] == 3 ){
                     $total_points  = ($duty['duration'] > 0) ? 30/$duty['duration']*$duty['point']*$duty['quantity'] : $duty['point']*$duty['quantity'];
                     // $total_points = $duty['monthly_tasks'];
                   }
                  if($duty['daily_compulsory'] == 1) {
                     // dd($total_points);
                     $t_points += $total_points;
                     array_push($pints,$total_points);
                 }
                 
               
            }
          }
          $user[$key]['t_c_points'] = $t_points;
          $user[$key]['p_t'] = $pints;
          $user[$key]['per_p_salary'] = ($user[$key]['t_c_points'] > 0) ? $user[$key]['salary']/$user[$key]['t_c_points'] : 0;
         //  dd($user);
       }
      $done_histories = $user;
      // dd($user); 
      // echo "<pre>"; print_r($done_histories); die("test");
      $users = OmsOmsUserModel::select('user_id', 'username')->whereHas('assignedCustomeDuties')->where('status',1)->orderBy('username')->get();
      $user_groups = OmsUserGroupModel::select('id','name')->where('duty_id','>',0)->orderBy('name')->get();
      return view('employee_performance.custom_duty.duty_salary_report')->with(compact('done_histories', 'users', 'old_input','daysWithoutHoliday','user_groups'));
   }

   public function saleOrders($user_id){
      $data = DB::table("oms_place_order AS opo")
      ->leftjoin($this->DB_BAOPENCART_DATABASE.".oc_order AS baord",function($join){
        $join->on("baord.order_id","=","opo.order_id");
        $join->on("opo.store","=",DB::raw("1"));
      })
      ->leftjoin($this->DB_DFOPENCART_DATABASE.".oc_order AS dford",function($join){
        $join->on("dford.order_id","=","opo.order_id");
        $join->on("opo.store","=",DB::raw("2"));
      })
      ->leftjoin("oms_orders AS ord","ord.order_id","=","opo.order_id")
      ->join("oms_user AS sp","sp.user_id","=","opo.user_id")
      // ->leftjoin("duty_assigned_users AS dau",function($join){
      //   $join->on("dau.user_id","=","sp.user_id");
      //   $join->on("dau.activity_id","=",DB::raw("2"));
      // })
      ->select(DB::raw("sp.user_id,COUNT(*) AS total_order"))
      
      // SUM(CASE WHEN ord.oms_order_status = 3 THEN 1 ELSE 0 END) AS shipped_order,
      // SUM(CASE WHEN ord.oms_order_status = 4 THEN 1 ELSE 0 END) AS delivered_order,
      // SUM(CASE WHEN ord.oms_order_status = 6 THEN 1 ELSE 0 END) AS return_order,
      // SUM(CASE WHEN baord.total >= 300 AND ord.oms_order_status = 4 THEN 1 ELSE 0 END) AS BA300Amount,
      // SUM(CASE WHEN baord.total >= 300 AND ord.oms_order_status = 4 THEN baord.total ELSE 0 END) AS BA300AmountTotal,
      // SUM(CASE WHEN dford.total >= 300 AND ord.oms_order_status = 4  THEN 1 ELSE 0 END) AS DF300Amount,
      // SUM(CASE WHEN dford.total >= 300 AND ord.oms_order_status = 4  THEN dford.total ELSE 0 END) AS DF300AmountTotal
      // ->where('sp.user_id',$user_id)
      ->where(function ($query) {
        $query->where('ord.oms_order_status','!=',5)
            ->orWhereNull('ord.oms_order_status');
      });
      $daysWithoutHoliday = 0;
      $date_from = Input::get('date_from');
      $date_to   = Input::get('date_to');
      if($date_from != "" && $date_to != "" ){
        $data = $data->whereDate('opo.created_at','>=',$date_from)
                ->whereDate('opo.created_at','<=',$date_to);
        $daysWithoutHoliday = $this->daysWithoutHoliday($date_from,$date_to);
      }
      $data = $data->where('opo.user_id','=',$user_id);
      $data = $data->groupBy("opo.user_id")->first();
      if($data){
        $data->daysWithoutHoliday = $daysWithoutHoliday;
      }
      return $data;
      // echo "<pre>"; print_r($data); die("function end");
     }
     private  function daysWithoutHoliday($date1,$date2){
      $start = new DateTime($date1);
      $end   = new DateTime($date2);
      // otherwise the  end date is excluded (bug?)
      $end->modify('+1 day');
  
      $interval = $end->diff($start);
  
      // total days
      $days = $interval->days;
  
      // create an iterateable period of date (P1D equates to 1 day)
      $period = new DatePeriod($start, new DateInterval('P1D'), $end);
  
      // best stored as array, so you can add more than one
      // $holidays = array('2012-09-07');
      $holidays = array();
  
      foreach($period as $dt) {
          $curr = $dt->format('D');
  
          // substract if Saturday or Sunday,,Sun
  
          if ($curr == 'Sun') {
              $days--;
          }
  
          // (optional) for the updated question
          elseif (in_array($dt->format('Y-m-d'), $holidays)) {
              $days--;
          }
      }
  
  
      return $days;
    }
    
   public function CalculateSalary($user, $whereclause, $filterWhereClause) {
      // dd($filterWhereClause);
      $new_array = [];
      if(is_array($user) && count($user) > 0) {
         foreach($user as $us) {
            // dd($us);
            $achieved_points = [];
            $done_duties = [];
            $per_point = $us['salary'] ? $us['salary'] : 0;
            $per_point_s = $per_point/100;
            foreach($us['assigned_custome_duties'] as $assignDuty) {
               $points = 0;
               $done_histories = DoneDutyHistroryModel::where('user_id', $us['user_id'])->where('duty_id', $assignDuty['activity_id'])->where($filterWhereClause)->get();
               if($done_histories) {
                  foreach($done_histories as $done_history) {
                     $points = $points + $done_history->points;
                     
                  }
               }else {
                  continue;
               }
               
               // dd($done_histories);
               $points = $points*$per_point_s;
               array_push($achieved_points, $points);
               if($assignDuty['custom_duty']['name'] != 'Chat Open' && $assignDuty['custom_duty']['name'] != 'Chat Close') {
                  $doneHistory = [
                     'user' => $us['firstname']. ' '. $us['lastname'],
                     'duty' => $assignDuty['custom_duty']['name'],
                     'monthly_tasks' => $assignDuty['monthly_tasks'],
                     'achieved_tasks' => count($done_histories),
                     'task_payment' => $points,
                     'point_of_job' => $assignDuty['point'],
                     'per_activity_point' => $assignDuty['per_quantity_point'],
                     'cost_total_activties' => $assignDuty['point']*$per_point_s,
                  ];
                  array_push($done_duties, $doneHistory);
               }
               
            }
            $us['achieved_salary'] = array_sum($achieved_points);
            // $us['total_achieved_salary'] = array_sum($achieved_points);
            // $us['total_achieved_salary'] = array_sum($achieved_points);
            $us['done_duties'] = $done_duties;
            array_push($new_array, $us);
         }
         // dd($new_array);
      //    $items = Collection::make($new_array);
      //   $user_array = new LengthAwarePaginator($items->forPage(null, 15), $items->count(), 15, null, ['path' => LengthAwarePaginator::resolveCurrentPath()], []);
         // dd($user_array);
      }else {
         $achieved_points = [];
         $done_duties = [];
         $per_point = $user['salary'] ? $user['salary'] : 0;
            $per_point_s = $per_point/100;
         foreach($user['assignedCustomeDuties'] as $assignDuty) {
            $points = 0;
            $done_histories = DoneDutyHistroryModel::where($whereclause)->where('duty_id', $assignDuty['activity_id'])->where($filterWhereClause)->get();
            // dd($assignDuty['customDuty']['name']);
            foreach($done_histories as $done_history) {
               $points = $points + $done_history->points;
            }
            $points = $points*$per_point_s;
            array_push($achieved_points, $points);
            $doneHistory = [
               'user' => $user['firstname']. ' '. $user['lastname'],
               'duty' => $assignDuty['customDuty']['name'],
               'monthly_tasks' => $assignDuty['monthly_tasks'],
               'achieved_tasks' => count($done_histories),
               'task_payment' => $points,
               'point_of_job' => $assignDuty['point'],
               'activity_qty' => $assignDuty['quantity'],
               'per_activity_point' => $assignDuty['per_quantity_point'],
               'cost_total_activties' => $assignDuty['point']*$per_point_s,
            ];
            array_push($done_duties, $doneHistory);
         }
         $user['achieved_salary'] = array_sum($achieved_points);
         // $assignDuty['total_salary'] = array_sum($achieved_points);
         $user['done_duties'] = $done_duties;
         array_push($new_array, $user);
         // $items = Collection::make($new_array);
         // $user_array = new LengthAwarePaginator($items->forPage(null, 5), $items->count(), 5, null, ['path' => LengthAwarePaginator::resolveCurrentPath()], []);
         // $user = $new_array;
      }
      return $new_array;
   }

   public function CheckListedProductsSku(Request $request) {
      // dd($request->all());
      $this->validate($request, [
         'sku' => 'required'
      ]);
      $skus = explode(',', $request->sku);
      $ba_flag = true;
      $df_flag = true;
      $oms_flag = true;
      $bamissing_products = [];
      $dfmissing_products = [];
      $oms_product_list = [];
      foreach($skus as $pr) {
         // checking ba listed products
         $ba_listed_product = DB::table($this->DB_BAOPENCART_DATABASE.'.oc_product')
                           ->where('sku', $pr)->first();
         
         if(!$ba_listed_product) {
            array_push($bamissing_products,$pr);
            $ba_flag = false;
         }
         // checking df listed products
         $df_listed_product = DB::table($this->DB_DFOPENCART_DATABASE.'.oc_product')
                           ->where('sku', $pr)->first();
         
         if(!$df_listed_product) {
            array_push($dfmissing_products,$pr);
            $df_flag = false;
         }

         $oms_invntry = OmsInventoryProductModel::where('sku', $pr)->where('listing', 0)->first();
         if($oms_invntry) {
            $oms_flag = false;
            array_push($oms_product_list,$pr);
         }
      }
      // dd($ba_flag);
      if($ba_flag && $df_flag && $oms_flag) {
         return response()->json([
            'status' => true
         ]);
      }else {
         return response()->json([
            'status' => false,
            'ba_flag' => $ba_flag,
            'df_flag' => $df_flag,
            'oms_flag' => $oms_flag,
            'bamissing_products' => $bamissing_products,
            'dfmissing_products' => $dfmissing_products,
            'oms_product_list'      => $oms_product_list
         ]);
      }
   }
}
