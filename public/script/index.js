var showLogin = function(){

$("#login").show();
$("#register").hide();
$("#register_expert").hide();
}


var showRegister = function(){
$("#login").hide();
$("#register_expert").hide();
$("#register").show();
}
var showExpert=function(){
	$("#login").hide();
	$("#register_expert").show();
	$("#register").hide();
}

var putoffNav = function(){	
$("#nav_common").hide();	

}