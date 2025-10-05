$(document).ready(function () {
    let pageUrl = window.location.href;
    let minQuantity = parseInt($("#minquantity").text());

    $("#qdec").click(function (e) {
        e.preventDefault();
        let currentVal = parseInt($("#quantity").val()) || minQuantity;
        if (currentVal > minQuantity) {
            $("#quantity").val(currentVal - 1);
        }
    });

    $("#qinc").click(function (e) {
        e.preventDefault();
        let currentVal = parseInt($("#quantity").val()) || minQuantity;
        $("#quantity").val(currentVal + 1);
    });

    // Prevent user from typing below minQuantity
    $("#quantity").on("input", function () {
        let val = parseInt($(this).val());
        if (isNaN(val) || val < minQuantity) {
            $(this).val(minQuantity);
        }
    });

    $("#btnshare").click(function (e) {
        e.preventDefault();
        navigator.clipboard.writeText(pageUrl).then(function () {
            alert("Link copied to clipboard!");
        });
    });

    // Facebook share
    $("#btn_fb_share").click(function (e) {
        e.preventDefault();
        let fbUrl = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(pageUrl);
        window.open(fbUrl, "_blank", "width=600,height=400");
    });

    // WhatsApp share
    $("#btn_whats_share").click(function (e) {
        e.preventDefault();
        let waUrl = "https://api.whatsapp.com/send?text=" + encodeURIComponent(pageUrl);
        window.open(waUrl, "_blank");
    });

    $("#showDescription").click(function () {
        $("#Description_section").addClass("active-section");
        $("#reviws_section").removeClass("active-section");

        $("#showDescription").addClass("active-tab");
        $("#showreviws").removeClass("active-tab");
    });

    $("#showreviws").click(function () {
        $("#reviws_section").addClass("active-section");
        $("#Description_section").removeClass("active-section");

        $("#showreviws").addClass("active-tab");
        $("#showDescription").removeClass("active-tab");
    });

    function loadReviews(offset, itemID) {
        $.get("ajax/load_reviews.php", {offset: offset, itemID: itemID}, function(data){
            if(data.trim() != ""){
                $("#reviews_container").append(data);
                $("#load_more").data("offset", offset + 5);
            } else {
                $("#load_more").remove();
            }
        });
    }

    // تحميل أول 5 مراجعات عند فتح الصفحة
    let itemID = $("#load_more").data("itemid");
    let offset = 0;
    loadReviews(offset, itemID);

    // زر Load More
    $("#load_more").click(function(){
        let offset = $(this).data("offset");
        loadReviews(offset, itemID);
    });


    $(document).on("click", ".itm_daitail", function(){
        let itmID = $(this).data('index');
        location.href="daitailitem.php?itemid="+itmID;
    })


    $('.btncart').click(function(e){
        e.preventDefault(); // prevent any default action

        let itmID = $(this).val(); // get button value
        let quantity = parseInt($("#quantity").val()) || 1;

        

        $.post("ajax/addtocart.php", { itemId: itmID, quantity: quantity }, function(response) {
            let res = JSON.parse(response);
            if (res.status === 'success') {
                alert('✅ ' + res.message + '\nCart count: ' + res.cart_count);
            } else {
                alert('❌ ' + res.message);
            }
        });
    });

});

