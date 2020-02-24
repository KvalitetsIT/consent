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

$headerTitle = $this->t('{consent:consent:noconsent_title}');
require "header.php";
?>

<div id="mainContainer" class="main-container">
    <div class="center-content">
        <h6 class="mdc-typography--headline6"><?php echo $this->t('{consent:consent:header}') ?></h6>
        <p class="mdc-typography--body1 left"><?php echo $this->t('{consent:consent:noconsent_text}') ?></p>
    </div>
    <div class="center-content button-container">
        <?php if ($this->data['resumeFrom']) { ?>
            <a class="mdc-button mdc-button--unelevated large-button" href="<?php echo htmlspecialchars($this->data['resumeFrom']) ?>">
                <div class="mdc-button__ripple"></div>
                <span class="mdc-button__label"><?php echo $this->t('{consent:consent:noconsent_return}') ?></span>
            </a>
        <?php } ?>
        <a class="mdc-button mdc-button--outlined large-button" href="<?php echo htmlspecialchars($this->data['logoutLink']) ?>">
            <div class="mdc-button__ripple"></div>
            <span class="mdc-button__label"><?php echo $this->t('{consent:consent:abort}') ?></span>
        </a>
    </div>
</div>

<?php require "header.php"; ?>
