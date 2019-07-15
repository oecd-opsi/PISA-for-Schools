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

  // turn off ACF form validation
  window.acf.validation.active = false;

  // get Save, pseudo-Submit and Submit button elements
  submitBtn = document.querySelector( '#case-form .acf-form-submit input' );
  saveBtn = document.querySelectorAll( '.form-btns-wrapper .save' );
  submitPseudoBtn = document.querySelector( '.form-btns-wrapper .submit' );

  // Listen to Save button clic
  saveBtn.forEach( saveBtn => saveBtn.addEventListener( 'click', function(){ submitBtn.click(); }) );

});

// Show step function
function showStep( newID, oldID = 0 ) {

  // get new current step element
  let currentStep = document.querySelector( '#step-' + newID );

  // discover if user is going back or forward and animate the new step
  console.log( oldID + ' - ' + newID );
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
