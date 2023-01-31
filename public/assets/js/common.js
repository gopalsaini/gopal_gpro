$("form#form").submit(function(e) {

    e.preventDefault();

    var formId = $(this).attr('id');
    var formAction = $(this).attr('action');

    var form_data = new FormData(this);

    var btnhtml = $("button[form=" + formId + "]").html();

    $.ajax({
        url: formAction,
        data: new FormData(this),
        dataType: 'json',
        type: 'post',
        beforeSend: function() {
            submitButton(formId, btnhtml, true);
        },
        error: function(xhr, textStatus) {

            if (xhr && xhr.responseJSON.message) {
                showMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
            } else {
                showMsg('error', xhr.status + ': ' + xhr.statusText);
            }

            submitButton(formId, btnhtml, false);

        },
        success: function(data) {
            showMsg('success', data.message);
            if (!data.update) {
                $('form#' + formId)[0].reset();
            }

            if (data.reload) {
                location.reload();
            }
            submitButton(formId, btnhtml, false);
        },
        cache: false,
        contentType: false,
        processData: false
    });

});

// function showMsg(type, msg) {
//     if (type == 'error') {
//         $('.toast-body').html('<i class="fa fa-times-circle red"></i> ' + msg);
//     } else if (type == 'success') {
//         $('.toast-body').html('<i class="fa fa-check-circle green"></i> ' + msg);
//     } else {
//         $('.toast-body').html('<i class="fa fa-exclamation-circle warning"></i> ' + msg);
//     }

//     $(".toast").toast({ delay: 3000 });
//     $('.toast').toast('show');
// }

function showMsg(type, msg) {

    var toastr = new Toastr({ animation: 'slide', timeout: 5000 });

    var icon = '';

    if (type == 'success') {
        icon = '<i class="fas fa-check-circle"></i>';
    } else if (type == 'error') {
        icon = '<i class="fas fa-times-circle"></i>';
    } else {
        icon = '<i class="faS fa-info-circle"></i>';
    }

    toastr.show(icon + msg);

}

$(document).ready(function() {

    $('.toast').mouseleave(function() {
        $('.toast').toast('hide');
    });

    $('.language').click(function() {
        var lang = $(this).data('lang');
        $.ajax({
            type: "POST",
            dataType: "json",
            url: baseUrl+"/language",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'lang': lang,
            },
            error: function(xhr, textStatus) {
    
                if (xhr && xhr.responseJSON.message) {
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                } else {
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
                }
            },
            success: function(data) {
                if (data.reload) {
                    location.reload();
                }
            }
        });
    });

});


$(function() {   
    $("input[type='file']").change(function() {      
        var uploadType = $(this).data('type');        
        var dvPreview = $("#" + $(this).data('image-preview'));        
        var isUpdate = $(this).data('isupdate');

                 
        if (typeof(FileReader) != "undefined") {            
            var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp|.xlsx)$/;             
            $($(this)[0].files).each(function() {               
                var file = $(this);               
                if (regex.test(file[0].name.toLowerCase())) {                  
                    var reader = new FileReader();                  
                    reader.onload = function(e) {                     
                        var img = $("<img />");                     
                        img.attr("style", "width: 100px;border:1px solid #222;margin-right: 13px");                     
                        img.attr("src", e.target.result);                                          
                        if (uploadType == 'multiple') {                         dvPreview.append(img);                      } else {                         dvPreview.html(img);                      }                  
                    }                  
                    reader.readAsDataURL(file[0]);               
                } else {                   alert(file[0].name + " is not a valid image file.");                   return false;                }            
            });         
        } else {             alert("This browser does not support HTML5 FileReader.");          }      
    });   
});

function submitButton(formId, btnhtml, disabled) {
    if (disabled) {
        $("button[form=" + formId + "]").html(btnhtml + '<pre class="spinner-border spinner-border-sm"  style="color:white;font-size: 100%;position:relative;top:21%;width: 1.4rem;height: 1.4rem;margin-left:10px"></pre>');
    } else {
        $("button[form=" + formId + "]").html(btnhtml);
    }
    $("button[form=" + formId + "]").prop('disabled', disabled);

    window.scrollTo({ top: 0, behavior: 'smooth' });
}


$(".cartqty").change(function(e) {

    e.preventDefault();

    var qty = $(this).val();
    var cartId = $(this).parent().parent().find("input[name=cart_id]").val();
    var productId = $(this).parent().parent().find("input[name=product_id]").val();

    $.ajax({
        url: baseUrl + '/update-cart',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            'id': cartId,
            'product_id': productId,
            'qty': qty
        },
        dataType: 'json',
        type: 'post',
        error: function(xhr, textStatus) {

            if (xhr && xhr.responseJSON.message) {
                showMsg('error', xhr.responseJSON.message);
            } else {
                showMsg('error', xhr.statusText);
            }
        },
        success: function(data) {

            showMsg('success', data.message);

            getPriceDetail();
        },
        cache: false,
        timeout: 5000
    });

});


var stateId = 0;
var cityId = 0;
var countryId = 0;


$('.country').change(function() {
    
    stateId = parseInt($(this).data('state_id'));
    cityId = parseInt($(this).data('city_id'));
    countryId = $(this).val();
    
    $.ajax({
        url: baseUrl + '/get-state?country_id=' + countryId,
        dataType: 'json',
        type: 'get',
        error: function(xhr, textStatus) {

            if (xhr && xhr.responseJSON.message) {
                showMsg('error', xhr.responseJSON.message);
            } else {
                showMsg('error', xhr.statusText);
            }
        },
        success: function(data) {
            $('.statehtml').fSelect('destroy')
            $('.statehtml').html(data.html);

            $('.statehtml option').each(function() {
                if (this.value == stateId)
                $('.statehtml').val(stateId);
                    

            });

            $('.statehtml').fSelect('create');

        },
        cache: false,
        timeout: 5000
    });

});


$(document).ready(getCity);


function getCity() {

    $('.statehtml').change(function() {

        $.ajax({
            url: baseUrl + '/get-city?state_id=' + $(this).val(),
            dataType: 'json',
            type: 'get',
            error: function(xhr, textStatus) {

                if (xhr && xhr.responseJSON.message) {
                    showMsg('error', xhr.responseJSON.message);
                } else {
                    showMsg('error', xhr.statusText);
                }
            },
            success: function(data) {

                $('.cityHtml').fSelect('destroy');
                $('.cityHtml').html(data.html);

                $('.cityHtml option').each(function() {
                    if (this.value == cityId)
                    $('.cityHtml').val(cityId);
                        
                });

                $('.cityHtml').fSelect('create');
            },
            cache: false,
            timeout: 5000
        });
    });

    

}

