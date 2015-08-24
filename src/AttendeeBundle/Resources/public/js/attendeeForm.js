$(document).ready(function(){
    
    if( $( 'input#attendee_czyCalosc').is( ':checked' ) ) {
        $( 'div#attendee_noce' ).css( 'display', 'none' );
    }
    
    if( $( 'input#attendee_zgloszePozniej').is( ':checked' ) ) {
        $( 'div:has(> input#attendee_emailAtt2User)').css('display', 'none');
    }
    
    $( 'input#attendee_czyCalosc').click(function(){
        if( $( 'input#attendee_czyCalosc').is( ':checked' ) ) {
            $( 'div#attendee_noce' ).css( 'display', 'none' );
        }
        
        if( !$( 'input#attendee_czyCalosc').is( ':checked' ) ) {
            $( 'div#attendee_noce' ).css( 'display', 'block' );
        }
    });
    
    $( 'input#attendee_zgloszePozniej' ).click(function(){
        if( $( 'input#attendee_zgloszePozniej').is( ':checked' ) ) {
            $( 'div:has(> input#attendee_emailAtt2User)').css('display', 'none');
        }
        
        if( !$( 'input#attendee_zgloszePozniej').is( ':checked' ) ) {
            $( 'div:has(> input#attendee_emailAtt2User)').css('display', 'block');
        }
        
    });
});

