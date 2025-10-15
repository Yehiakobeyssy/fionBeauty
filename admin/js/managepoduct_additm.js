$(document).ready(function(){
    let discounts = [];

    // Toggle section
    $("#discountToggle").change(function(){
        if ($(this).is(":checked")) {
            $("#discountSection").slideDown();
        } else {
            $("#discountSection").slideUp();
            discounts = [];
            $("#discountTable tbody").empty();
            $("#discountData").val(''); // clear data
        }
    });

    // Add discount rule
    $("#addDiscount").click(function(e){
        e.preventDefault(); // prevent form submit when clicking Add

        const qty = parseFloat($("#qty").val());
        const disc = parseFloat($("#discount").val());

        if (isNaN(qty) || isNaN(disc)) {
            alert("Please enter both Quantity and Discount");
            return;
        }

        if (discounts.length > 0) {
            const last = discounts[discounts.length - 1];
            if (qty <= last.qty) {
                alert("Quantity must be greater than " + last.qty);
                return;
            }
            if (disc <= last.discount) {
                alert("Discount must be greater than " + last.discount);
                return;
            }
        }

        // Add new record
        discounts.push({ qty: qty, discount: disc });
        $("#discountTable tbody").append(`<tr><td>${qty}</td><td>${disc}%</td></tr>`);

        // update hidden field with JSON
        $("#discountData").val(JSON.stringify(discounts));

        // clear inputs
        $("#qty").val('');
        $("#discount").val('');
    });
});