<?php

use ChurchCRM\dto\ChurchMetaData;
use ChurchCRM\dto\SystemURLs;
use ChurchCRM\Plugin\PluginManager;
use ChurchCRM\Service\SystemService;
use ChurchCRM\Utils\InputUtils;
?>

<div class="auth-footer">
  <div>
    &copy; <?= SystemService::getCopyrightDate() ?> <strong><?= InputUtils::escapeHTML(ChurchMetaData::getChurchName() ?: 'ChurchCRM') ?></strong>
    &middot; <?= gettext('Powered by') ?>
    <a href="<?= InputUtils::escapeAttribute(SystemURLs::attributed('https://churchcrm.io', 'login_footer')) ?>" target="_blank" rel="noopener noreferrer">ChurchCRM</a>
  </div>
  <?php $churchSocialLinks = ChurchMetaData::getChurchSocialLinks(); ?>
  <?php if ($churchSocialLinks !== []): ?>
  <div class="auth-footer-social">
    <?php foreach ($churchSocialLinks as $link): ?>
    <a href="<?= InputUtils::escapeAttribute($link['url']) ?>" target="_blank" rel="noopener noreferrer"
       title="<?= InputUtils::escapeAttribute($link['label']) ?>" aria-label="<?= InputUtils::escapeAttribute($link['label']) ?>">
      <i class="<?= InputUtils::escapeAttribute($link['icon']) ?>" aria-hidden="true"></i>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

  <?= PluginManager::getPluginFooterContent() ?>
</body>
</html>
