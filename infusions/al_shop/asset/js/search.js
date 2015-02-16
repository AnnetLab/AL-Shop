

$(document).ready(function(){
    var config = {
        siteURL     : siteURL,
        searchSite  : true,
        type        : 'web',
        append      : false,
        perPage     : 8,
        page        : 0
    }

    $('#searchForm').submit(function(){
        googleSearch();
        return false;
    });

    if (do_search) {
        googleSearch();
    }

    function googleSearch(settings){
        settings = $.extend({},config,settings);
        settings.term = settings.term || $('#s').val();
        if(settings.searchSite){
            settings.term = 'site:'+settings.siteURL+' '+settings.term;
        }
        var apiURL = 'http://ajax.googleapis.com/ajax/services/search/'+settings.type+'?v=1.0&callback=?';
        var resultsDiv = $('#resultsDiv');
        $.getJSON(apiURL,{q:settings.term,rsz:settings.perPage,start:settings.page*settings.perPage},function(r){
            var results = r.responseData.results;
            $('#more').remove();
            if(results.length){
                var pageContainer = $('<div>',{className:'pageContainer'});
                for(var i=0;i<results.length;i++){
                    pageContainer.append(new result(results[i]) + '');
                }
                if(!settings.append){
                    resultsDiv.empty();
                }
                pageContainer.append('<div class="clear"></div>')
                    .hide().appendTo(resultsDiv)
                    .fadeIn('slow');
                var cursor = r.responseData.cursor;
                if( +cursor.estimatedResultCount > (settings.page+1)*settings.perPage){
                    $('<div>',{id:'more'}).text(moreTEXT).appendTo(resultsDiv).click(function(){
                        googleSearch({append:true,page:settings.page+1});
                        $(this).fadeOut();
                    });
                }
            }
            else {
                resultsDiv.empty();
                $('<p>',{className:'notFound',html:notfoundTEXT}).hide().appendTo(resultsDiv).fadeIn();
            }
        });
    }

    function result(r){
        var arr = [];
                arr = [
                    '<div class="webResult">',
                    '<h2><a href="',r.unescapedUrl,'" target="_blank">',r.title,'</a></h2>',
                    '<p>',r.content,'</p>',
                    '<a href="',r.unescapedUrl,'" target="_blank">',r.visibleUrl,'</a>',
                    '</div>'
                ];
        this.toString = function(){
            return arr.join('');
        }
    }




});