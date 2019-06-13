//* FORUM BANNERS

const bannerCloseBtns = document.querySelectorAll( '.bs-close-forum-banner' );

function closeBanner(e) {

  const banner = this.closest( '.bs-forum-banner' );

  // record user's choice to hide banner via AJAX
  const bannerId = banner.dataset.id;

  let request = new XMLHttpRequest();
  request.open( 'POST', ajaxurl, true );
  request.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded;' );
  request.onload = function () {
    if ( this.status >= 200 && this.status < 400 ) {
      // If successful
      console.log( 'success: ' + this.response );
    } else {
      // If fail
      console.log( 'fail: ' + this.response );
    }
  };
  request.onerror = function() {
    // Connection error
    console.log( 'Connection error' );
  };
  request.send( 'action=record_close_banner&banner_id=' + bannerId );

  // close the banner
  banner.style.display = 'none';

}

bannerCloseBtns.forEach( button => button.addEventListener( 'click', closeBanner ) );
