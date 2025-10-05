$(document).ready(function() {

    // 🛒 Load cart & totals initially
    loadCart();
    loadTotals();

    // ➕➖ Plus / Minus buttons
    $(document).on('click', '.qty-btn', function() {
        let row = $(this).closest('tr');
        let itemId = row.data('id');
        let input = row.find('.qty-input');
        let qty = parseInt(input.val());

        if ($(this).hasClass('plus')) qty++;
        else qty--;

        // prevent negative or zero
        if (qty < 1) qty = 1;

        $.post('ajax/cart_action.php', { 
            action: 'change', 
            itemId: itemId, 
            quantity: qty 
        }, function() {
            loadCart();
            loadTotals(); // 🔄 refresh totals after update
        });
    });

    // 🗑️ Delete item
    $(document).on('click', '.delete-btn', function() {
        let itemId = $(this).closest('tr').data('id');
        $.post('ajax/cart_action.php', { 
            action: 'delete', 
            itemId: itemId 
        }, function() {
            loadCart();
            loadTotals(); // 🔄 refresh totals after delete
        });
    });

    // 🧹 Clear all cart items
    $(document).on('click', '#btnclearall', function() {
        if (!confirm("Are you sure you want to clear the entire cart?")) return;

        $.post('ajax/cart_action.php', { action: 'clearall' }, function(response) {
            if (response.trim() === 'cleared') {
                loadCart();
                loadTotals();
            }
        });
    });


    // 🔄 Load cart table
    function loadCart() {
        $.post('ajax/cart_action.php', { action: 'display' }, function(data) {
            $('#cart-container').html(data);
        });
    }

    // 💰 Load grand total
    function loadTotals() {
        $.post('ajax/grandtotal.php', function(data) {
            $('#grand-total-container').html(data);
        });
    }

});
