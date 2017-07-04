function initInputMask() {
    
    if (!$().inputmask) {
        return;
    }
    
    $(".mask_phone").inputmask("mask", {
        "mask": "(99) 9999-9999"
    });

    $(".mask_cnpj").inputmask("mask", {
        "mask": "99.999.999/9999-99"
    });

    $(".mask_cep").inputmask("mask", {
        "mask": "99999-999"
    });
}