<aside class="main-sidebar fixed offcanvas b-r sidebar-tabs" style="visibility: visible;" data-toggle='offcanvas'>
    <div class="sidebar">
        <div class="d-flex hv-100 align-items-stretch">
            <div class="indigo text-white">
                <div class="nav mt-5 pt-5 flex-column nav-pills" id="v-pills-tab" role="tablist"
                    aria-orientation="vertical">
                    <a class="nav-link" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab"
                    aria-controls="v-pills-home" aria-selected="true"><i class="icon-inbox2"></i></a>
                    <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab"
                    aria-controls="v-pills-profile" aria-selected="false"><i class="icon-add"></i></a>
                    <a class="nav-link blink skin_handle"  href="#"><i class="icon-lightbulb_outline"></i></a>
                    <a class="nav-link" id="v-pills-messages-tab" href="#"><i class="icon-message"></i></a>
                    <a class="nav-link" id="v-pills-settings-tab" href="#"><i class="icon-settings"></i></a>
                    <a href="">
                        <figure class="avatar">
                            <img src="assets/img/dummy/u3.png" alt="">
                            <span class="avatar-badge online"></span>
                        </figure>
                    </a>
                </div>
            </div>
            <div class="tab-content flex-grow-1 menu" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel"
                    aria-labelledby="v-pills-home-tab">
                    <div class="relative brand-wrapper sticky b-b sidebar-top-box">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <div class="text-xs-center">
                                <span class="font-weight-bold s-18" style="text-shadow: 2px -4px 5px orangered;"><?php echo session('firstname'). ' '. session('lastname') ?></span>
                            </div>
                            {{-- <div class="badge badge-danger r-0">New Panel</div> --}}
                        </div>
                    </div>
                    <ul class="sidebar-menu">
                        <li class="treeview @if(strpos(Request::url(), 'home') !== false) active @endif">
                            <a href="{{url('/home')}}">
                            <i class="icon icon-sailing-boat-water s-24"></i> <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="treeview @if( str_contains(Request::url(), '/place/order') ) active @endif"><a href="#"><i class="icon icon-shopping-cart s-24"></i>Place Order<i
                                class=" icon-angle-left  pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li class="@if( str_contains(Request::url(), '/place/order') ) active @endif"><a href="#"><i class="icon icon-circle-o"></i>Add Order</a>
                                        <ul class="treeview-menu">
                                            @forelse ( $store_share_data as $key => $store )
                                            <li class="@if( Request::url() == route('place.order',$store->id) ) active @endif"><a href="{{ route('place.order',$store->id) }}"><i class="icon icon-circle-o"></i>{{ $store->name }}</a>
                                            </li>
                                            @empty

                                            @endforelse

                                            {{--  <li class="@if( str_contains(Request::url(), '/place/order/df') ) active @endif"><a href="{{ route('place.order',2) }}"><i class="icon icon-circle-o"></i>Dress Fair</a>
                                            </li>  --}}
                                        </ul>
                                    </li>
                                </ul>
                        </li>

                        <li class="treeview @if( (str_contains(Request::url(), '/orders') && str_contains(Request::url(), '/PurchaseManagement') != 1) || ( str_contains(Request::url(), '/exchange') ) ) active @endif)">
                            <a href="#">
                                <i class="icon icon-shopping-cart s-24"></i> <span>Orders</span>
                                <i class=" icon-angle-left  pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li class="@if( str_contains(Request::url(), '/orders') && str_contains(Request::url(), '/PurchaseManagement') != 1 ) active @endif"><a href="#"><i class="icon icon-shopping-cart"></i>Normal<i
                                        class=" icon-angle-left  pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li class="@if( Request::url() == route('orders') ) active @endif"><a href="{{ route('orders') }}">All Orders </a></li>
                                        {{--  <li><a href="panel-page-blank-tabs.html">Customer Return Request </a></li>  --}}
                                        <li class="@if( Request::url() == route('orders' ) ) active @endif">
                                            <a href="{{ route('orders') }}?order_status_id=1">Pending</a>
                                        </li>
                                        <li class="@if( Request::url() == route('orders.picking-list-awaiting') ) active @endif"><a href="{{ route('orders.picking-list-awaiting') }}">Pick List </a>
                                        </li>
                                        <li class="@if( Request::url() == route('orders.pack.order') ) active @endif"><a href="{{ route('orders.pack.order') }}">Pack Orders </a>
                                        </li>
                                        <li class="@if( Request::url() == route('orders.generate.awb') ) active @endif"><a href="{{ route('orders.generate.awb') }}">Generate & Print AWB </a>
                                        </li>
                                       <li class="@if( Request::url() == route('orders.ship.order') ) active @endif"><a href="{{ route('orders.ship.order') }}">Ship Orders  </a>
                                        </li>
                                        {{--  <li><a href="panel-page-blank-tabs.html">Deliver Orders</a>  --}}
                                        </li>

                                        <li class="@if( Request::url() == route('orders.return.order') ) active @endif" ><a href="{{ route('orders.return.order') }}">Return Orders  </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="@if( str_contains(Request::url(), '/exchange') ) active @endif"><a href="#"><i class="icon icon-bug text-red"></i>Exchange<i
                                        class=" icon-angle-left  pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li class="@if( Request::url() == route('exchange') ) active @endif" ><a href="{{ route('exchange') }}">All List</a>
                                        </li>
                                        <li class="@if( Request::url() == route('exchange') ) active @endif"><a href="{{ route('exchange') }}?order_status_id=1">Pending</a>
                                        </li>
                                        {{--  <li><a href="login.html">Approve Exchange</a>
                                        </li>  --}}
                                        <li class="@if( Request::url() == route('exchange.picking.list.awaiting') ) active @endif"><a href="{{ route('exchange.picking.list.awaiting') }}">Pick List</a>
                                        </li>
                                        <li class="@if( Request::url() == route('exchange.pack') ) active @endif"><a href="{{ route('exchange.pack') }}">Pack</a>
                                        </li>
                                        <li><a href="login-2.html">Generate & Print AWB</a>
                                        </li>
                                        <li><a href="login.html">Ship Orders</a>
                                        </li>
                                        <li><a href="login-2.html">Deliver Orders</a>
                                        </li>
                                        <li><a href="login.html">Return Orders</a>
                                        </li>
                                        <li><a href="login-2.html">Airway Bill History</a>
                                        </li>
                                    </ul>
                                </li>
                                <li><a href="#"><i class="icon icon-undo"></i>Return<i
                                        class=" icon-angle-left  pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li><a href="panel-page-invoice.html">All Orders</a>
                                        </li>
                                        <li><a href="panel-page-no-posts.html">Returns Deliver Orders</a>
                                        </li>
                                        <li><a href="panel-page-no-posts.html">Airway Bill History</a>
                                        </li>
                                    </ul>
                                </li>
                                <!--  order opertaion menu start  -->
                                <li><a href="#"><i class="icon icon-developer_board"></i>Operation<i
                                    class=" icon-angle-left  pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li class="@if( Request::url() == route('orders.online') ) active @endif"><a href="{{ route('orders.online') }}">Online Orders </a></li>
                                        <li class="@if( Request::url() == route('orders.reship-orders') ) active @endif"><a href="{{ route('orders.reship-orders') }}">Reshipment Approval</a>
                                        </li>
                                        <li class="@if( Request::url() == route('orders.reship-orders') ) active @endif"><a href="{{ route('orders.reship-orders') }}">Exchange Approval</a>
                                        </li>
                                        <li><a href="panel-page-blank-tabs.html">Ready For Returns </a></li>
                                        <li class="@if( Request::url() == route('orders.awb.generated') ) active @endif"><a href="{{ route('orders.awb.generated') }}">Airway Bill History  </a>
                                        </li>
                                    </ul>
                                </li>
                                <!--  order opertaion menu end  -->
                                <li><a href="#"><i class="icon icon-fingerprint text-green"></i>Reseller Orders<i
                                    class=" icon-angle-left  pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li><a href="login.html">Pending Orders</a>
                                    </li>
                                    <li><a href="login-2.html">Reaeller Return Request</a>
                                    </li>
                                </ul>
                                </li>
                                <li><a href="#"><i class="icon icon-documents3"></i>Vouchers<i
                                    class=" icon-angle-left  pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li><a href="panel-page-invoice.html">Receipts</a>
                                    </li>
                                    <li><a href="panel-page-no-posts.html">Pending</a>
                                    </li>
                                    <li><a href="panel-page-no-posts.html">Payments</a>
                                    </li>
                                </ul>
                            </li>
                            </ul>
                        </li>

                        <li class="treeview @if( str_contains(Request::url(), 'PurchaseManagement/') && strpos(Request::url(), 'withdraw/money') === false && strpos(Request::url(), 'account/summary') === false)  active @endif">
                            <a href="#">
                                <i class="icon icon icon-shopping-bag s-24"></i>
                                Purchase Management
                                <i class=" icon-angle-left  pull-right"></i></a>
                            </a>
                            <ul class="treeview-menu">
                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/place/purchase/order') !== false || strpos(Request::url(), 'out/stock/product') !== false) active @endif">
                                    <a href="{{route('place.purchase.order')}}"><i class="icon icon-add"></i>Add
                                    Products
                                </a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/purchase/orders') !== false) active @endif">
                                    <a href="{{route('purchase.orders')}}"><i class="icon icon-circle-o"></i>All
                                    Orders</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/new/purchase/order') !== false) active @endif">
                                    <a href="{{route('new.purchase.orders')}}"><i class="icon icon-add"></i>New Order</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/awaiting/approval') !== false) active @endif">
                                    <a href="{{route('awaiting.approval.purchase.orders')}}"><i class="icon icon-pause"></i>Awaiting Approval</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/confirmed') !== false || strpos(Request::url(), 'PurchaseManagement/ship/order') !== false) active @endif">
                                    <a href="{{route('confirmed.purchase.orders')}}"><i class="icon icon-check-circle"></i>Confirm</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/get/to/be/shipped') !== false) active @endif">
                                    <a href="{{route('get.to.be.shipped')}}"><i class="icon icon-ship"></i>To Be Shipped</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/shipped/orders') !== false) active @endif">
                                    <a href="{{route('get.shipped.orders')}}"><i class="icon icon-ship"></i>Shipped</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/add/to/deliver/orders') !== false) active @endif">
                                    <a href="{{route('add.to.deliver')}}"><i class="icon icon-truck"></i>
                                        Deliver
                                    </a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/delivered/orders') !== false) active @endif">
                                    <a href="{{route('get.delivered.orders')}}"><i class="icon icon-truck"></i>Delivered</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/cancelled/orders') !== false) active @endif">
                                    <a href="{{route('get.cancelled.orders')}}"><i class="icon icon-close"></i>Cancelled</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/shipped/stock/cancelled/requests') !== false) active @endif">
                                    <a href="{{route('shipped.stock.cancelled.requests')}}"><i class="icon icon-ban"></i>Stock Cancelled (Shipped)</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/to/be/shipped/stock/cancelled/requests') !== false) active @endif">
                                    <a href="{{route('to.be.shipped.stock.cancelled.requests')}}"><i class="icon icon-ban"></i>Stock Cancelled (To Be Shipped)
                                    </a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/add/complaint') !== false) active @endif">
                                    <a href="{{route('add.complaint')}}"><i class="icon icon-announcement"></i>Add Complaint</a>
                                </li >

                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/accounts') !== false) active @endif">
                                    <a href="{{route('accounts')}}"><i class="icon icon-account_balance"></i>Accounts</a>
                                </li>
                                
                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/account/summary/report') !== false) active @endif">
                                    <a href="{{route('account.summary.report')}}"><i class="icon icon-report"></i>Account Summary Report</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'PurchaseManagement/withdraw/requests') !== false) active @endif">
                                    <a href="{{route('withdraw.request')}}"><i class="icon icon-money"></i>Withdraw Request</a>
                                </li>
                            </ul>
                        </li>

                        <li class="treeview @if(str_contains(Request::url(), 'PurchaseManagement/withdraw/money') || str_contains(Request::url(), 'PurchaseManagement/account/summary')) active @endif">
                            <a href="javascript:void(0)" class="menu-toggle waves-effect waves-block">
                               <i class="icon icon icon-payment s-24"></i>
                               <span>Payments</span>
                               <i class=" icon-angle-left  pull-right"></i>
                           </a>
                           <ul class="treeview-menu">
                             <li class="@if(strpos(Request::url(), 'PurchaseManagement/withdraw/money') !== false) active @endif">
                                 <a href="{{route('withdraw.money')}}"><i class="icon icon-money"></i>Withdraw</a>
                             </li>
                             <li class="@if(strpos(Request::url(), 'PurchaseManagement/account/summary') !== false) active @endif">
                                <a href="{{route('account.summary')}}"><i class="icon icon-money"></i>Balance</a>
                            </li>
                         </ul>
                        </li>

                        {{--  <li class="treeview @if(strpos(Request::url(), 'inventoryManagement/add/inventory') !== false || strpos(Request::url(), 'inventoryManagement/inventory/dashboard') !== false) active @endif)"><a href="#"><i class="icon icon-cubes s-24"></i>Inventory Management<i
                            class=" icon-angle-left  pull-right"></i></a>  --}}
                        <li class="treeview @if( str_contains(Request::url(), 'inventoryManagement/') ) active @endif)"><a href="#"><i class="icon icon-cubes s-24"></i>Inventory Management<i
                              class=" icon-angle-left  pull-right"></i></a>
                            <ul class="treeview-menu">
                                <li class="@if(strpos(Request::url(), 'inventoryManagement/add/inventory') !== false) active @endif"><a href="{{route('add.inventory')}}"><i class="icon icon-add"></i>Add Inventory</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'inventoryManagement/inventory/dashboard') !== false) active @endif"><a href="{{route('inventory.dashboard')}}"><i class="icon icon-dashboard"></i>Dashboard</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'inventoryManagement/inventory/stock/level') !== false) active @endif"><a href="{{route('inventory.stock.level')}}"><i class="icon icon-line-chart"></i>Stock Level</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'inventoryManagement/add/stock') !== false) active @endif" ><a href="{{route('inventory.add.stock')}}"><i class="icon icon-add"></i>Add Stock</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'inventoryManagement/stock/report') !== false) active @endif"><a href="{{route('stock.report')}}"><i class="icon icon-file-o"></i>Reports</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'inventoryManagement/inventory/alarm') !== false) active @endif"><a href="{{route('inventory.alarm')}}"><i class="icon icon-alarm"></i>Inventory Alarm</a>
                                </li>
                                <li><a href="panel-page-users.html"><i class="icon icon-settings_applications"></i>Settings</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'inventoryManagement/inventory/options') !== false) active @endif"><a href="{{route('inventory.options')}}"><i class="icon icon-server"></i>Inventory Options</a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'option/connection') !== false) active @endif"><a href="{{route('option.connection')}}"><i class="icon icon-link"></i>Options Connection</a>
                                </li>
                            </ul>
                        </li>

                        <li class="treeview @if( (str_contains(Request::url(), '/Catalog') || str_contains(Request::url(), '/productgroup')) ) active @endif)">
                            <a href="#">
                                <i class="icon icon-tags s-24"></i> <span> Catalog</span>
                                <i class=" icon-angle-left  pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li class="@if( str_contains(Request::url(), '/Catalog/attribute') || str_contains(Request::url(), '/productgroup/attribute')) active @endif"><a href="#"><i class="icon icon-shopping-cart"></i>Attributes<i
                                        class=" icon-angle-left  pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        {{-- <li class="@if( Request::url() == route('attribute.groups') ) active @endif"><a href="{{ route('attribute.groups') }}"> Attributes Group </a></li> --}}

                                        <li class="@if( Request::url() == route('attributes') ) active @endif"><a href="{{ route('attributes') }}"> Attributes </a></li>

                                        {{-- <li class="@if( Request::url() == route('attribute.templates') ) active @endif"><a href="{{ route('attribute.templates') }}"> Attributes Templates </a></li> --}}

                                    </ul>
                                </li>
                                <li class="@if(strpos(Request::url(), 'Catalog/product/listing') !== false) active @endif">
                                    <a href="{{route('product.listing')}}"><i class="icon icon-list"></i>Product Listing
                                </a>
                                </li>
                            </ul>
                        </li>

                        <li class="treeview">
                            <a href="#">
                                <i class="icon icon-flare s-24"></i> <span>Promotion</span>
                                <i class=" icon-angle-left  pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="panel-page-users.html"><i class="icon icon-pages"></i>Group Page</a>
                                </li>
                                <li><a href="#"><i class="icon icon-shopping-cart"></i>Organic Post<i
                                        class=" icon-angle-left  pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li><a href="panel-page-users.html"><i class="icon icon-more"></i>Product List</a>
                                        </li>
                                        <li>
                                            <a href="panel-page-blank-tabs.html"><i class="icon icon-tasks"></i>BA Work <i
                                                class=" icon-angle-left  pull-right"></i></a>
                                            <ul class="treeview-menu">
                                                <li><a href="panel-page-blank-tabs.html"> Facebook/Instagram </a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <a href="panel-page-blank-tabs.html"><i class="icon icon-tasks"></i>DF Work <i
                                                class=" icon-angle-left  pull-right"></i></a>
                                            <ul class="treeview-menu">
                                                <li><a href="panel-page-blank-tabs.html"> Facebook/Instagram </a>
                                                </li>
                                            </ul>
                                        </li>

                                    </ul>
                                </li>

                                <li><a href="#"><i class="icon icon-buysellads"></i>Paid Ads<i
                                        class=" icon-angle-left  pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li><a href="panel-page-users.html"><i class="icon icon-more"></i>Product List</a>
                                        </li>
                                        <li>
                                            <a href="panel-page-blank-tabs.html"> <i class="icon icon-tasks"></i>Work <i
                                                class=" icon-angle-left  pull-right"></i></a>
                                            <ul class="treeview-menu">
                                                <li><a href="panel-page-blank-tabs.html"> Facebook/Instagram </a>
                                                </li>
                                                <li><a href="panel-page-blank-tabs.html"> Facebook/Instagram </a>
                                                </li>
                                            </ul>
                                        </li>

                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="treeview"><a href="#"><i class="icon icon-all_out s-24"></i>Leavs<i
                                class=" icon-angle-left  pull-right"></i></a>
                            <ul class="treeview-menu">
                                <li><a href="panel-page-users.html"><i class="icon icon-clipboard"></i>Leaves Request</a>
                                </li>
                            </ul>
                        </li>
                        <li class="treeview @if(strpos(Request::url(), 'omsSetting/category/setting') !== false || strpos(Request::url(), '/roles') !== false || strpos(Request::url(), '/omsSetting/payment/method') !== false || strpos(Request::url(), 'omsSetting/shipping/method') !== false
                        || strpos(Request::url(), '/omsSetting/localisations/add/geo/zones') !== false || strpos(Request::url(), 'add/shipping/method') !== false || strpos(Request::url(), 'edit/shipping/method') !== false
                        || strpos(Request::url(), 'omsSetting/localisations/edit/goe/zone') !== false) active @endif"><a href="#"><i class="icon icon-settings s-24"></i>OMS Settings<i
                                class=" icon-angle-left  pull-right"></i></a>
                            <ul class="treeview-menu">
                                <li><a href="panel-page-users.html"><i class="icon icon-vcard"></i>Supplier</a>
                                </li>
                                <li><a href="panel-page-users-create.html"><i class="icon icon-user-circle-o"></i>Staff</a>
                                </li>
                                <li><a href="panel-page-profile.html"><i class="icon icon-phonelink_off"></i>Public Holiday </a>
                                </li>
                                <li><a href="panel-page-profile.html"><i class="icon icon-users"></i>User Groups </a>
                                </li>
                                <li><a href="panel-page-profile.html"><i class="icon icon-user"></i>Shipping Provider </a>
                                </li>
                                <li class="@if(strpos(Request::url(), 'roles') !== false) active @endif"><a href="#"><i class="icon icon-perm_data_setting"></i>Role And Permissions<i
                                    class=" icon-angle-left  pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li><a href="{{route('get.roles')}}">Roles</a>
                                    </li>
                                    <li><a href="panel-page-users.html">Permissions</a>
                                    </li>
                                </ul>
                            </li>

                            <li class="@if(strpos(Request::url(), 'omsSetting/payment/method') !== false || strpos(Request::url(), 'omsSetting/shipping/method') !== false || strpos(Request::url(), 'add/shipping/method') !== false || strpos(Request::url(), 'edit/shipping/method') !== false) active @endif"><a href="#"><i class="icon icon-cc"></i>Methods Management<i
                                    class=" icon-angle-left  pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li class="@if(strpos(Request::url(), 'omsSetting/payment/method') !== false) active @endif">
                                        <a href="{{route('payment.method')}}">Payment Methods</a>
                                    </li>
                                    <li class="@if(strpos(Request::url(), 'omsSetting/shipping/method') !== false || strpos(Request::url(), 'add/shipping/method') !== false || strpos(Request::url(), 'edit/shipping/method') !== false) active @endif">
                                        <a href="{{route('shipping.method')}}">Shipping Methods</a>
                                    </li>
                                </ul>
                            </li>

                            <li class="@if(strpos(Request::url(), 'omsSetting/localisations/add/geo/zones') !== false || strpos(Request::url(), 'omsSetting/shipping/method') !== false || strpos(Request::url(), 'omsSetting/localisations/edit/goe/zone') !== false) active @endif"><a href="#"><i class="icon icon-location_city"></i>Localisation<i
                                    class=" icon-angle-left  pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li class="@if(strpos(Request::url(), 'omsSetting/payment/method') !== false) active @endif">
                                        <a href="{{route('payment.method')}}">Countries</a>
                                    </li>
                                    <li class="@if(strpos(Request::url(), 'omsSetting/shipping/method') !== false) active @endif">
                                        <a href="{{route('shipping.method')}}">Cities</a>
                                    </li>
                                    <li class="@if(strpos(Request::url(), 'omsSetting/localisations/add/geo/zones') !== false || strpos(Request::url(), 'omsSetting/localisations/edit/goe/zone') !== false) active blink @endif">
                                        <a href="{{route('geo.zones')}}">Geo Zone</a>
                                    </li>
                                </ul>
                            </li>

                                <li class="@if(strpos(Request::url(), 'omsSetting/category/setting') !== false) active @endif"><a href="#"><i class="icon icon-perm_data_setting"></i>Promotion Setting<i
                                    class=" icon-angle-left  pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li><a href="panel-page-users.html">Organic Setting</a>
                                    </li>
                                    <li><a href="panel-page-users.html">Paid Setting</a>
                                    </li>
                                    <li class="@if(strpos(Request::url(), 'omsSetting/category/setting') !== false) active @endif">
                                        <a href="{{route('category.name')}}">Main Category Setting</a>
                                    </li>
                                </ul>
                            </li>
                                <li><a href="panel-page-profile.html"><i class="icon icon-attach_money"></i>Salaries </a>
                                </li>
                                <li>
                                    <a href="panel-page-blank-tabs.html"> <i class="icon icon-percent"></i>Commissions <i
                                        class=" icon-angle-left  pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li><a href="panel-page-blank-tabs.html"> Setting </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#">
                            <i class="icon icon-mobile s-24"></i>
                            <i class=" icon-angle-left  pull-right"></i>
                            <span>Mobile App Settings</span>
                        </a>
                            <ul class="treeview-menu">
                                <li>
                                    <a href="panel-page-blank-tabs.html"> <i class="icon icon-picture-o"></i>Home Banner <i
                                        class=" icon-angle-left  pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li><a href="panel-page-blank-tabs.html"> BusnissArcade </a>
                                        </li>
                                        <li><a href="panel-page-blank-tabs.html"> Dressfair </a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="panel-page-blank-tabs.html"> <i class="icon icon-themeisle"></i>Theme <i
                                        class=" icon-angle-left  pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li><a href="panel-page-blank-tabs.html"> BusnissArcade </a>
                                        </li>
                                        <li><a href="panel-page-blank-tabs.html"> Dressfair </a>
                                        </li>
                                    </ul>
                                </li>

                            </ul>
                        </li>
                        <li class="treeview ">
                            <a href="#">
                                <i class="icon icon-store_mall_directory  s-24"></i> <span>Reseller</span>
                                <i class=" icon-angle-left  pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li>
                                    <a href="panel-page-blank-tabs.html"> <i class="icon icon-users"></i>Reseller Users <i
                                        class=" icon-angle-left  pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li><a href="panel-page-blank-tabs.html"> Approved </a>
                                        </li>
                                        <li><a href="panel-page-blank-tabs.html"> Pending </a>
                                        </li>
                                        <li><a href="panel-page-blank-tabs.html"> Create New </a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="panel-page-blank-tabs.html"> <i class="icon icon-product-hunt"></i>Products <i
                                        class=" icon-angle-left  pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li><a href="panel-page-blank-tabs.html"> Product List </a>
                                        </li>
                                        <li><a href="panel-page-blank-tabs.html"> My Products </a>
                                        </li>
                                        <li><a href="panel-page-blank-tabs.html"> Create New </a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="panel-page-blank-tabs.html"> <i class="icon icon-cart-plus"></i>Sale Orders <i
                                        class=" icon-angle-left  pull-right"></i></a>
                                    <ul class="treeview-menu">
                                        <li><a href="panel-page-blank-tabs.html"> Place Order </a>
                                        </li>
                                        <li><a href="panel-page-blank-tabs.html"> All Orders </a>
                                        </li>
                                        <li><a href="panel-page-blank-tabs.html"> Return Orders </a>
                                        </li>
                                    </ul>
                                </li>

                                <li><a href="panel-page-users.html"><i class="icon icon-credit-card"></i>Earning</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                    <div class="relative brand-wrapper sticky b-b p-3">
                        <form>
                            <div class="form-group input-group-sm has-right-icon">
                                <input class="form-control form-control-sm light r-30" placeholder="Search" type="text">
                                <i class="icon-search"></i>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>

<div class="has-sidebar-left">
    <div class="pos-f-t">
    <div class="collapse" id="navbarToggleExternalContent">
        <div class="bg-dark pt-2 pb-2 pl-4 pr-2">
            <div class="search-bar">
                <input class="transparent s-24 text-white b-0 font-weight-lighter w-128 height-50" type="text"
                    placeholder="start typing...">
            </div>
            <a href="#" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-expanded="false"
            aria-label="Toggle navigation" class="paper-nav-toggle paper-nav-white active "><i></i></a>
        </div>
    </div>
</div>
</div>
<a href="#" data-toggle="push-menu" class="paper-nav-toggle left ml-2 fixed">
    <i></i>
</a>
