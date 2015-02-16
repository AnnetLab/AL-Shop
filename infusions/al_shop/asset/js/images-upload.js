function delete_image(id) {
    $.ajax({
        url: al_shop_dir+'includes/images-upload.php',
        type: 'POST',
        dataType: 'JSON',
        data: {
            action: 'delete_image',
            image_id: id
        },
        success: function(data) {
            $('.uimage-'+id).slideUp().remove();
        }
    });
    return false;
}



$(document).ready(function(){
    var uploader = new plupload.Uploader({
        runtimes : 'html5,flash',
        browse_button : 'pickfiles',
        container : 'files-container',
        max_file_size : '2mb',
        //multipart : false,
        url : al_shop_dir+'includes/images-upload.php',
        flash_swf_url : al_shop_dir+'includes/plupload/js/plupload.flash.swf',
        filters : [
            {title : 'Image files', extensions : 'jpg,gif,png'}
        ]//,
        //resize : {width : thumb_width, height : thumb_height, quality : 100}
    });

    uploader.bind('Init', function(up, params) {
        //$('#filelist').html('<div>".$locale['shp49'].":</div>');
    });

    $('#uploadfiles').click(function(e) {
        uploader.start();
        e.preventDefault();
    });

    uploader.init();

    uploader.bind('FilesAdded', function(up, files) {
        $.each(files, function(i, file) {
            $('#filelist').append(
                '<div id=\'' + file.id + '\'>' +
                    file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
                    '</div>');
        });

        up.refresh();
        uploader.start();
        //e.preventDefault();
    });

    uploader.bind('UploadProgress', function(up, file) {
        $('#' + file.id + ' b').html(file.percent + '%');
    });

    uploader.bind('Error', function(up, err) {
        $('#filelist').append('<div>Error: ' + err.code +
            ', Message: ' + err.message +
            (err.file ? ', File: ' + err.file.name : '') +
            '</div>'
        );

        up.refresh();
    });

    uploader.bind('FileUploaded', function(up, file, data) {
        //alert(file.id);
        //alert(file.image_id);
        //console.log(data.response);
        //alert(data.response);
        var response = $.parseJSON(data.response);
        //console.log(response);
        $('#files-uploaded').append('<div class=\'uimage uimage-'+response.id+'\'><input type=\'radio\' name=\'cover\' value=\''+response.id+'\' /> <img src=\''+al_shop_dir+'asset/goods/'+response.thumb+'\' height=\'50\' /><a href=\'#del\' onclick=\'javascript: delete_image('+response.id+');\'>['+deleteTEXT+']</a><input type=\'hidden\' name=\'images-uploaded[]\' value=\''+response.id+'\' /></div>');

        $('#' + file.id + ' b').html('100%');
        $('#' + file.id).fadeOut().remove();
    });

});