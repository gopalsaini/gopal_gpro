$("form#form").submit(function(e) {

    e.preventDefault();

    var formId = $(this).attr('id');
    var formAction = $(this).attr('action');
    var btnhtml = $("button[form="+formId+"]").html();

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
                sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
            } else {
                sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
            }
            submitButton(formId, btnhtml, false);
        },
        success: function(data) {
            if (data.error) {
                sweetAlertMsg('error', data.message);
            } else {

                if (data.reset) {
                    $('#' + formId)[0].reset();

                    if($('.previewimages').length > 0) {
                        $('.previewimages').html('');
                    }
                    if($('#summernote').length > 0) {
                        $('#summernote').summernote('reset');
                    }
                    if($('.js-example-tags').length > 0){
                        $(".js-example-tags").select2({
                            allowClear: true
                        });
                    }
                }

                if (data.reload) {
                    location.reload();
                }

                if (data.script) {
                    resetFormData();
                }
                
                if (data.comment && $('#commentstablelist').length > 0) {
                    $('#commentstablelist').DataTable().ajax.reload();
                    $('#userHistoryList').DataTable().ajax.reload();
                }
                sweetAlertMsg('success', data.message);

            }
            submitButton(formId, btnhtml, false);
        },
        cache: false,
        contentType: false,
        processData: false,
    });

});

$('.language').click(function() {
    var lang = $(this).data('lang');
    $.ajax({
        type: "POST",
        dataType: "json",
        url: baseUrl+"/admin/language",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            'lang': lang,
        },
        beforeSend: function() {
            $('#preloader').css('display', 'block');
        },
        error: function(xhr, textStatus) {

            if (xhr && xhr.responseJSON.message) {
                sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
            } else {
                sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
            }
            $('#preloader').css('display', 'none');
        },
        success: function(data) {
            $('#preloader').css('display', 'none');
            if (data.reload) {
                location.reload();
            }
        }
    });
});

function sweetAlertMsg(type, msg) {
    if (type == 'success') {
        swal({
            title: 'Success !',
            text: msg,
            icon: "success",
            button: "OK",
            closeOnClickOutside: false
        });
    } else {
        swal({
            title: "Error!",
            text: msg,
            icon: "error",
            button: "Ok",
            dangerMode: true,
            closeOnClickOutside: false
        });
    }
}

function notify(type, msg) {
    $.notify(`<i class="fa fa-bell-o"></i><strong>${type}</strong> ${msg}`, {
        type: 'theme',
        allow_dismiss: true,
        delay: 2000,
        showProgressbar: true,
        timer: 1000
    });
}

$(function() {   
    $("input[type='file']").change(function() {      
        var uploadType = $(this).data('type');        
        var dvPreview = $("#" + $(this).data('image-preview'));        
        var isUpdate = $(this).data('isupdate');

                 
        if (typeof(FileReader) != "undefined") {            
            var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp|.xlsx|.pdf)$/;             
            $($(this)[0].files).each(function() {               
                var file = $(this);               
                if (regex.test(file[0].name.toLowerCase())) {                  
                    var reader = new FileReader();                  
                    reader.onload = function(e) {                     
                        var img = $("<img />");                     
                        img.attr("style", "width: 150px;");                     
                        img.attr("src", e.target.result);                                          
                        if (uploadType == 'multiple') {                         dvPreview.append(img);                      } else {                         dvPreview.html(img);                      }                  
                    }                  
                    reader.readAsDataURL(file[0]);               
                } else {                   alert(file[0].name + " is not a valid image file.");                   return false;                }            
            });         
        } else {             alert("This browser does not support HTML5 FileReader.");          }      
    });   
});

$('#summernote').summernote({
    placeholder: 'Enter Description',
    tabsize: 2,
    height: 200,
});

function submitButton(formId, btnhtml, disabled){
    if(disabled){
        $("button[form="+formId+"]").html(btnhtml+' <i class="spinner-border spinner-border-sm"></i>');
        $('#preloader').show();
    }else{
        $("button[form="+formId+"]").html(btnhtml);
        $('#preloader').hide();
        window.scrollTo({ top: 0, behavior: 'smooth'});
    }
    $("button[form="+formId+"]").prop('disabled', disabled);
}
