<?php defined('APPLICATION') or die;
if ($this->data('hasArguments')) {
    $linkSuggestion = Gdn_Theme::link($this->SelfUrl, 'no parameters');
} else {
    $linkSuggestion = Gdn_Theme::link($this->SelfUrl.'/Anonymous', 'parameters');
}
?>
<h2>Hello <?= $this->data('name') ?>!</h2>
<div class="p">Now try using this page with <?= $linkSuggestion ?>.</p>
<div class="p">Isn't that awesome?</div>
