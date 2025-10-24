$(document).ready(function () {

    let currentAction = ""; 
    let currentId = null; 
    let currentProvinceID = null;

    const $modal = $("#popupModal");
    const $modalTitle = $("#modalTitle");
    const $modalInput = $("#modalInput");
    const $modalShippingFee = $("#modalShippingFee");
    const $modalAmountOver = $("#modelPriveOver");
    const $modalSaveBtn = $("#modalSaveBtn");

    /* ---------- Load Provinces ---------- */
    function loadProvinces() {
        $.ajax({
            url: "ajaxadmin/provinces.php",
            method: "POST",
            data: { action: "list" },
            dataType: "html",
            success: function (data) { $("#provinceList").html(data); },
            error: function () { alert("Failed to load provinces."); }
        });
    }
    loadProvinces();

    /* ---------- Toggle Cities ---------- */
    $(document).on("click", ".province-header", function (e) {
        if ($(e.target).is("button") || $(e.target).is("i")) return;

        let cityContainer = $(this).closest(".province-card").find(".city-list");
        let provinceID = $(this).data("id");

        if (cityContainer.is(":visible")) {
            cityContainer.slideUp();
        } else {
            $.ajax({
                url: "ajaxadmin/cities.php",
                method: "POST",
                data: { provinceID: provinceID },
                dataType: "html",
                success: function (data) { cityContainer.html(data).slideDown(); },
                error: function () { alert("Failed to load cities."); }
            });
        }
    });

    /* ---------- Open Modal ---------- */
    function openModal(title, name = "", shippingFee = 0, amountOver = 0, showFields = true) {
        $modalTitle.text(title);
        $modalInput.val(name);
        $modalShippingFee.val(shippingFee);
        $modalAmountOver.val(amountOver);

        if (showFields) {
            $modalShippingFee.show();
            $modalAmountOver.show();
        } else {
            $modalShippingFee.hide();
            $modalAmountOver.hide();
        }

        $modal.fadeIn();
    }

    /* ---------- Close Modal ---------- */
    $(".close").on("click", function () { $modal.hide(); });
    $(window).on("click", function (e) { if ($(e.target).is($modal)) $modal.hide(); });

    /* ---------- Add Province ---------- */
    $("#addProvinceBtn").on("click", function () {
        currentAction = "addProvince";
        currentId = null;
        openModal("Add Province", "", 0, 0, true);
    });

    /* ---------- Edit Province ---------- */
    $(document).on("click", ".editProvinceBtn", function (e) {
        e.stopPropagation();
        currentAction = "editProvince";
        currentId = $(this).data("id");
        openModal(
            "Edit Province",
            $(this).data("name"),
            $(this).data("fee"),
            $(this).data("amountover"),
            true
        );
    });

    /* ---------- Add City ---------- */
    $(document).on("click", ".addCityBtn", function (e) {
        e.stopPropagation();
        currentAction = "addCity";
        currentId = null;
        currentProvinceID = $(this).data("provinceid");
        openModal("Add City", "", 0, 0, false);
        $modalShippingFee.hide();
        $modalAmountOver.hide();
    });

    /* ---------- Edit City ---------- */
    $(document).on("click", ".editCityBtn", function () {
        currentAction = "editCity";
        currentId = $(this).data("id");
        currentProvinceID = $(this).data("provinceid");
        openModal("Edit City", $(this).data("name"), 0, 0, false);
        $modalShippingFee.hide();
        $modalAmountOver.hide();
    });

    /* ---------- Save Modal ---------- */
    $modalSaveBtn.on("click", function () {
        const name = $modalInput.val().trim();
        const fee = parseFloat($modalShippingFee.val()) || 0;
        const amountOver = parseFloat($modalAmountOver.val()) || 0;
        if (!name) return;

        switch (currentAction) {
            case "addProvince":
                $.post("ajaxadmin/saveProvince.php",
                    { provinceName: name, shippingFee: fee, amountOver: amountOver },
                    function (res) { loadProvinces(); $modal.hide(); },
                    "json"
                ).fail(function () { alert("Failed to save province."); });
                break;

            case "editProvince":
                $.post("ajaxadmin/updateProvince.php",
                    { provinceID: currentId, provinceName: name, shippingFee: fee, amountOver: amountOver },
                    function (res) { if (res.success) loadProvinces(); $modal.hide(); },
                    "json"
                ).fail(function () { alert("Update failed."); });
                break;

            case "addCity":
                $.post("ajaxadmin/saveCity.php",
                    { provinceID: currentProvinceID, cityName: name },
                    function (res) {
                        if (res.success) {
                            $(`.province-card .province-header[data-id='${currentProvinceID}']`).click();
                            $modal.hide();
                        }
                    }, "json"
                ).fail(function () { alert("Failed to add city."); });
                break;

            case "editCity":
                $.post("ajaxadmin/updateCity.php",
                    { cityID: currentId, cityName: name },
                    function (res) {
                        if (res.success) {
                            $(`.province-card .province-header[data-id='${currentProvinceID}']`).click();
                            $modal.hide();
                        }
                    }, "json"
                ).fail(function () { alert("Update failed."); });
                break;
        }
    });

    /* ---------- Toggle Province / City Active & Delivery ---------- */
    $(document).on("change", ".toggle-delivery, .toggle-active", function () {
        const provinceID = $(this).data("id");
        const field = $(this).hasClass("toggle-delivery") ? "is_deliverable" : "provinceActive";
        const value = $(this).is(":checked") ? 1 : 0;
        $.post("ajaxadmin/toggleProvince.php", { provinceID, field, value }, function (res) {
            if (res.success) loadProvinces(); else alert("Error: " + res.message);
        }, "json");
    });

    $(document).on("change", ".toggle-city-delivery, .toggle-city-active", function () {
        const cityID = $(this).data("id");
        const field = $(this).hasClass("toggle-city-delivery") ? "is_deliverable" : "cityactive";
        const value = $(this).is(":checked") ? 1 : 0;
        $.post("ajaxadmin/toggleCity.php", { cityID, field, value }, function (res) {
            if (!res.success) alert("Error: " + res.message);
        }, "json");
    });

    /* ---------- Search ---------- */
    $("#searchRegion").on("input", function () {
        const searchText = $(this).val().toLowerCase();
        $(".province-card").each(function () {
            const provinceName = $(this).find(".province-name").text().toLowerCase();
            const cityNames = $(this).find(".city-name").map(function () { return $(this).text().toLowerCase(); }).get();
            const match = provinceName.includes(searchText) || cityNames.some(name => name.includes(searchText));
            $(this).toggle(match);
        });
    });

    $('#shippingfeeBtn').click(function(){
        location.href="manageRegions.php?do=fees";
    })


        $(".shippingFeeInput").on("change", function(){
        var provinceID = $(this).data("id");
        var value = parseFloat($(this).val()) || 0;

        $.post("ajaxadmin/updateShippingFeeAmount.php", 
               { provinceID: provinceID, field: 'shippingFee', value: value }, 
               function(res){
                   if(res.success){
                       console.log("Updated shipping fee for province " + provinceID);
                   } else {
                       alert("Update failed: " + res.message);
                   }
               }, "json");
    });

    // عند تغيير أي Amount Over
    $(".amountOverInput").on("change", function(){
        var provinceID = $(this).data("id");
        var value = parseFloat($(this).val()) || 0;

        $.post("ajaxadmin/updateShippingFeeAmount.php", 
               { provinceID: provinceID, field: 'amount_over', value: value }, 
               function(res){
                   if(res.success){
                       console.log("Updated amount over for province " + provinceID);
                   } else {
                       alert("Update failed: " + res.message);
                   }
               }, "json");
    });

});
