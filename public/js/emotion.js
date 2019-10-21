function formDataChange(btnModifier) {
    var _form = btnModifier.closest('form');
    var _data = {};

    _data[btnModifier.attr('name')] = btnModifier.val();

    $.ajax({
        url : btnModifier.attr('data-href'),
        type: _form.attr('method'),
        data : _data,
    }).then(function(html) {
        $('#div-ajax').replaceWith(
            $(html).find('#div-ajax')
        );
    });
}

$(function() {
    $('body').on("click", "#searchAnnouncement",  function() {
        var _form = $(this).closest('form');
        if(_form.valid()){
            $.ajax({
                type: 'GET',
                url: _form.attr('action'),
                data: _form.serialize(),
                dataType: "json",
            }).then(function(response) {
                $('#r-advantages-part').empty().append(response.html.content);
            });
        }
        return false;
    }).on("click", '#eSwipe', function() {
        var _this  = $(this);
        var _div   = _this.next('#modal-ajax');

        $.ajax({
            type: 'POST',
            url: _this.attr('data-href'),
        }).then(function(response) {
            _div.empty().append(response);
            $('#e-swipeForm').modal('show');
        });
    }).on("click", '.leave-comment', function() {
        var _this  = $(this);
        var _div   = _this.next('#modal-ajax');

        $.ajax({
            type: 'POST',
            url: _this.attr('data-href'),
        }).then(function(response) {
            _div.empty().append(response);
            $('#modal-leave-comment').modal('show');
        });
    }).on("click", '.description', function(e) {
        e.preventDefault();
        var _this     = $(this);
        var _div      = $('#modal-ajax-desc');

        $.ajax({
            type: 'POST',
            url: _this.attr('data-href'),
        }).then(function(response) {
            _div.empty().append(response);
            $('#vehicleDescription').modal('show');
        });
    }).on("click", '#next', function(e) {
        e.preventDefault();
        var _this     = $(this);
        var _formData = new FormData();
        var _form     = _this.closest('form');

        var x = _form.serializeArray();
        $.each(x, function(i, field) {
            _formData.append(field.name,  field.value);
        });

        if(_form.valid()){
            $.ajax({
                type: 'POST',
                url: _form.attr('action'),
                data: _formData,
                contentType : false,
                processData : false,
            }).then(function(response) {
                $('#announcement_body').empty().append(response);
            });
        }
    }).on("click", '#submitData', function(e) {
        e.preventDefault();
        var _this     = $(this);
        var _formData = new FormData();
        var _form     = _this.closest('form');

        var x = _form.serializeArray();
        $.each(x, function(i, field) {
            _formData.append(field.name,  field.value);
        });

        if(_form.valid()){
            $.ajax({
                type: 'POST',
                url: _form.attr('action'),
                data: _formData,
                contentType : false,
                processData : false,
            }).then(function(response) {
                $('#announcement_body').empty().append(response);
            });
        }
    });
});

changeRate(null, 3);

function changeRate(element, rate=null){
    if(rate == null){
        let id = $(element).attr('for');
        let rateAux = $('#'+id).val();
        $('#rate').val(rateAux);
    }else{
        let rateAux = $("#rate").val();
        $("#lblStar"+rateAux).click();
    }
}