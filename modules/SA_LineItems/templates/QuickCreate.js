$(function () {
    var form = $('#line-item-form');

    form.submit(function (e) {
        e.preventDefault();

        var datastring = $(this).serialize();

        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: datastring
        }).success(function (data) {
            if (data === "") {
                form.find('input[type!=\'hidden\'][type!=\'submit\']').val('');
                // TODO Somehow add a new line to the table or refresh it
            } else {
                alert(data);
            }
        }).error(function (a, b, c) {
            alert("Error!");
            console.log(a, b, c);
        });
    });
});