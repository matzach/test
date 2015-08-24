$(document).ready(function() {
    //add class "highlight" when hover over the row
    $('table tbody tr').hover(function() {             
       $(this).addClass('highlight');
    }, function() {
       $(this).removeClass('highlight');
    });

 }); 
