$(document).ready(function(){

  /*console.log('document is ready'); test if this script is running properly*/
  $("#hideLogin").click(function(){
    console.log('register show');
    $("#loginForm").hide();
    $("#registerForm").show();
  });

  $("#hideRegister").click(function(){
    console.log('login show');
    $("#loginForm").show();
    $("#registerForm").hide();
  });



});
