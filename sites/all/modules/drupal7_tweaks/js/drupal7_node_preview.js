(function($) {
	//our object module object
	var Drupal7Tweaks = Drupal7Tweaks || {};
	//our drupal behaviors
	Drupal.behaviors.drupal7_tweaks = {
		attach: function(context, settings) {
			Drupal7Tweaks.preview = $('#node-preview-syntax', context);
			$("#edit-field-text-and-code-und-0-value", context).focus(function() {
				$(this).keydown(function(e) {
					Drupal7Tweaks.activateDycypher($(this), true);
				});
			});
			//function for keydown or focus to process the text
			Drupal7Tweaks.activateDycypher = function(element) {
				//our text and code string
				var code = element.val();
				//give our code linebreaks
				var code_with_linebreaks = code.replace(/\n/g, "<br />");
				//our post code
				var postCode = '&code='+ encodeURIComponent(code);
				//Drupal7Tweaks.preview.html(code_with_linebreaks);
				//post our data to our ajax function
				Drupal7Tweaks.decypherCode(postCode, Drupal7Tweaks.preview);
			}
			//ajax function for decyphering the comments and code
			Drupal7Tweaks.decypherCode = function(code, element) {
				$.post('/ajax/decypher', code, function(data) {
					element.html(data);
					console.log(data);
					console.log(element);
					SyntaxHighlighter.highlight();
				});
			}
			
			$('#insert-code-button', context).click(function(e){
				e.preventDefault();
				var textarea = $("#edit-field-text-and-code-und-0-value", context);
				var current_text = textarea.val();
				var code = '\n <pre class="brush:php"></pre>';
				$("#edit-field-text-and-code-und-0-value", context).val(current_text + code);
			});
		}
	};
})(jQuery);