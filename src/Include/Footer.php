<?php

use ChurchCRM\Authentication\AuthenticationManager;
use ChurchCRM\dto\ChurchMetaData;
use ChurchCRM\dto\SystemURLs;
use ChurchCRM\Plugin\PluginManager;
use ChurchCRM\Service\SystemService;

use ChurchCRM\Utils\InputUtils;
$isAdmin = AuthenticationManager::getCurrentUser()->isAdmin();
?>
      </div><!-- /.container-xl -->
    </div><!-- /.page-body -->

    <footer class="footer footer-transparent d-print-none gel-footer">
      <div class="container-xl">
        <div class="d-flex flex-column flex-md-row gap-2 justify-content-between align-items-center">
          <div>
            &copy; <?= SystemService::getCopyrightDate() ?>
            <?= InputUtils::escapeHTML(ChurchMetaData::getChurchName() ?: 'ChurchCRM') ?>
          </div>
          <div>
            <?= gettext('Powered by') ?>
            <a href="<?= InputUtils::escapeAttribute(SystemURLs::attributed('https://churchcrm.io', 'footer')) ?>" target="_blank" rel="noopener noreferrer">ChurchCRM</a>
            <span class="ms-1"><?= InputUtils::escapeHTML((string) ($_SESSION['sSoftwareInstalledVersion'] ?? '')) ?></span>
          </div>
        </div>
      </div>
    </footer>

  </div><!-- /.page-wrapper -->

  <!-- Floating Action Buttons -->
  <div class="fab-container" id="fab-container">
    <?php if (AuthenticationManager::getCurrentUser()->isAddRecordsEnabled()): ?>
    <a href="<?= SystemURLs::getRootPath() ?>/PersonEditor.php" class="fab-button fab-person">
      <span class="fab-label" id="fab-person-label"></span>
      <div class="fab-icon">
        <i class="fa-solid fa-person-half-dress"></i>
      </div>
    </a>
    <a href="<?= SystemURLs::getRootPath() ?>/FamilyEditor.php" class="fab-button fab-family">
      <span class="fab-label" id="fab-family-label"></span>
      <div class="fab-icon">
        <i class="fa-solid fa-people-roof"></i>
      </div>
    </a>
    <?php endif; ?>
    <button class="fab-menu-toggle d-xl-none" id="fab-menu-toggle" type="button"
            data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false"
            aria-label="<?= gettext('Toggle navigation') ?>">
      <i class="fa-solid fa-bars"></i>
    </button>
  </div>

</div><!-- /.page -->

<?php if (isset($sGlobalMessage) && !empty($sGlobalMessage)) { ?>
    <script nonce="<?= SystemURLs::getCSPNonce() ?>">
        $("document").ready(function () {
            showGlobalMessage(<?= InputUtils::jsonEncodeForScript($sGlobalMessage) ?>, <?= InputUtils::jsonEncodeForScript($sGlobalMessageClass) ?>);
        });
    </script>
<?php } ?>

<?= PluginManager::getPluginFooterContent() ?>
</body>
</html>
<?php

// Turn OFF output buffering
ob_end_flush();
