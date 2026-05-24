    $(document).on("click", ".ingredients_header", function () {
        $(this).next(".ingredients_content").slideToggle(200);
        $(this).find(".arrow").toggleClass("open");
    });

    $(document).on("click", ".add_cart_btn", function(e) {
        e.preventDefault();

        let itmID = $(this).data('id');
        let quantity = 1;

        $.post("ajax/addtocart.php", {
            itemId: itmID,
            quantity: quantity
        }, function(response) {

            let res;

            try {
                res = JSON.parse(response);
            } catch (err) {
                console.log("Invalid JSON:", response);
                alert("Server error");
                return;
            }

            if (res.status === 'success') {

                if (res.cart_count > 0) {
                    $('.cartnumber').show().text(res.cart_count);
                } else {
                    $('.cartnumber').hide();
                }

                $('#numberofitems').html(res.cart_count);

                alert('✅ Successfully added to cart.');

            } else {
                alert('❌ ' + res.message);
            }

        });
    });