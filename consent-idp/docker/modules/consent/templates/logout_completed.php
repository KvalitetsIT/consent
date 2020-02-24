<?php

$this->data['header'] = $this->t('{consent:consent:logged_out_title}');
$this->includeAtTemplateBase('includes/header.php');

echo('<p>' . $this->t('{consent:consent:logged_out_text}') . '</p>');

$this->includeAtTemplateBase('includes/footer.php');