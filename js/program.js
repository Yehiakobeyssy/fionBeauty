$(document).ready(function () {

    $(document).on("click", ".program_card", function () {
        let proId = $(this).attr("data-id");

        console.log(proId); // debug

        if(proId){
            window.location.href = "specialprogram.php?proId=" + proId;
        }
    });

});