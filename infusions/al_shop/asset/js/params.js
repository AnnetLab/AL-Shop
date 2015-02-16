$(document).ready(function(){
    $('#change_param').change(function(){
        var param_type = $(this).val();
        if (param_type == 'text') {
            $('#param_options').css('display','none');
        } else if (param_type == 'select') {
            $('#param_options').css('display','table-row');
        }
    });

    $('#add_value').live('click',function(e){
        var s = '<div><input type="text" class="textbox" name="param_values[]" /> <a href="#" class="del-param"></a></div>';
        $('#prepender').append(s);
        e.preventDefault();
    });
    $('.del-param').live('click',function(e){
        $(this).parent().remove();
        e.preventDefault();
    });
    $('.del-ex-param').live('click',function(e){
        $.ajax({
            url: baseurl+'includes/params.php',
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 'delete_param',
                param_id: $(this).attr('data-param-id')
            },
            error: function() {
                alert('Ajax error');
            }
        });
        $(this).parent().remove();
        e.preventDefault();
    });
});