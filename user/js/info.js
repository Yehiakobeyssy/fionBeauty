
$(document).ready(function() {

    // When Province changes → load cities
    $('#provinceSelect').on('change', function() {
        let provinceID = $(this).val();

        if (provinceID == 0) {
            $('#citySelect').html('<option value="">SELECT ONE</option>');
            return;
        }

        $.ajax({
            url: 'ajaxuser/getCities.php', // ✅ user path
            type: 'POST',
            data: { provinceID: provinceID },
            dataType: 'json',
            success: function(response) {
                let options = '<option value="">SELECT ONE</option>';
                if (response.length > 0) {
                    $.each(response, function(i, city) {
                        options += '<option value="'+city.cityID+'">'+city.cityName+'</option>';
                    });
                } else {
                    options += '<option value="">No cities found</option>';
                }
                $('#citySelect').html(options);
            }
        });
    });

    // When City changes → auto-select province
    $('#citySelect').on('change', function() {
        let cityID = $(this).val();
        if (cityID === "") return;

        $.ajax({
            url: 'ajaxuser/getProvinceByCity.php', // ✅ user path
            type: 'POST',
            data: { cityID: cityID },
            dataType: 'json',
            success: function(response) {
                if (response && response.provinceID) {
                    $('#provinceSelect').val(response.provinceID);
                }
            }
        });
    });

});

