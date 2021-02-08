let $modal = $('#myModal');

let $btnChangePassword = $('.js-modal');

    let url = `/changer-password/{{ utilisateur.id }}`;

    let onGetSuccess = function (response){
        // Injecte le code HTML (la réponse)
        $modal.html(response);
        // Ouvre la modal
        $modal.find('.modal').modal('show');

        let $btnModifier = $('.js-changePassword');
        let $form;

        let changePassword = function(url){
            let form_data = $form.serialize();
            $.ajax({
                url: url,
                method: 'POST',
                data: form_data,
                // Le redirectToRoute ferme la modal automatiquement
                // success: function(response){
                // $modal.find(".modal").modal('hide');
                //}
            });
        };

        // Envoi le form en POST grâce à la fonction searchAdherent au click submit
        $("form").on('submit', function(e) {
            $form = $(this)
            changePassword(url);
        });
    }

    // Au click, ouvre la modal
    $btnChangePassword.on('click', function (e){
        e.preventDefault();
        $.ajax({
            url: url,
            method: 'GET',
            success: function(response){
                // Au succes, déclenche la fonction onGetSuccess
                onGetSuccess(response)
            }
        })
    });