$(document).ready(function(){
    $('.ajax-stats').click(function(){
        
        var url = jQuery(this).data('url');
        var data = { event: jQuery(this).data('event') };
        var idJSON = JSON.stringify(data);
        
        request = { 
            type: 'POST',
            url: url,
            data: idJSON,
            dataType: 'JSON',
            success: function(response, status, xhr){
                $('#attsNo').text(response.attsNo);
                $('#newAttsNo').text(response.newAttsNo);
                $('#declaredUsers').text(response.declaredUsers);
                $('#declaredTempUsers').text(response.declaredTempUsers);
                $('#arrivedAtts').text(response.arrivedAtts);
                $('#arrivedUserAtts').text(response.arrivedUserAtts);
                $('#arrivedTempUserAtts').text(response.arrivedTempUserAtts);
                
                $('#attsNoP').text(response.attsNoP + '%');
                $('#newAttsNoP').text(response.newAttsNoP + '%');
                $('#declaredUsersP').text(response.declaredUsersP + '%');
                $('#declaredTempUsersP').text(response.declaredTempUsersP + '%');
                $('#arrivedAttsP').text(response.arrivedAttsP + '%');
                $('#arrivedUserAttsP').text(response.arrivedUserAttsP + '%');
                $('#arrivedTempUserAttsP').text(response.arrivedTempUserAttsP + '%');
                
                $('#attsNoP').animate({'width': response.attsNoP + '%'});
                $('#newAttsNoP').animate({'width': response.newAttsNoP + '%'});
                $('#declaredUsersP').animate({'width': response.declaredUsersP + '%'});
                $('#declaredTempUsersP').animate({'width': response.declaredTempUsersP + '%'});
                $('#arrivedAttsP').animate({'width': response.arrivedAttsP + '%'});
                $('#arrivedUserAttsP').animate({'width': response.arrivedUserAttsP + '%'});
                $('#arrivedTempUserAttsP').animate({'width': response.arrivedTempUserAttsP + '%'});
            },
            error: function(response, status, xhr){
                error(response);
            }
        };
        jQuery.ajax(request);
    });
        
});




