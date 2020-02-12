<?php

if (array_key_exists('name', $this->data['dstMetadata'])) {
    $dstName = $this->data['dstMetadata']['name'];
} elseif (array_key_exists('OrganizationDisplayName', $this->data['dstMetadata'])) {
    $dstName = $this->data['dstMetadata']['OrganizationDisplayName'];
} else {
    $dstName = $this->data['dstMetadata']['entityid'];
}
if (is_array($dstName)) {
    $dstName = $this->t($dstName);
}
$dstName = htmlspecialchars($dstName);


$this->data['header'] = $this->t('{consent:consent:noconsent_title}');;

$this->includeAtTemplateBase('includes/header.php');

echo '<p>' . $this->t('{consent:consent:noconsent_text}') . '</p>';

if ($this->data['resumeFrom']) {
    echo('<a class="btn btn-primary" href="' . htmlspecialchars($this->data['resumeFrom']) . '">');
    echo($this->t('{consent:consent:noconsent_return}'));
    echo('</a>');
}

echo('<a class="btn btn-default" href="' . htmlspecialchars($this->data['logoutLink']) . '">' . $this->t('{consent:consent:abort}') . '</a>');

$this->includeAtTemplateBase('includes/footer.php');
