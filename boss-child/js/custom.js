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

  /* necessary to get @ mentions working in the tinyMCE on the forums */
  window.onload = function() {
    my_timing = setInterval(function(){myTimer();},1000);
    function myTimer() {
      if (typeof window.tinyMCE !== 'undefined' && window.tinyMCE.activeEditor !== null && typeof window.tinyMCE.activeEditor !== 'undefined') {
        $( window.tinyMCE.activeEditor.contentDocument.activeElement )
    			.atwho( 'setIframe', $( '.wp-editor-wrap iframe' )[0] )
    			.bp_mentions( bp.mentions.users );
  		  window.clearInterval(my_timing);
      }
    }
    myTimer();
	};

});
