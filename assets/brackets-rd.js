/* If you call doneCb([value], true), the next edit will be automatically 
   activated. This works only in the first round. */
jQuery.noConflict();
   
function acEditFn(container, data, doneCb) {
  var input = $('<input type="text">')
  input.val(data)
  input.blur(function() { doneCb(input.val()) })
  input.keyup(function(e) { if ((e.keyCode||e.which)===13) input.blur() })
  container.html(input)
  input.focus()
}
 
function acRenderFn(container, data, score) {
    container.append(data)
}
 
jQuery(document).ready(function() {
    jQuery('#autoComplete').bracket({
      init: autoCompleteData,
      decorator: {edit: acEditFn,
                  render: acRenderFn}})
  })