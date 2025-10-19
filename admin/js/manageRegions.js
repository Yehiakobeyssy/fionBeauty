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
      // if not loaded yet or want fresh, (we load fresh each time)
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

  /* ---------- Add Province ---------- */
  $("#addProvinceBtn").on("click", function () {
    let name = prompt("Enter province name:");
    if (!name) return;
    $.post("ajaxadmin/saveProvince.php", { provinceName: name }, function (resp) {
      loadProvinces();
    }, "json").fail(function () {
      alert("Failed to save province.");
    });
  });

  /* ---------- Edit Province ---------- */
  $(document).on("click", ".editProvinceBtn", function (e) {
    e.stopPropagation();
    let id = $(this).data("id");
    let current = $(this).data("name") || "";
    let name = prompt("Edit province name:", current);
    if (!name) return;
    $.post("ajaxadmin/updateProvince.php", { provinceID: id, provinceName: name }, function (res) {
      if (res.success) loadProvinces();
      else alert(res.message || "Update failed.");
    }, "json").fail(function () { alert("Update failed."); });
  });

  /* ---------- Toggle Province Active (inactive) ---------- */
// Toggle delivery
$(document).on("change", ".toggle-delivery", function () {
  let provinceID = $(this).data("id");
  let value = $(this).is(":checked") ? 1 : 0;

  $.ajax({
    url: "ajaxadmin/toggleProvince.php",
    method: "POST",
    data: { provinceID, field: "is_deliverable", value },
    dataType: "json",
    success: function (res) {
      if (res.success) {
        loadProvinces();
      } else {
        alert("Error: " + res.message);
      }
    }
  });
});

// Toggle active
$(document).on("change", ".toggle-active", function () {
  let provinceID = $(this).data("id");
  let value = $(this).is(":checked") ? 1 : 0;

  $.ajax({
    url: "ajaxadmin/toggleProvince.php",
    method: "POST",
    data: { provinceID, field: "provinceActive", value },
    dataType: "json",
    success: function (res) {
      if (res.success) {
        loadProvinces();
      } else {
        alert("Error: " + res.message);
      }
    }
  });
});


  /* ---------- Add City ---------- */
  $(document).on("click", ".addCityBtn", function (e) {
    e.stopPropagation();
    let provinceID = $(this).data("provinceid");
    let name = prompt("Enter city name:");
    if (!name) return;
    $.post("ajaxadmin/saveCity.php", { provinceID: provinceID, cityName: name }, function (res) {
      if (res.success) {
        // reload cities for that province
        $(`.province-card .province-header[data-id='${provinceID}']`).click();
      } else alert(res.message || "Failed to add city.");
    }, "json").fail(function () { alert("Failed to add city."); });
  });

  /* ---------- Edit City ---------- */
  $(document).on("click", ".editCityBtn", function () {
    let id = $(this).data("id");
    let current = $(this).data("name") || "";
    let provinceID = $(this).data("provinceid");
    let name = prompt("Edit city name:", current);
    if (!name) return;
    $.post("ajaxadmin/updateCity.php", { cityID: id, cityName: name }, function (res) {
      if (res.success) {
        // reload cities for that province
        $(`.province-card .province-header[data-id='${provinceID}']`).click();
      } else alert(res.message || "Update failed.");
    }, "json").fail(function () { alert("Update failed."); });
  });

  /* ---------- Toggle City Active ---------- */
$(document).on("change", ".toggle-city-delivery", function () {
  let cityID = $(this).data("id");
  let value = $(this).is(":checked") ? 1 : 0;

  $.ajax({
    url: "ajaxadmin/toggleCity.php",
    method: "POST",
    data: { cityID, field: "is_deliverable", value },
    dataType: "json",
    success: function (res) {
      if (!res.success) alert("Error: " + res.message);
    }
  });
});

// Toggle City Active
$(document).on("change", ".toggle-city-active", function () {
  let cityID = $(this).data("id");
  let value = $(this).is(":checked") ? 1 : 0;

  $.ajax({
    url: "ajaxadmin/toggleCity.php",
    method: "POST",
    data: { cityID, field: "cityactive", value },
    dataType: "json",
    success: function (res) {
      if (!res.success) alert("Error: " + res.message);
    }
  });
});


});
