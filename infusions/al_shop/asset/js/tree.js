var catSearchUrl = '../includes/goods-load.php'
$(document).ready(function () {
    $('#catTree li:has("ul")').find('a:first').prepend('<em class="marker"></em>');
    $('#catTree li span').click(function () {
        $('a.current').removeClass('current');
        var a = $('a:first',this.parentNode);
        var cat_id = a.attr('cat-id');
        a.toggleClass('current');
        var li=$(this.parentNode);
        if (!li.next().length) {
            li.find('ul:first > li').addClass('last');
        }
        var ul=$('ul:first',this.parentNode);
        if (ul.length) {
            ul.slideToggle(300);
            var em=$('em:first',this.parentNode);
            em.toggleClass('open');
        }
        $.ajax({
            url: catSearchUrl,
            method: "GET",
            dataType: "JSON",
            data: {
                cat_id: cat_id
            },
            success: function(data) {
                $('#catResult-published').empty();
                $('#catResult-unpublished').empty();
                if (data.total > 0) {
                    if (data.goods_published) {
                        for (key in data.goods_published) {
                            $('#catResult-published').append(data.goods_published[key].good_title+' <a href=\''+fusion_self_aid+'&page=goods&edit='+data.goods_published[key].good_id+'\'>['+editTEXT+']</a> <a href=\''+fusion_self_aid+'&page=goods&delete='+data.goods_published[key].good_id+'\' onclick=\'return confirm(\"Confirm delete\");\'>['+deleteTEXT+']</a><br />');
                        }
                    } else {
                        $('#catResult-published').append('No results');
                    }
                    if (data.goods_unpublished) {
                        for (key in data.goods_unpublished) {
                            $('#catResult-unpublished').append(data.goods_unpublished[key].good_title+' <a href=\''+fusion_self_aid+'&page=goods&edit='+data.goods_unpublished[key].good_id+'\'>['+editTEXT+']</a> <a href=\''+fusion_self_aid+'&page=goods&delete='+data.goods_unpublished[key].good_id+'\' onclick=\'return confirm(\"Confirm delete\");\'>['+deleteTEXT+']</a><br />');
                        }
                    } else {
                        $('#catResult-unpublished').append('No results');
                    }
                } else {
                    $('#catResult-published').append('No results');
                    $('#catResult-unpublished').append('No results');
                }
            }
        });
    });
});