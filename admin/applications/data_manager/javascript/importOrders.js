     function showHideDivs(selector){
         if ($(selector + ':visible').size() > 0){
             $(selector).hide();
         }else{
             $(selector).show();
         }
     };
     
     $(document).ready(function (){
         $('#epTabs').tabs();
     });
