jQuery(document).ready(function($){

  // get tag filter selector
  var topicFilter = $('#tag-filter');

  // tag filter for topics function
  topicFilter.change( function(e) {

    window.location.href = $(this).find(':selected').data('redirect');

  });

  // set selected tag filter option
  var urlParams = new URLSearchParams(window.location.search);
  if ( urlParams.get('topictag') ) {
    console.log(urlParams.get('topictag'));
    topicFilter.val( urlParams.get('topictag') );
  }

});
