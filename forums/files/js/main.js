function create_thread() {
    var name;
    var content;
    name = document.getElementById("thread_name").value;
    content = document.getElementById("thread_content").value;
    if (name == "" || name.length > 64) {
        $(".name").addClass("has-error");
        $(".name").addClass("has-feedback");
    } else {
        $(".name").removeClass("has-error");
        $(".name").removeClass("has-feedback");
    }
    if (content == "") {
        $(".content").addClass("has-error");
        $(".content").addClass("has-feedback");
    } else {
        $(".content").removeClass("has-error");
        $(".content").removeClass("has-feedback");
    }
    if (name == "" || content == "" || name.length > 64) {
        return false;
    }
}