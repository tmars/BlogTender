function sendForm(form, callback) {
    $.ajax({
		url: form.attr('action'),
        type: form.attr('method'),
        data: form.serialize(),
		complete: function(data) {
			callback(data);
		}
	})
}