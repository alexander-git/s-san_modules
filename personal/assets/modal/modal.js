$(document).ready(function() {
    $(document).on('click', '.showModalButton', function() {
        var button = $(this);
        var modal = $(button.attr('data-modal'));
        var header = $(button.attr('data-modal-header'));
        var content = $(button.attr('data-modal-content'));
        var url = button.attr('data-url');
        var title = button.attr('data-title');

        if (!modal.data('bs.modal').isShown) {
            modal.modal('show');
        } 
        content.load(url);
        header.find('.modalTitle').remove();
        header.append('<h4 class="modalTitle">'+title+'</h4>');
    });

});