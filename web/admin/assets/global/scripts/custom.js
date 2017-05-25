$('.delete-resource-form').on('click', function () {

    var $form = $(document.createElement('form'));

    $form.attr({
        'action': $(this).data('path'),
        'method': 'POST'
    });

    $form.append($('<input/>', {
        type: 'hidden',
        name: '_method'
    }).val('DELETE'));

    $form.append($(this).data('crf'));

    $(this).parent().append($form);

    $form.submit();

    return false;
});
