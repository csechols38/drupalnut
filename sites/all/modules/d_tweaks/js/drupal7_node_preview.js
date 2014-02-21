(function($) {
	//our object module object
	var Drupal7Tweaks = Drupal7Tweaks || {};
	//our drupal behaviors
	Drupal.behaviors.drupal7_tweaks = {
		attach: function(context, settings) {
			Drupal7Tweaks.preview = $('#node-preview-syntax', context);
			$("#edit-field-text-and-code-und-0-value", context).focus(function() {
				//set our preview dialog
				 Drupal7Tweaks.preview.dialog({ width: 700, height: 400, position: { my: "left center", at: "right", of: $(this) } });
				 //start our decypher function on keydown
				$(this).keydown(function(e) {
					//clear the timout so dont only have one timout function running at once
					clearTimeout(Drupal7Tweaks.decypherInterval);
					//interval function so we arent doing a million ajax calles while the user types
					Drupal7Tweaks.decypherInterval = setTimeout(Drupal7Tweaks.activateDecypher, 200, $(this));
				});
			});
			//function for keydown or focus to process the text
			Drupal7Tweaks.activateDecypher = function(element) {
				//our text and code string
				var code = element.val();
				//give our code linebreaks
				//var code_with_linebreaks = code.replace(/\n/g, "<br />");
				var code_with_linebreaks = code.replace(/\n/g, "<br />");
				//our post code
				var postCode = '&code='+ encodeURIComponent(code_with_linebreaks);
				//Drupal7Tweaks.preview.html(code_with_linebreaks);
				//post our data to our ajax function
				Drupal7Tweaks.decypherCode(postCode, Drupal7Tweaks.preview);
			}
			//ajax function for decyphering the comments and code
			Drupal7Tweaks.decypherCode = function(code, element) {
				//pos tthe code to our php function
				$.post('/ajax/decypher', code, function(data) {
					//insert the decyphered text into the dialog 
					element.html(data);
					//highlight the code if any
					SyntaxHighlighter.highlight();
				});
			}
			//function for injucting our code prefix and suffix into the textarea
			$('#insert-code-button', context).click(function(e){
				e.preventDefault();
				var textarea = $("#edit-field-text-and-code-und-0-value", context);
				var current_text = textarea.val();
				var code = "\r" + "\t" + '{code}{/code}';
				$("#edit-field-text-and-code-und-0-value", context).val(current_text + code);
			});
		}
	};
})(jQuery);