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
                        minlength: 3,
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
                    email: {
                        required: true,
                        email: true
                    }
                },

                // Messages for form validation
                messages: {
                    email: {
                        required: 'Digite o seu e-mail',
                        email: 'E-mail inválido'
                    }
                },

                // Ajax form submition
                submitHandler: function (form) {
                    $(form).ajaxSubmit(
                        {
                            success: function () {
                                $("#sky-form2").addClass('submited');
                            }
                        });
                },

                // Do not change code below
                errorPlacement: function (error, element) {
                    error.insertAfter(element.parent());
                }
            });
    });

})(jQuery);
