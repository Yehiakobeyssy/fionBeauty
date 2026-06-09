$(document).on('click', '.btn_remove', function () {

    let itemId = $(this).data('id');
    let card = $(this).closest('.wishlist_item');

    $.ajax({
        url: '../ajax/remove_favorite.php',
        type: 'POST',
        dataType: 'json',
        data: { itemId: itemId },

        success: function (res) {

            if (res.status === 'success') {
                card.fadeOut(300, function () {
                    $(this).remove();
                });
            }

        }
    });

});
$(document).on('click', '.btn_cart', function () {

    let itemId = $(this).data('id');

    $.ajax({
        url: '../ajax/addtocart.php',
        type: 'POST',
        dataType: 'json',
        data: {
            itemId: itemId
        },

        success: function (res) {
            if (res.status === 'success') {
                alert('Added to cart successfully');
            } else {
                alert(res.message);
            }
        }
    });

});