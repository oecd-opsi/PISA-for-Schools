// CASE STUDY FORM SCRIPT

var steps, currentStepN, submitBtn, saveBtn, submitPseudoBtn;

// Fire when DOM is ready
document.addEventListener( 'DOMContentLoaded', function(){

  // Get all field groups; each group is a step
  steps = document.querySelectorAll( '[id^="step-"]' );

  // hide all steps
  steps.forEach( step => step.style.display = 'none' );

  // get from URL the step to display or display step zero
  currentStepN = window.location.hash.replace( /^\D+/g, '');
  currentStepN ? showStep( currentStepN ) : showStep( 0 );

  // get Save, pseudo-Submit and Submit button elements
  submitBtn = document.querySelector( '#case-form .acf-form-submit input' );
  saveBtn = document.querySelectorAll( '.form-btns-wrapper .save' );
  submitPseudoBtn = document.querySelector( '.form-btns-wrapper .submit' );

  // Listen to Save button clic, then trigger clic on Submit button
  saveBtn.forEach( btn => btn.addEventListener( 'click', function(e){

    // turn off ACF form validation
    window.acf.validation.active = false;

    e.preventDefault;

    // edit Select status value
    document.querySelector( '#acf-field_5d2d9016a2abe' ).value = 'draft';

    submitBtn.click();

  }) );

  // Listen to pseudo-Submit button clic, then change Select status field value and trigger clic on Submit button
  submitPseudoBtn.addEventListener( 'click', function(e){

    // turn on ACF form validation
    window.acf.validation.active = true;

    e.preventDefault;

    // edit Select status value
    document.querySelector( '#acf-field_5d2d9016a2abe' ).value = 'pending';

    submitBtn.click();

  });

});

// Show step function
function showStep( newID, oldID = 0 ) {

  // get new current step element
  let currentStep = document.querySelector( '#step-' + newID );

  // remove active class to old step in side navigation
  let oldStepNav = document.querySelector( '.form-step-navigation [href="#step-' + oldID + '"]' );
  oldStepNav.classList.remove( 'active' );
  // add active class to new step in side navigation
  let newStepNav = document.querySelector( '.form-step-navigation [href="#step-' + newID + '"]' );
  newStepNav.classList.add( 'active' );

  // discover if user is going back or forward and animate the new step
  if ( oldID <= newID ) {
    currentStep.classList.remove( 'fadeInLeft' );
    currentStep.classList.add( 'fadeInRight' )
  } else {
    currentStep.classList.remove( 'fadeInRight' );
    currentStep.classList.add( 'fadeInLeft' );
  }
  currentStep.style.display = 'inherit';

  // update current step number
  currentStepN = newID;

}

// listen to URL hash change
window.addEventListener( 'hashchange', function(e){

  // hide all steps
  steps.forEach( step => step.style.display = 'none' );

  // display new step
  let newStepN = window.location.hash.replace( /^\D+/g, '');
  showStep( newStepN, currentStepN );
  document.documentElement.scrollTop = 0;

});

// after ACF form validation is complete
acf.add_filter('validation_complete', function( json, $form ){

  // check if there is the ACF error banner
  setTimeout(function () {
    if ( document.querySelector( '.acf-notice.acf-error-message' ) ) {

      console.log( json );
      document.documentElement.scrollTop = 0;

      steps.forEach( function( step ) {

        // remove validated and haserror classes from previous validation
        step.classList.remove( 'validated', 'haserror' )

        // related step side nav item
        let stepNavID = step.id;
        let stepNav = document.querySelector( '[href="#' + stepNavID + '"]' );

        // add haserror and validated classes
        if ( step.querySelector( '.acf-error' ) ) {
          stepNav.classList.add( 'haserror' );
        } else {
          stepNav.classList.add( 'validated' );
        }

      });

    }
  }, 1000);

	// return
	return json;

});
