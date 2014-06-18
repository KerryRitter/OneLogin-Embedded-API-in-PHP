#OneLogin Embedded API in PHP
This class allows you to easily cURL and embed OneLogin apps in your PHP website using a simple configuration change and templating.

##Templating
Templating is similar to Handlebars, but is very specific. The variables allowed are the following:   

### Apps Template
* {{apps}} - this is where each app is rendered using the applied app_template.

### App Template 
* {{icon}} - this is the URL for the app's icon
* {{id}} - this is the app's ID. This can be used with the OneLogin's Launch API (shown in example)
* {{name}} - this is the name of the app.
* {{provisioned-class}} - if the app is provisioned, it outputs "provisioned"; otherwise, it outputs "not-provisioned". This will allow you to display and hide icons with CSS.
* {{extension-required-class}} - if the app requires an extension, it outputs "extension-required"; otherwise, it outputs "no-extension-required". This will allow you to display and hide icons with CSS.

## Example of use
This example gets the current WordPress user and gets their apps (Note: at this point, the user has already logged in to OneLogin, so we do not need to remotely authenticate).

	<?php
	$current_user = wp_get_current_user();

	$apps_template = '<ul>{{apps}}</ul>';

	$app_template = '<li class="one-login-app {{provisioned-class}} {{extension-required-class}}">
						<img src="{{icon}}">
						<div class="provisioned-icon"></div>
						<div class="extension-required-icon"></div>
						<h3><a href="https://app.onelogin.com/launch/{{id}}">{{name}}</a></h3>
					</li>';

	$oleApi = new OneLoginEmbeddedApi();
	$apps = $oleApi->get_apps_for_user($current_user->user_email);
	echo $apps->toHtml($apps_template, $app_template);
	?>