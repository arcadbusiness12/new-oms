<div class="row"> <div class="block-header">
    <ul class="tabs">
        <?php if(session('role') == 'ADMIN') { 
            ?>
            <?php foreach (@$tabs as $tab) { ?>
                <?php if(Request::path() == $tab['url']) { ?>
                    <li class="active"><a href="javascript::void(0)"><?php echo $tab['name'] ?></a>
                        <?php if(isset($counter[$tab['name']])) { ?>
                        <div class="badge badge-secondary"><?php echo $counter[$tab['name']]; ?></div>
                        <?php } ?>
                    </li>
                <?php } else { ?>
                    <li><a href="<?php echo URL::to('/'.$tab['url']); ?>"><?php echo $tab['name'] ?></a>
                        <?php if(isset($counter[$tab['name']])) { ?>
                        <div class="badge badge-secondary"><?php echo $counter[$tab['name']]; ?></div>
                        <?php } ?>
                    </li>
                <?php }  ?>
            <?php } ?>
        <?php } else { 
            $accessPermissions = json_decode(session('access'),true);
            ?>
            <?php foreach (@$tabs as $tab) { ?>
                <?php if(array_key_exists($tab['url'], $accessPermissions)) { ?>
                    <?php if(Request::path() == $tab['url']) { ?>
                        <li class="active"><a href="javascript::void(0)"><?php echo $tab['name'] ?></a>
                            <?php if(isset($counter[$tab['name']])) { ?>
                            <div class="badge badge-secondary"><?php echo $counter[$tab['name']]; ?></div>
                            <?php } ?>
                        </li>
                    <?php } else { ?>
                        <li><a href="<?php echo URL::to('/'.$tab['url']); ?>"><?php echo $tab['name']; ?></a>
                            <?php if(isset($counter[$tab['name']])) { ?>
                            <div class="badge badge-secondary"><?php echo $counter[$tab['name']]; ?></div>
                            <?php } ?>
                        </li>
                    <?php }  ?>
                <?php }  ?>
            <?php } ?>
        <?php } ?>
    </ul>
</div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="card no-b">
            <div class="card-header white">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form name="filter_orders" id="filter_orders" method="get" action="<?php echo $search_form_action ?>">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <lebel class="form-label" for="order_id">Order ID</lebel>
                                            <input type="number" name="order_id" id="order_id" class="form-control" autocomplete="off" 
                                            value="<?php echo isset($old_input['order_id']) ? $old_input['order_id'] : '' ?>">
                                        </div>
                                    </div>
                                </div>
                              <!--<div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <lebel class="form-label" for="title">Product Title</lebel>
                                            <input type="text" name="title" id="title" class="form-control" autocomplete="off" 
                                            value="<?php echo isset($old_input['title']) ? $old_input['title'] : '' ?>">
                                        </div>
                                    </div>
                                </div>-->
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <lebel class="form-label" for="model">Product SKU</lebel>
                                            <input type="text" name="model" id="model" class="form-control" autocomplete="off" 
                                            value="<?php echo isset($old_input['model']) ? $old_input['model'] : '' ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <lebel class="form-label" for="model">Supplier</lebel>
                                            <select name="supplier" class="form-control">
                                                <option value=""></option>
                                                <?php foreach ($suppliers as $supplier) { ?>
                                                    <option value="<?php echo $supplier['user_id'] ?>" <?php if(isset($old_input['supplier']) && $old_input['supplier'] == $supplier['user_id']) { ?> selected="selected" <?php } ?> ><?php echo $supplier['firstname'] . "&nbsp;" . $supplier['lastname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <lebel class="form-label" for="model">Normal/Shipped</lebel>
                                            <select name="action" class="form-control">
                                                <option value="">Normal/Shipped</option>
                                                
                                                    <option value="normal" @if(isset($old_input['action']) && $old_input['action'] == 'normal') selected ='selected' @endif>Normal</option>
                                                    <option value="shipped" @if(isset($old_input['action']) && $old_input['action'] == 'shipped') selected ='selected' @endif>Shipped</option>
                                                
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 text-right">
                                    <button type="submit" id="search_filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>