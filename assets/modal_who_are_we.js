const openModal = function (e){

    e.preventDefault();

    let url = '/qui-sommes-nous';
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

document.querySelectorAll('.js-modal_who').forEach((modal) => {
    modal.addEventListener('click', openModal);
});