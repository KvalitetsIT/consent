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

$headerTitle = $this->t('{consent:consent:consent_title}');
require "header.php";
?>
<div class="pin-input-container">
    <div>
        <?php echo getcwd()?>
        <h6 class="mdc-typography--headline6"><?php echo $this->t('{consent:consent:header}') ?></h6>
        <p class="mdc-typography--body1"><?php echo $this->t($this->data['acceptText'], array('SPNAME' => $dstName)) ?></p>
    </div>
</div>
<div class="button-container">
    <button onclick="" class="mdc-button mdc-button--unelevated large-button">
        <div class="mdc-button__ripple"></div>
        <span class="mdc-button__label"><?php echo $this->t('{activationmodule:activation:usevenliglogin}') ?></span>
    </button>
    <button onclick="" class="mdc-button mdc-button--outlined large-button">
        <div class="mdc-button__ripple"></div>
        <span class="mdc-button__label"><?php echo $this->t('{activationmodule:activation:donotusevenliglogin}') ?></span>
    </button>
</div>
</div>
    <p>
        <?php
        echo $this->t($this->data['acceptText'], array('SPNAME' => $dstName));

        if (array_key_exists('descr_purpose', $this->data['dstMetadata'])) {
            echo '</p><p>' . $this->t(
                    '{consent:consent:consent_purpose}',
                    array(
                        'SPNAME' => $dstName,
                        'SPDESC' => $this->getTranslation(
                            SimpleSAML_Utilities::arrayize(
                                $this->data['dstMetadata']['descr_purpose'],
                                'en'
                            )
                        ),
                    )
                );
        }
        ?>
    </p>

    <form style="display: inline; margin: 0px; padding: 0px"
          action="<?php echo htmlspecialchars($this->data['yesTarget']); ?>">
        <p style="margin: 1em">

            <?php
            // Embed hidden fields...
            foreach ($this->data['yesData'] as $name => $value) {
                echo '<input type="hidden" name="' . htmlspecialchars($name) .
                    '" value="' . htmlspecialchars($value) . '" />';
            }
            ?>
        </p>
        <input type="submit" name="yes" class="btn btn-primary" id="yesbutton"
               value="<?php echo htmlspecialchars($this->t('{consent:consent:yes}')) ?>"/>
    </form>

    <form style="display: inline; margin-left: .5em;" action="<?php echo htmlspecialchars($this->data['noTarget']); ?>"
          method="get">

        <?php
        foreach ($this->data['noData'] as $name => $value) {
            echo('<input type="hidden" name="' . htmlspecialchars($name) .
                '" value="' . htmlspecialchars($value) . '" />');
        }
        ?>
        <input type="submit" style="display: inline" class="btn btn-default" name="no" id="nobutton"
               value="<?php echo htmlspecialchars($this->t('{consent:consent:no}')) ?>"/>
    </form>

    <script>
        $(document).ready(function () {
            $('#yesbutton').prop('disabled', true);

            $('#seeconsentbutton').click(function () {
                $('#yesbutton').prop('disabled', false);
            });

        });


    </script>

<?php

$pdfurl = "../../getconsenttemplate.php?StateId=" . $_REQUEST['StateId'];

echo "<a target='_blank' id='seeconsentbutton' class='btn btn-default' href='pdfjs/web/viewer.html?file=" . urlencode($pdfurl) . "'>" . $this->t('{consent:consent:seeconsent}') . "</a>";

$this->includeAtTemplateBase('includes/footer.php');

?>
