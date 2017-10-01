jQuery(function($){

  // Activate the color picker
  jQuery ('input.color')
  .each(function(){
    var $this = jQuery(this);
    var $picker = $this.parent().find('.colorpicker');
    $picker
    .farbtastic($this)
    .hide();
  })
  .after('<span class="show-colorpicker">&nbsp;</span>');

  jQuery('input.color, span.show-colorpicker')
  .click(function(){
    jQuery(this).parent().find('.colorpicker').slideToggle();
  });

  // Add the line highlighting
  jQuery('div.capability-selection:odd').addClass('highlight1');
  jQuery('div.capability-selection:odd:odd').addClass('highlight2');

	// Add a confirmation message to the delete link
  jQuery('a.delete-link').click(function(){
    return confirm( $delete_confirm_message );
  });

});


jQuery(window).load(function(){

  // Hide options boxes
  jQuery('div.should-be-closed').removeClass('should-be-closed').find('.hndle').click();
  jQuery('div.should-be-opened').removeClass('should-be-opened');

});
