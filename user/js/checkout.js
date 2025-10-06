$(document).ready(function() {

    $(".add").click(function() {
        $(".add").css("border", "1px solid transparent");
        $(this).css("border", "1px solid #009245");
        $(".choiceAdd").prop("checked", false);
        $(this).find(".choiceAdd").prop("checked", true);
        var addID = $(this).find(".choiceAdd").val();
        $("#txtaddress").val(addID);
    });

    $('#openform').click(function(){
        $('.frmadd').slideDown(500);
        $('.openformadd').slideUp(500);
    });

    $("#txtinvoiceNote").on("input blur", function() {
        $("#txtnote").val($(this).val());
    });
});
