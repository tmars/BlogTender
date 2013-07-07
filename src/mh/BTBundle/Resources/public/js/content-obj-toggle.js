function likeIt(obj) {
    var form = $(obj).parent()
    sendForm(form, function (data) {
        var response = $.parseJSON(data.responseText)
        if ('error' == response.status) {
            form.replaceWith('Ошибка: ' + response.message);
        } else if ('done' == response.status) {
            form.replaceWith('ваш лайк принят')
        }
    })
}
function complaintIt(obj) {
    var form = $(obj).parent()
    sendForm(form, function (data) {
        var response = $.parseJSON(data.responseText)
        if ('error' == response.status) {
            form.replaceWith('Ошибка: ' + response.message);
        } else if ('done' == response.status) {
            form.replaceWith('ваш жалоба принят')
        }
    })
}
function bestAnswer(obj) {
    var form = $(obj).parent()
    sendForm(form, function (data) {
        alert(data.responseText)
        var response = $.parseJSON(data.responseText)
        if ('error' == response.status) {
            form.replaceWith('Ошибка: ' + response.message);
        } else if ('done' == response.status) {
            form.replaceWith('ответ лучший!')
        }
    })
}
function commentIt(obj) {
    var form = $(obj).parent();
    //console.log(obj);
    sendForm(form, function (data) {
        //console.log(data);
        if (200 == data.status) {
            form.after(data.responseText);
            form.trigger( 'reset' );
        }
    })
}