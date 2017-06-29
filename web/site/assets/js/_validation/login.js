(function ($) {
    "use strict";

    $(function () {
        // Validation for login form
        $("#sky-form").validate(
            {
                // Rules for form validation
                rules: {
                    _username: {
                        required: true,
                        //email: true
                    },
                    _password: {
                        required: true,
                        minlength: 1,
                        maxlength: 20
                    }
                },

                // Messages for form validation
                messages: {
                    _username: {
                        required: 'Digite o seu e-mail ou cnpj',
                        //email: 'E-mail inválido'
                    },
                    _password: {
                        required: 'Digite sua senha'
                    }
                },

                // Do not change code below
                errorPlacement: function (error, element) {
                    error.insertAfter(element.parent());
                }
            });


        // Validation for recovery form
        $("#sky-form2").validate(
            {
                // Rules for form validation
                rules: {
                    'resetting_request[email]': {
                        required: true,
                        email: true
                    }
                },

                // Messages for form validation
                messages: {
                    'resetting_request[email]': {
                        required: 'Digite o seu e-mail',
                        email: 'E-mail inválido'
                    }
                },

                // Do not change code below
                errorPlacement: function (error, element) {
                    error.insertAfter(element.parent());
                }
            });

        // Validation for recovery form
        $("#sky-form3").validate(
            {
                // Rules for form validation
                rules: {
                    'resetting_reset[plainPassword][first]': {
                        required: true
                    },
                    'resetting_reset[plainPassword][second]': {
                        required: true
                    }
                },

                // Messages for form validation
                messages: {
                    'resetting_reset[plainPassword][first]': {
                        required: 'Digite uma nova senha'
                    },
                    'resetting_reset[plainPassword][second]': {
                        required: 'Digite a confirmação da nova senha'
                    }
                },

                // Do not change code below
                errorPlacement: function (error, element) {
                    error.insertAfter(element.parent());
                }
            });
    });

})(jQuery);
