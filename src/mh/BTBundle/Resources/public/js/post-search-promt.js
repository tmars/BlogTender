$(document).ready(function() {
    $("#post_tag").select2({
        width: 'element',
        minimumInputLength: 1,
        placeholder: "Поиск по сайту...",
        formatInputTooShort: function (input, min) { return "Пожалуйста введите больше " + (min - input.length) + " символа"; },
        query: function (query) {
            query.callback({results: [{id: query.term, text: query.term}]});

            $.ajax({
              url: post_search_promt_url + query.term,
              cache: false
            }).done(function( msg ) {
              var data = {results: eval(msg)}
              data.results.push({id: query.term, text: query.term})
              query.callback(data);
            });
        }
    });
});