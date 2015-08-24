jQuery(document).ready(function(){
    
//    var hostname = jQuery(location).attr('hostname');
//    
//    if( hostname == "localhost" ) {    
//        var urlResponse = "http://"+hostname+"/akredytacja/web/app_dev.php/attendee/ajax/";
//    } else {
//        var urlResponse = "http://"+hostname+"/attendee/ajax/";
//    }
    
    var urlResponse = '/app_dev.php/attendee/ajax/';
    
    jQuery('.att-select').change(function(){
        var attId = jQuery(this).data('attendee');
        var data = { status: jQuery(this).val(), event: jQuery(this).data('event'), attendee: attId };
        var dataJSON = JSON.stringify(data);
        
        jQuery.ajax({
            type:"POST",
            url: urlResponse+'status',
            data: dataJSON,            
            daratype: JSON,
            success: function(msg) {  
                jQuery( '#att-skladka-' + attId ).val(0);
                jQuery( '#att-skladka-uwaga-' + attId ).val('');
                addAjaxMessage2('Status zmieniony.', 'success');
            },
            error: function(msg) {
                addAjaxMessage2('Błąd przy zmianie statusu. ' + msg, 'error');
            }
        });
    });
    
    jQuery('.att-skladka-btn').click(function(){
        var attId = jQuery(this).data('attendee');
        var data = { 
                event: jQuery(this).data('event'), 
                attendee: attId, 
                skladka: jQuery( '#att-skladka-' + attId ).val(),
                comment: jQuery( '#att-skladka-uwaga-' + attId ).val()
            };
        
        var dataJSON = JSON.stringify(data);
        
        jQuery.ajax({
            type:"POST",
            url: urlResponse+'dues',
            data: dataJSON,            
            daratype: JSON,
            success: function(msg) {  
                jQuery( '#att-select-' + attId ).val(7);
                addAjaxMessage2('Składka zmieniona.', 'success');
            },
            error: function(msg) {
                addAjaxMessage2('Bład przy zmianie składki. ' + msg, 'error');
            }
        });
    });
    
    jQuery('.org-comment-button').click(function(){
        var attId = jQuery(this).data('attendee');
        var data = {
            event: jQuery(this).data('event'), 
            attendee: attId, 
            comment: jQuery( '#org-comment-' + attId ).val()
        };
        
        var dataJSON = JSON.stringify(data);
        
        jQuery.ajax({
            type:"POST",
            url: urlResponse+'orgcomment',
            data: dataJSON,            
            daratype: JSON,
            success: function(msg) {  
                addAjaxMessage2('Dodana uwaga.', 'success');
            },
            error: function(msg) {
                addAjaxMessage2('Błąd przy dodawaniu uwagi. ' + msg, 'error');
            }
        });
    });
    
    jQuery('.att-arrived').click(function(){
        var attId = jQuery(this).data('attendee');
        var data = {
            event: jQuery(this).data('event'), 
            attendee: attId, 
            arrived: jQuery(this).prop('checked')
        };
        
        if (jQuery(this).prop('checked') == false) {
            if (!confirm('Czy napewno odznaczyć przyjazd uczesnika?')) {
                jQuery(this).prop('checked', true);
                return 0;
            }
        }
        
        var dataJSON = JSON.stringify(data);
        
        jQuery.ajax({
            type:"POST",
            url: urlResponse+'arrived',
            data: dataJSON,            
            daratype: JSON,
            success: function(msg) {  
                addAjaxMessage2('Uczestnik przyjechał/nie przyjechał na event.', 'success');
            },
            error: function(msg) {
                addAjaxMessage2('Błąd przy zaznaczeniu uczesntika. ' + msg, 'error');
            }
        });
    });
    
    jQuery('.att-resignation-btn').click(function(e){
        event.stopPropagation();
        e.preventDefault();
        var attId = jQuery(this).data('attendee');
        var data = {
            attendee: attId
        };
        
        var dataJSON = JSON.stringify(data);
        
        jQuery.ajax({
            type:"POST",
            url: urlResponse+'resignate',
            data: dataJSON,            
            daratype: JSON,
            success: function(msg) {  
                console.log(msg.result);
                addAjaxMessage(msg.result, 'success');
            },
            error: function(msg) {
                addAjaxMessage(msg, 'error');
            }
        });
    });
    
    jQuery('.att-redeclare-btn').click(function(e){
        event.stopPropagation();
        e.preventDefault();
        var attId = jQuery(this).data('attendee');
        var data = {
            attendee: attId
        };
        
        var dataJSON = JSON.stringify(data);
        
        jQuery.ajax({
            type:"POST",
            url: urlResponse+'redeclare',
            data: dataJSON,            
            daratype: JSON,
            success: function(msg) {  
                console.log(msg.result);
                addAjaxMessage(msg.result, 'success');
            },
            error: function(msg) {
                addAjaxMessage(msg, 'error');
            }
        });
    });
    
    //Wyświetla wiadomość z reloadem
    function addAjaxMessage(msg, cls)
    {
        var info = jQuery('<div class="ajax-msg ' +cls+ '">' +msg+ '</div>');
        jQuery('#ajax-nest').prepend(info);
        info.fadeOut(3000, function(){location.reload(true);});
    }
    
    //Wyświetla wiadomość bez reloadu
    function addAjaxMessage2(msg, cls)
    {
        var info = jQuery('<div class="ajax-msg ' +cls+ '">' +msg+ '</div>');
        jQuery('#ajax-nest').prepend(info);
        info.fadeOut(7000);
    }
    
});

