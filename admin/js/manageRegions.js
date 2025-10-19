$(document).ready(function () {

  loadProvinces();

  /* ---------- Load provinces ---------- */
  function loadProvinces() {
    $.ajax({
      url: "ajaxadmin/provinces.php",
      method: "POST",
      data: { action: "list" },
      dataType: "html",
      success: function (data) {
        $("#provinceList").html(data);
      },
      error: function () {
        alert("Failed to load provinces.");
      }
    });
  }

  /* ---------- Toggle expand provinces -> load cities ---------- */
  $(document).on("click", ".province-header", function (e) {
    // avoid triggering when clicking buttons inside header
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
        success: function (data) {
          cityContainer.html(data).slideDown();
        },
        error: function () {
          alert("Failed to load cities.");
        }
      }); 
    }
  });

  /* ---------- Modal Setup ---------- */
  let currentAction = ""; // addProvince, editProvince, addCity, editCity
  let currentId = null; // provinceID or cityID
  let currentProvinceID = null; // For city operations

  const modal = $("#popupModal");
  const modalTitle = $("#modalTitle");
  const modalInput = $("#modalInput");
  const modalSaveBtn = $("#modalSaveBtn");

  function openModal(title, value = "") {
    modalTitle.text(title);
    modalInput.val(value);
    modal.show();
    modalInput.focus();
  }

  $(".close").on("click", function () { modal.hide(); });
  $(window).on("click", function (e) { if ($(e.target).is(modal)) modal.hide(); });

  /* ---------- Add/Edit Province ---------- */
  $("#addProvinceBtn").on("click", function () {
    currentAction = "addProvince";
    openModal("Add Province");
  });

  $(document).on("click", ".editProvinceBtn", function (e) {
    e.stopPropagation();
    currentAction = "editProvince";
    currentId = $(this).data("id");
    openModal("Edit Province", $(this).data("name"));
  });

  /* ---------- Add/Edit City ---------- */
  $(document).on("click", ".addCityBtn", function (e) {
    e.stopPropagation();
    currentAction = "addCity";
    currentProvinceID = $(this).data("provinceid");
    openModal("Add City");
  });

  $(document).on("click", ".editCityBtn", function () {
    currentAction = "editCity";
    currentId = $(this).data("id");
    currentProvinceID = $(this).data("provinceid");
    openModal("Edit City", $(this).data("name"));
  });

  /* ---------- Modal Save Button ---------- */
  modalSaveBtn.on("click", function () {
    const name = modalInput.val().trim();
    if (!name) return;

    switch (currentAction) {
      case "addProvince":
        $.post("ajaxadmin/saveProvince.php", { provinceName: name }, function () {
          loadProvinces();
          modal.hide();
        }, "json").fail(function () { alert("Failed to save province."); });
        break;

      case "editProvince":
        $.post("ajaxadmin/updateProvince.php", { provinceID: currentId, provinceName: name }, function (res) {
          if (res.success) loadProvinces();
          modal.hide();
        }, "json").fail(function () { alert("Update failed."); });
        break;

      case "addCity":
        $.post("ajaxadmin/saveCity.php", { provinceID: currentProvinceID, cityName: name }, function (res) {
          if (res.success) {
            $(`.province-card .province-header[data-id='${currentProvinceID}']`).click();
            modal.hide();
          }
        }, "json").fail(function () { alert("Failed to add city."); });
        break;

      case "editCity":
        $.post("ajaxadmin/updateCity.php", { cityID: currentId, cityName: name }, function (res) {
          if (res.success) {
            $(`.province-card .province-header[data-id='${currentProvinceID}']`).click();
            modal.hide();
          }
        }, "json").fail(function () { alert("Update failed."); });
        break;
    }
  });

  /* ---------- Toggle Province Active / Delivery ---------- */
  $(document).on("change", ".toggle-delivery, .toggle-active", function () {
    const provinceID = $(this).data("id");
    const field = $(this).hasClass("toggle-delivery") ? "is_deliverable" : "provinceActive";
    const value = $(this).is(":checked") ? 1 : 0;

    $.ajax({
      url: "ajaxadmin/toggleProvince.php",
      method: "POST",
      data: { provinceID, field, value },
      dataType: "json",
      success: function (res) {
        if (res.success) loadProvinces();
        else alert("Error: " + res.message);
      }
    });
  });

  /* ---------- Toggle City Active / Delivery ---------- */
  $(document).on("change", ".toggle-city-delivery, .toggle-city-active", function () {
    const cityID = $(this).data("id");
    const field = $(this).hasClass("toggle-city-delivery") ? "is_deliverable" : "cityactive";
    const value = $(this).is(":checked") ? 1 : 0;

    $.ajax({
      url: "ajaxadmin/toggleCity.php",
      method: "POST",
      data: { cityID, field, value },
      dataType: "json",
      success: function (res) {
        if (!res.success) alert("Error: " + res.message);
      }
    });
  });

  /* ---------- Search Provinces & Cities ---------- */
  $("#searchRegion").on("input", function() {
    const searchText = $(this).val().toLowerCase();

    $(".province-card").each(function() {
      const provinceName = $(this).find(".province-name").text().toLowerCase();
      const cityNames = $(this).find(".city-name").map(function() { return $(this).text().toLowerCase(); }).get();
      const match = provinceName.includes(searchText) || cityNames.some(name => name.includes(searchText));
      $(this).toggle(match);
    });
  });

});
