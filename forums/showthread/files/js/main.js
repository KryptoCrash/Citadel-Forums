function create_comment() {
    var content;
    content = document.getElementById("reply").value;
    if (content.trim() == "") {
        $(".comment_add").addClass("has-error");
        $(".comment_add").addClass("has-feedback");
    } else {
        $(".comment_add").removeClass("has-error");
        $(".comment_add").removeClass("has-feedback");
    }
    if (content.trim() == "") {
        return false;
    }
}

toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-bottom-left",
    "preventDuplicates": true,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "slideDown",
    "hideMethod": "slideUp"
};

$(function(){
    $(".like").on("click", function() {
        
        var data = $(this).parent().parent().parent().attr('data');
        var data = data.split(".;");
        var locPar = $(this).parent();
        var locBut = $(this);

        var postType = data[1];
        var postUUID = data[0];
        var value = 1;
        $.post('/forums/showthread/rate_add.php',{postType:postType,postUUID:postUUID,value:value},
        function(data) {
            if (data != "null") {
                if (data === "same") {
                    toastr["error"]("Already Liked");
                    return;
                }
                var ratings = $.parseJSON(data);
                locPar.find(".dislike").html(" "+ratings['dislikes']);
                locPar.find(".like").html(" "+ratings['likes']);
                toastr["success"]("Liked");
            } else {
                toastr["error"]("An error message.");
                console.log("null data");
            }
        });
    });
});

$(function(){
    $(".dislike").on("click", function() {
        var data = $(this).parent().parent().parent().attr('data');
        var data = data.split(".;");
        var locPar = $(this).parent();
        var locBut = $(this);

        var postType = data[1];
        var postUUID = data[0];
        var value = -1;
        $.post('/forums/showthread/rate_add.php',{postType:postType,postUUID:postUUID,value:value},
        function(data) {
            if (data != "null") {
                if (data === "same") {
                    toastr["error"]("Already Disliked");
                    return;
                }
                var ratings = $.parseJSON(data);
                locPar.find(".dislike").html(" "+ratings['dislikes']);
                locPar.find(".like").html(" "+ratings['likes']);
                toastr["success"]("Disliked");
            } else {
                toastr["error"]("An error message.");
                console.log("null data");
            }
        });
    });
});
function cursorInsert(elm) {
    $( 'input[type=button]' ).on('click', function(){
        var cursorPosStart = $(elm).prop('selectionStart');
        var cursorPosEnd = $(elm).prop('selectionEnd');
        var v = $(elm).val();
        var textBefore = v.substring(0,  cursorPosStart );
        var textAfter  = v.substring( cursorPosEnd, v.length );
        $(elm).val( textBefore+ $(this).val() +textAfter );
    });
}
console.log("test");