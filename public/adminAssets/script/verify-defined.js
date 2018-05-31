function normalErrorVerify(formId,name,msg){
	var $form = $("#" + formId);
	var $out = $form.find("[name="+name+"]").parent().parent();
	var $input = $form.find("[name="+name+"]").parent();
	var str = "<i class='form-control-feedback glyphicon glyphicon-remove'></i><small class='help-block'>"+ msg +"</small>";

	
	$out.find("i").remove();
	$out.find("small").remove();
	$out.removeClass("has-success").addClass("has-feedback has-error");
	$input.append(str);
}
function errorVerifyClick(formId,name){
	var $form = $("#" + formId);
	var $out = $form.find("[name="+name+"]").parent().parent();
	var $input = $form.find("[name="+name+"]").parent();

	$input.one("click",function() {
		$out.find("i").remove();
		$out.find("small").remove();
		$out.removeClass("has-feedback has-error");
	});
}