jQuery(document).ready(function($){
  let mediaUploaderBefore;
  let mediaUploaderAfter;
//   For Before
  $('#upload_image_button_before').click(function(e) {
    e.preventDefault();
      if (mediaUploaderBefore) {
      mediaUploaderBefore.open();
      return;
    }
    mediaUploaderBefore = wp.media.frames.file_frame = wp.media({
      title: 'Choose Image',
      button: {
      text: 'Choose Image'
    }, multiple: false });
    mediaUploaderBefore.on('select', function() {
      var attachment = mediaUploaderBefore.state().get('selection').first().toJSON();
      $('#background_image_before').val(attachment.url);
    });
    mediaUploaderBefore.open();
  });
    //  For After  
  $('#upload_image_button_after').click(function(e) {
    e.preventDefault();
      if (mediaUploaderAfter) {
      mediaUploaderAfter.open();
      return;
    }
    mediaUploaderAfter = wp.media.frames.file_frame = wp.media({
      title: 'Choose Image',
      button: {
      text: 'Choose Image'
    }, multiple: false });
    mediaUploaderAfter.on('select', function() {
      var attachment = mediaUploaderAfter.state().get('selection').first().toJSON();
      $('#background_image_after').val(attachment.url);
    });
    mediaUploaderAfter.open();
  });
});