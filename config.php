<?php if (!defined('PLX_ROOT')) exit; ?>
<?php
// Controle du token du formulaire
plxToken::validateFormToken($_POST);

$plugin_title = $plxPlugin->getInfo('title');
$plugin_admin_url = 'parametres_plugin.php?p=' . rawurlencode($plugin_title);
if (!empty($_POST['save'])) {
    // Transation de post et sauvegarde des paramètres
    $plxPlugin->setParam('opengraph_enabled', (bool) $_POST['opengraph_enabled'], 'numeric');
    $plxPlugin->setParam('schemaorg_enabled', (bool) $_POST['schemaorg_enabled'], 'numeric');
    $plxPlugin->setParam('twittercard_enabled', (bool) $_POST['twittercard_enabled'], 'numeric');
    $plxPlugin->setParam('twittercard_type', (string) $_POST['twittercard_type'], 'string');
    $plxPlugin->setParam('twittercard_site', (string) $_POST['twittercard_site'], 'string');
    $plxPlugin->setParam('twittercard_creator', (string) $_POST['twittercard_creator'], 'string');
    $plxPlugin->saveParams();
    header('Location: ' . $plugin_admin_url);
    exit;
}
// Génération du formulaire de configuration
?>

<h2><?php echo plxUtils::strCheck($plugin_title) ?></h2>

<form action="<?php echo plxUtils::strCheck($plugin_admin_url); ?>" method="post">
    <fieldset>
        <legend><?php $plxPlugin->lang('L_OPENGRAPH_TITLE') ?></legend>
        <p>
            <a href="https://developers.facebook.com/docs/opengraph"><?php $plxPlugin->lang('L_DOCUMENTATION') ?></a>
            <a href="https://developers.facebook.com/tools/debug"><?php $plxPlugin->lang('L_VALIDATOR') ?></a>
        </p>
        <p>
            <?php $plxPlugin->lang('L_OPENGRAPH_SUMMARY') ?>
        </p>
        <p>
            <input type="radio" value="1" name="opengraph_enabled" id="opengraph_enabled"<?php echo ($plxPlugin->getParam('opengraph_enabled') ? ' checked="checked"' : ''); ?>/>
            <label for="opengraph_enabled"><?php $plxPlugin->lang('L_OPENGRAPH_ENABLED') ?></label>
            <input type="radio" value="0" name="opengraph_enabled" id="opengraph_disabled"<?php echo ($plxPlugin->getParam('opengraph_enabled') ? '' : ' checked="checked"'); ?>/>
            <label for="opengraph_disabled"><?php $plxPlugin->lang('L_OPENGRAPH_DISABLED') ?></label>
        </p>
    </fieldset>
    <fieldset>
        <legend><?php $plxPlugin->lang('L_SCHEMAORG_TITLE') ?></legend>
        <p>
            <a href="https://developers.google.com/+/web/snippet/"><?php $plxPlugin->lang('L_DOCUMENTATION') ?></a>
            <a href="http://www.google.com/webmasters/tools/richsnippets"><?php $plxPlugin->lang('L_VALIDATOR') ?></a>
        </p>
        <p>
            <?php $plxPlugin->lang('L_SCHEMAORG_SUMMARY') ?>
        </p>
        <p>
            <input type="radio" value="1" name="schemaorg_enabled" id="schemaorg_enabled"<?php echo ($plxPlugin->getParam('schemaorg_enabled') ? ' checked="checked"' : ''); ?>/>
            <label for="schemaorg_enabled"><?php $plxPlugin->lang('L_SCHEMAORG_ENABLED') ?></label>
            <input type="radio" value="0" name="schemaorg_enabled" id="schemaorg_disabled"<?php echo ($plxPlugin->getParam('schemaorg_enabled') ? '' : ' checked="checked"'); ?>/>
            <label for="schemaorg_disabled"><?php $plxPlugin->lang('L_SCHEMAORG_DISABLED') ?></label>
        </p>
    </fieldset>
    <fieldset>
        <legend><?php $plxPlugin->lang('L_TWITTERCARD_TITLE') ?></legend>
        <p>
            <a href="https://dev.twitter.com/cards/overview"><?php $plxPlugin->lang('L_DOCUMENTATION') ?></a>
            <a href="https://cards-dev.twitter.com/validator"><?php $plxPlugin->lang('L_VALIDATOR') ?></a>
        </p>
        <p>
            <?php $plxPlugin->lang('L_TWITTERCARD_SUMMARY') ?>
        </p>
        <p>
            <input type="radio" value="1" name="twittercard_enabled" id="twittercard_enabled"<?php echo ($plxPlugin->getParam('twittercard_enabled') ? ' checked="checked"' : ''); ?>/>
            <label for="twittercard_enabled"><?php $plxPlugin->lang('L_TWITTERCARD_ENABLED') ?></label>
            <input type="radio" value="0" name="twittercard_enabled" id="twittercard_disabled"<?php echo ($plxPlugin->getParam('twittercard_enabled') ? '' : ' checked="checked"'); ?>/>
            <label for="twittercard_disabled"><?php $plxPlugin->lang('L_TWITTERCARD_DISABLED') ?></label>
        </p>
        <dl>
            <dt><?php $plxPlugin->lang('L_TWITTERCARD_TYPE') ?></dt>
            <dd>
                <input type="radio" value="summary" name="twittercard_type" id="twittercard_type_summary"<?php echo ($plxPlugin->getParam('twittercard_type') === 'summary' ? ' checked="checked"' : ''); ?>>
                <label for="twittercard_type_summary" style="float:none"><?php $plxPlugin->lang('L_TWITTERCARD_TYPE_SUMMARY') ?></label>
            </dd>
            <dd>
                <input type="radio" value="summary_large_image" name="twittercard_type" id="twittercard_type_summary_large_image"<?php echo ($plxPlugin->getParam('twittercard_type') === 'summary_large_image' ? ' checked="checked"' : ''); ?>>
                <label for="twittercard_type_summary_large_image" style="float:none"><?php $plxPlugin->lang('L_TWITTERCARD_TYPE_SUMMARY_LARGE_IMAGE') ?></label>
            </dd>
        </dl>
        <p>
            <label for="twittercard_site"><?php $plxPlugin->lang('L_TWITTERCARD_SITE') ?> (twitter:site)</label>
            @<input type="text" name="twittercard_site" id="twittercard_site" value="<?php echo $plxPlugin->getParam('twittercard_site') ?>">
        </p>
        <p>
            <label for="twittercard_creator"><?php $plxPlugin->lang('L_TWITTERCARD_CREATOR') ?> (twitter:creator)</label>
            @<input type="text" name="twittercard_creator" id="twittercard_creator" value="<?php echo $plxPlugin->getParam('twittercard_creator') ?>">
        </p>
    </fieldset>
    <p>
        <?php echo plxToken::getTokenPostMethod() ?>
        <input type="submit" name="save" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
    </p>
</form>
