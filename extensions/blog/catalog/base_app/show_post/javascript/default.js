function checkEmail(email) {
var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;

if (!filter.test(email)) {
return false;
}
	return true;
}

$(document).ready(function (){
		$('#commentf').submit(function(){

			retf = true;
			$('.comf').each(function(){

				if($(this).attr('name') == 'comment_email'){
					if(!checkEmail($(this).val())){
						if(retf){
							alert('Wrong Email Address');
						}
						retf = false;
					}
				}

				if($(this).val() == ''){
					//if($(this).attr('name') != 'comment_text'){
						if(retf){
							alert('All the fields are necesary');
						}
						retf = false;
					/*}else{
						if(CKEDITOR.instances.comment_text.getData() =='' || CKEDITOR.instances.comment_text.getData() == null){
							if(retf){
								alert('All the fields are necesary');
							}
						retf = false;
						}
					}*/
				}
			});
			return retf;
		});

		/*$('.makeFCK').each(function (){
			CKEDITOR.replace(this, {
				toolbar : 'Basic'
			});
		});*/

});