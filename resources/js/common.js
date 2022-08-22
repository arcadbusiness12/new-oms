$(document).ready(function() {
    console.log("Load");
    $('.my-top-class').removeClass('placeholder')
    console.log(window.location.origin+window.location.pathname);
});
// =================== Inventory ================
$(document).delegate('.input-image', 'change', function(){
   var input = $(this)[0];
   //console.log(input);
 var data_id = $(this).attr('data-id');
if (input.files && input.files[0]) {
 var reader = new FileReader();
 reader.onload = function (e) {
   //console.log(e.target.result);
   //$('#' + data_id).attr('src', e.target.result);
   $('#uploadable').attr('src', e.target.result);
 }
 //console.log(input.files[0]);
 reader.readAsDataURL(input.files[0]);
}
});
