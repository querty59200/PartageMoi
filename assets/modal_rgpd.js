const openModal = function (e){

    e.preventDefault();

    let url = '/rgpd';
    let $modal = $('#myModal');

    $.ajax({
        url: url,
        type: 'GET',
        success: function (response) {
            $modal.html(response);
            $modal.find('.modal').modal('show');
        }
    })
}

document.querySelectorAll('.js-modal_rgpd').forEach((modal) => {
    modal.addEventListener('click', openModal);
});
