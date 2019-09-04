//Signin
$("#signin").submit(function () {
    if (document.getElementById("terms").checked == true) {
        $.ajax({
            type: "POST",
            url: "system/controller.php",
            data: $("#signin").serialize(),
            beforeSend: function () {
                document.getElementById("info").className = "alert alert-info";
                $("#info").html("Kontrol Ediliyor... Bu işlem biraz uzun sürebilir.");
            },
            success: function (data) {
                var parsedData = JSON.parse(data);

                if (parsedData.status == "success") {
                    document.getElementById("info").className = "alert alert-success";
                    $("#info").html(parsedData.reason);

                    setTimeout("location.reload()", 1000);
                } else if (parsedData.status == "error") {
                    document.getElementById("info").className = "alert alert-danger";
                    $("#info").html(parsedData.reason);
                }
            }
        });
    } else {
        document.getElementById("info").className = "alert alert-warning";
        $("#info").html("Kullanım Koşullarını Kabul Etmelisiniz!");
    }
    return false;
});

//Create Schedule
$("#createschedule").submit(function () {
    var data = new FormData(this);
    $.ajax({
        type: "POST",
        url: "system/controller.php",
        data: data,
        contentType: false,
        processData: false,
        success: function (data) {
            var parsedData = JSON.parse(data);

            if (parsedData.status == "success") {
                document.getElementById("info").className = "alert alert-success";
                $("#info").html(parsedData.reason);

                setTimeout("location.reload()", 1000);
            } else if (parsedData.status == "error") {
                document.getElementById("info").className = "alert alert-danger";
                $("#info").html(parsedData.reason);
            }
        }
    });
    return false;
});

//Post Delete
$(document.body).on("click", "button[name=scheduledelete]", function (e) {
    e.preventDefault();
    var message = confirm("do you approve this process?");
    if (message == true) {
        var id = $(this).attr("id");
        $.ajax({
            type: "POST",
            url: "system/controller.php",
            data: {scheduledelete: id},
            success: function (data) {
                location.reload();
            }
        });
        return false;
    }
});