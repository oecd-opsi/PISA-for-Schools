jQuery(document).ready(function($){

  $(document).on( 'change', 'input#field_15', function(e){

    // get input value and phone field object
    var country = $(this).val(),
        phoneField = $('input#field_17');

    // set a delay before acting
    setTimeout( function(e){

      $.getJSON( "/wp-content/themes/boss-child/js/country-prefixes.json", function( result ){
        // filter the JSON and get the phone prefix
        var countryObj = result.find( function( item ) {
          if ( item.name == country ) {
            return true;
          }
        } );
        var prefix = countryObj.dial_code;
        // insert the prefix inside the field 
        phoneField.val(prefix);
      });

    }, 500);

  });

});
