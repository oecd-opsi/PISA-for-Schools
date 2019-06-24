//* FORUM BANNERS

var createCookie = function(name, value, days) {
  var expires;
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    expires = "; expires=" + date.toGMTString();
  }
  else {
    expires = "";
  }
  document.cookie = name + "=" + value + expires + "; path=/";
}

function getCookie(c_name) {
  if (document.cookie.length > 0) {
    c_start = document.cookie.indexOf(c_name + "=");
    if (c_start != -1) {
      c_start = c_start + c_name.length + 1;
      c_end = document.cookie.indexOf(";", c_start);
      if (c_end == -1) {
        c_end = document.cookie.length;
      }
      return unescape(document.cookie.substring(c_start, c_end));
    }
  }
  return "";
}

const bannerCloseBtns = document.querySelectorAll( '.bs-close-forum-banner' );

function closeBanner(e) {

  const banner = this.closest( '.bs-forum-banner' );

  // record a cookie
  const bannerId = banner.dataset.id;
  let cookieAsArray = [];
  let existing_cookie = getCookie( 'closedBanner' );

  if ( existing_cookie ) {
    cookieAsArray = JSON.parse( existing_cookie );
    cookieAsArray.push( bannerId );
  } else {
    cookieAsArray = [ bannerId ];
  }

  let json_cookie = JSON.stringify( cookieAsArray );
  createCookie( 'closedBanner', json_cookie, '');

  // close the banner
  banner.style.display = 'none';

}

bannerCloseBtns.forEach( button => button.addEventListener( 'click', closeBanner ) );
