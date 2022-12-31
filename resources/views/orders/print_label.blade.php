<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Stock Label</title>
    </head>
    <body class="<?php echo $label_type ?>">
        <?php if(isset($labels['small']) && $labels['small']) { ?>
            <?php foreach ($labels['small'] as $label) { ?>
            <page class="small">
                <section class="content small">
                    <div class="barcode-sticker">
                        <div class="barcode_box">
                            <label class="heading">BusinessArcade.com</label>
                            <div class="row">
                                <div class="barcode_image">
                                    <img src="<?php echo $label['product_image'] ?>" />
                                </div>
                                <div class="barcode_detail">
                                    <div class="row">
                                        <label>CODE</label> : <?php echo $label['product_sku'] ?>
                                    </div>
                                    <?php if($label['option']) { ?>
                                    <?php if($label['option']['size']) { ?>
                                    <div class="row">
                                        <label>Size</label> : <label><?php echo $label['option']['size'] ?></label>
                                    </div>
                                    <div class="row">
                                        <label><?php echo $label['option']['color'] ?></label>
                                    </div>
                                    <?php }else{ ?>
                                    <div class="row">
                                        <label><?php echo $label['option']['color'] ?></label>
                                    </div>
                                    <?php }?>
                                    <?php } ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="row" align="center">
                                <div class="col-xs-12">
                                    <svg class="barcode" jsbarcode-format="CODE128" jsbarcode-value="<?php echo $label['barcode'] ?>" jsbarcode-textmargin="0" jsbarcode-fontoptions="bold" jsbarcode-height="50"></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </page>
            <?php } ?>
        <?php } ?>

        <?php if(isset($labels['big']) && $labels['big']) { ?>
            <?php foreach ($labels['big'] as $key => $label) { ?>
                <page class="big">
                    <section class="content big">
                        <div class="barcode-sticker">
                            <div class="barcode_box">
                                <div class="row">
                                    <div class="title">
                                        <label class="heading"><b>BusinessArcade.com</b></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="barcode_image">
                                        <img src="<?php echo $label['product_image'] ?>" />
                                    </div>
                                    <div class="barcode_detail">
                                        <div class="row">
                                            <label>CODE</label> : <?php echo $label['product_sku'] ?>
                                        </div>
                                        <?php if($label['option']) { ?>
                                        <?php if($label['option']['size']) { ?>
                                        <div class="row">
                                            <label>Size</label> : <label><?php echo $label['option']['size'] ?></label>
                                        </div>
                                        <div class="row">
                                            <label><?php echo $label['option']['color'] ?></label>
                                        </div>
                                        <?php }else{ ?>
                                        <div class="row">
                                            <label><?php echo $label['option']['color'] ?></label>
                                        </div>
                                        <?php }?>
                                        <?php } ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="row" align="center">
                                    <div class="col-xs-12">
                                        <svg class="barcode" jsbarcode-format="CODE128" jsbarcode-value="<?php echo $label['barcode'] ?>" jsbarcode-textmargin="0" jsbarcode-fontoptions="bold" jsbarcode-height="80"></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </page>
            <?php } ?>
        <?php } ?>
        <?php if(!$labels){ ?>
            <div class="alert alert-info">No Inventory Product Found!</div>
        <?php } ?>
    </body>
</html>
<script src="{{URL::asset('/assets/js/JsBarcode.all.min.js')}}"></script>
<script type="text/javascript">
    JsBarcode(".barcode").init();
</script>
<?php if($label_type == 'small'){ ?>
<style type="text/css">
@media all{
    @page {
        size: 38mm 28mm;
        margin: 0;
        box-shadow: 0;
        box-sizing: border-box;
    }
}
</style>
<?php }else{ ?>
<style type="text/css">
@media all{
    @page {
        size: 78mm 48mm;
        margin: 0;
        box-shadow: 0;
        box-sizing: border-box;
    }
}
</style>
<?php } ?>
<style type="text/css">
@media all{
    body, page.small,
    body, page.big {
        margin: 0!important;
        box-shadow: none;
    }
    .page-break  { display: block; page-break-before: always; page-break-inside: avoid;}

    /* Small Label */
    section.content{
        margin: 0!important;
        font-family: 'Roboto', Arial, Tahoma, sans-serif;
        width: 100%;
    }
    section.content.small{
        font-family: 'Roboto', Arial, Tahoma, sans-serif;
        width: 100%!important;
        height: 100%!important;
        margin: 0;
        display: inline-block;
        border: 2px solid;
        box-sizing: border-box;
    }
    section.content .row{
        width: 100%;
        display: block;
    }
    section.content.small .barcode_box .clearfix{
        clear: both;
    }
    section.content.small .barcode_box{
        padding: 5px;
    }
    section.content.small .barcode_box .barcode{
        width: 100%!important;
        height: 33px!important;
    }
    section.content.small .barcode_box label.heading{
        font-size: 14px;
        display: block;
        text-align: center;
    }
    section.content.small .barcode_box .barcode_image{
        width: 25%;
        float: left;
    }
    section.content.small .barcode_box .barcode_detail{
        width: 70%;
        float: right;
        text-align: left;
        padding: 5px 0 0 0;
        font-size: 10px;
    }
    section.content.small .barcode_box .barcode_detail .row{
        margin-bottom: 2px;
        font-weight: bold;
    }
    section.content.small .barcode_box .barcode_image img{
        width: 100%;
        height: 35px;
        max-height: 35px;
    }
    
    /* Big Label */
    section.content.big{
        font-family: 'Roboto', Arial, Tahoma, sans-serif;
        width: 100%!important;
        height: 100%!important;
        margin: 0;
        display: inline-block;
        border: 2px solid;
        box-sizing: border-box;
    }
    section.content.big .clearfix{
        clear: both;
    }
    section.content.big .barcode_box .barcode{
        width: 100%!important;
        height: 55px!important;
    }
    section.content.big .barcode_box{
        padding: 5px;
    }
    section.content.big .barcode_box label.heading{
        font-size: 20px;
        margin-bottom: 5px;
        display: block;
        text-align: center;
    }
    section.content.big .barcode_box .barcode_image{
        width: 40%;
        float: left;
    }
    section.content.big .barcode_box .barcode_detail{
        width: 60%;
        float: right;
        text-align: left;
    }
    section.content.big .barcode_box .barcode_detail .row{
        margin-bottom: 5px;
        font-size: 17px;
        font-weight: 600;
    }
    section.content.big .barcode_box .barcode_detail .code{
        font-size: 16px;
    }
    section.content.big .barcode_box .barcode_detail .name{
        font-size: 14px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        text-overflow: ellipsis;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    section.content.big .barcode_box .barcode_image img{
        width: 100px;
        height: 75px;
    }
}
@media screen{
    section.content.small{
        width: 38mm!important;
        height: 28mm!important;
    }
    section.content.big{
        width: 78mm!important;
        height: 48mm!important;
    }
}
</style>