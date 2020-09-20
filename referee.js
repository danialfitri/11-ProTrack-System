
$("h4").click(function(){
    var el = $(this).parent().children(".goal-body");
    if(el.css("display") == "none"){
        el.css("display", "block");
        
    }else{
        el.css("display", "none");
    }
});
