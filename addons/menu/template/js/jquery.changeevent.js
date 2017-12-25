$.fn.changeevent = function(j) {
    if (j) {
        var ischange = false;
        $(this).find("input,select").each(function() {
            if ($(this).attr("type") == "checkbox") {
                if ($(this).attr("data-changeevent-val") != $(this).is(":checked") + "") {
                    ischange = true
                }
            } else {
                if ($(this).attr("data-changeevent-val") != $(this).val()) {
                    ischange = true
                }
            }
        });
        return ischange
    } else {
        $(this).find("input,select").each(function() {
            if ($(this).attr("type") == "checkbox") {
                $(this).attr("data-changeevent-val", $(this).is(":checked"))
            } else {
                $(this).attr("data-changeevent-val", $(this).val())
            }
        })
    }
};