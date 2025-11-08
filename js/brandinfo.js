$(document).on("click", ".btn-cart", function(e) {
    e.preventDefault();
    e.stopPropagation();
    let itmID = $(this).data('id');

    $.post("ajax/addtocart.php", { itemId: itmID }, function(response) {
        let res = JSON.parse(response);
        if (res.status === 'success') {
            if(res.cart_count > 0) $('.cartnumber').show();
            $('#numberofitems').html(res.cart_count);
            alert('✅ Successfully added to cart.')
        } else {
            alert('❌ ' + res.message);
        }
    });
});
