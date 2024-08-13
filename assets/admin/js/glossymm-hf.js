;(function($){
    $("#glossymm-template-select select").on("change",function(){
        console.log($(this).val());        
        if ($(this).val() !== "") {
            $('#glossymm-target-location-select').show();      
            $('#glossymm-target-user-select').show();         
        }else{
            $('#glossymm-target-location-select').hide();
            $('#glossymm-target-user-select').hide();
        } 
    });
   

})(jQuery)