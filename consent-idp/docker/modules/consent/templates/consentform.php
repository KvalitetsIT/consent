<?php
/**
 * Template form for giving consent.
 *
 * Parameters:
 * - 'srcMetadata': Metadata/configuration for the source.
 * - 'dstMetadata': Metadata/configuration for the destination.
 * - 'yesTarget': Target URL for the yes-button. This URL will receive a POST request.
 * - 'yesData': Parameters which should be included in the yes-request.
 * - 'noTarget': Target URL for the no-button. This URL will receive a GET request.
 * - 'noData': Parameters which should be included in the no-request.
 * - 'attributes': The attributes which are about to be released.
 * - 'sppp': URL to the privacy policy of the destination, or FALSE.
 *
 * @package simpleSAMLphp
 */
assert('is_array($this->data["srcMetadata"])');
assert('is_array($this->data["dstMetadata"])');
assert('is_string($this->data["yesTarget"])');
assert('is_array($this->data["yesData"])');
assert('is_string($this->data["noTarget"])');
assert('is_array($this->data["noData"])');
assert('is_array($this->data["attributes"])');
assert('is_array($this->data["hiddenAttributes"])');
assert('$this->data["sppp"] === false || is_string($this->data["sppp"])');

// Parse parameters
if (array_key_exists('name', $this->data['srcMetadata'])) {
    $srcName = $this->data['srcMetadata']['name'];
} elseif (array_key_exists('OrganizationDisplayName', $this->data['srcMetadata'])) {
    $srcName = $this->data['srcMetadata']['OrganizationDisplayName'];
} else {
    $srcName = $this->data['srcMetadata']['entityid'];
}

if (is_array($srcName)) {
    $srcName = $this->t($srcName);
}

$srcName = htmlspecialchars($srcName);

$dstName = $this->data['dstName'];

$attributes = $this->data['attributes'];

$pdfurl = "../../getconsenttemplate.php?StateId=" . $_REQUEST['StateId'];

$headerTitle = $this->t('{consent:consent:consent_title}');
require "header.php";
?>
<div class="pin-input-container">
    <div class="center-content button-container">
        <h6 class="mdc-typography--headline6"><?php echo $this->t('{consent:consent:header}') ?></h6>
        <p class="mdc-typography--body1"><?php echo $this->t($this->data['acceptText'], array('SPNAME' => $dstName)) ?></p>

        <a target='_blank' id='seeconsentbutton' class='mdc-button mdc-button--unelevated large-button'
           href="<?php echo 'pdfjs/web/viewer.html?file=' . urlencode($pdfurl) ?>"><?php echo $this->t("{consent:consent:seeconsent}") ?></a>
    </div>
</div>
</div
<div class="center-content">
    <form action="<?php echo htmlspecialchars($this->data['yesTarget']); ?>" class="center-content">
        <?php
        // Embed hidden fields...
        foreach ($this->data['yesData'] as $name => $value) {
            echo '<input type="hidden" name="' . htmlspecialchars($name) .
                '" value="' . htmlspecialchars($value) . '" />';
        }
        ?>
        <button type="submit" class="mdc-button mdc-button--unelevated large-button" name="yes" id="yesbutton"
                value="">
            <div class="mdc-button__ripple"></div>
            <span class="mdc-button__label"><?php echo htmlspecialchars($this->t('{consent:consent:yes}')) ?></span>
        </button>
    </form>

    <form action="<?php echo htmlspecialchars($this->data['noTarget']); ?>" method="get" class="center-content">
        <?php
        foreach ($this->data['noData'] as $name => $value) {
            echo('<input type="hidden" name="' . htmlspecialchars($name) .
                '" value="' . htmlspecialchars($value) . '" />');
        }
        ?>
        <button type="submit" class="mdc-button mdc-button--outlined large-button" name="no" id="nobutton"
                value="">
            <div class="mdc-button__ripple"></div>
            <span class="mdc-button__label"><?php echo htmlspecialchars($this->t('{consent:consent:no}')) ?></span>
        </button>
    </form>
</div>

<script>
    $(document).ready(function () {
        $('#yesbutton').prop('disabled', true);

        $('#seeconsentbutton').click(function () {
            $('#yesbutton').prop('disabled', false);
        });
    });
</script>

<?php require "header.php"; ?>
