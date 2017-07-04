$(document).ready(function () {
    customerAddressInit();
});

function customerAddressInit() {

    var $collectionHolder;

    var $addCustomerAddressLink = $('#btn_add_customerAddress');
    var $newLinkPanel = $('#panel_add_customerAddress');

    $collectionHolder = $('div#customerAddress');

    var i = 0;
    $collectionHolder.find('div.panel-body').each(function () {
        if (i > 0 || $('#customer_customerAddress_' + i + '_nomeCurso').prop('disabled') == false) {
            addCustomerAddressFormDeleteLink($(this));
        }
        i++;
    });

    var index = $collectionHolder.find('div.panel-default').length;

    $collectionHolder.data('index', index);

    /*if (index == 0) {
     addCustomerAddressForm($collectionHolder, $newLinkPanel);
     }*/

    $addCustomerAddressLink.on('click', function (e) {
        e.preventDefault();
        addCustomerAddressForm($collectionHolder, $newLinkPanel);
    });
}

function addCustomerAddressForm($collectionHolder, $newLinkPanel) {

    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');

    var newForm = prototype.replace(/__name__/g, index);
    var new_index = index + 1;

    $collectionHolder.data('index', new_index);

    var $newFormPanelBody = $('<div class="panel-body"></div>').append(newForm);
    var $newFormPanel = $('<div class="panel panel-default"></div>').append($newFormPanelBody);
    $newLinkPanel.before($newFormPanel);

    /*var count = $collectionHolder.find('div.panel-body').length;

     //if (count > 1 || typeof $('#customerAddress_0_lyCursoId').val() === 'undefined') {
     if (count > 1) {
     addCustomerAddressFormDeleteLink($newFormPanelBody, $newFormPanel);
     }*/

    formCollectionAddDelActions('#form-customer');

    $('.make-switch').bootstrapSwitch();

    addCustomerAddressFormDeleteLink($newFormPanelBody, $newFormPanel);

    return new_index;
}

function addCustomerAddressFormDeleteLink($newFormPanelBody, $newFormPanel) {
    var $removeForm = $('<div class="row"><div class="col-md-12"><div class="form-group"><a href="#" class="btn red btn-outline form-control" style="margin-top: 24px"><span class="fa fa-remove"></span> Remover Item</a></div></div></div>');
    $newFormPanelBody.append($removeForm);

    if ($newFormPanel == null) {
        $newFormPanel = $newFormPanelBody.parent('.panel');
    }

    var $collectionHolder = $('div#customerAddress');

    $removeForm.on('click', function (e) {
        e.preventDefault();
        $newFormPanel.remove();

        var index = $collectionHolder.find('div.panel-default').length;

        $collectionHolder.removeData('index');
        $collectionHolder.data('index', index);

        formCollectionAddDelActions('#form-customer');
    });
}
