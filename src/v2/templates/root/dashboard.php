<?php

use ChurchCRM\dto\SystemURLs;
use ChurchCRM\Utils\DateTimeUtils;
use ChurchCRM\Utils\InputUtils;

require SystemURLs::getDocumentRoot() . '/Include/Header.php';

?>

<?php if ($canReviewSelfRegistrations) { ?>
<div class="alert alert-warning d-none flex-wrap gap-2 align-items-center justify-content-between" id="selfRegisterDashboardAlert" role="status">
    <div>
        <i class="fa-solid fa-user-clock me-2"></i>
        <strong id="selfRegisterDashboardCount">0</strong>
        <?= gettext('self-registrations are waiting for review') ?>
    </div>
    <a href="<?= $sRootPath ?>/people/self-register" class="btn btn-sm btn-warning">
        <?= gettext('Review now') ?>
    </a>
</div>
<?php } ?>

<?php
$statCard = function (string $id, string $label, int $value, string $icon, string $tone, string $url, string $meta = ''): void {
    ?>
    <a href="<?= $url ?>" class="card card-sm gel-stat h-100 text-decoration-none">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between gap-2">
                <div class="min-w-0">
                    <div class="gel-stat-label"><?= InputUtils::escapeHTML($label) ?></div>
                    <div class="gel-stat-value" id="<?= $id ?>"><?= $value ?></div>
                    <?php if ($meta !== ''): ?><div class="gel-stat-meta"><?= $meta ?></div><?php endif; ?>
                </div>
                <span class="gel-stat-icon gel-tone-<?= $tone ?>" aria-hidden="true"><i class="<?= $icon ?>"></i></span>
            </div>
        </div>
    </a>
    <?php
};
$statDisabled = function (string $label, string $icon, string $tone): void {
    ?>
    <div class="card card-sm gel-stat h-100 gel-stat-disabled">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between gap-2">
                <div>
                    <div class="gel-stat-label"><?= InputUtils::escapeHTML($label) ?></div>
                    <div class="gel-stat-meta"><?= gettext('Disabled in settings') ?></div>
                </div>
                <span class="gel-stat-icon gel-tone-<?= $tone ?>" aria-hidden="true"><i class="<?= $icon ?>"></i></span>
            </div>
        </div>
    </div>
    <?php
};
$newPeopleDelta = $newPeople['current'] - $newPeople['previous'];
$newPeopleTrend = $newPeopleDelta > 0 ? 'up' : ($newPeopleDelta < 0 ? 'down' : 'flat');
$newPeopleMeta = '<span class="gel-trend gel-trend-' . $newPeopleTrend . '" title="'
    . InputUtils::escapeAttribute(sprintf(gettext('%d in the previous 30 days'), $newPeople['previous'])) . '">'
    . ($newPeopleTrend === 'flat' ? '' : '<i class="fa-solid fa-arrow-' . $newPeopleTrend . '" aria-hidden="true"></i> ')
    . InputUtils::escapeHTML(sprintf(gettext('%d new in 30 days'), $newPeople['current'])) . '</span>';
?>

<!-- Stat Cards Row -->
<div class="row row-cards mb-3 g-3">
    <div class="col-6 col-md-4 col-xl">
        <?php $statCard('familyCountDashboard', gettext('Families'), (int) $dashboardCounts['families'], 'fa-solid fa-people-roof', 'navy', $sRootPath . '/people/family'); ?>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <?php $statCard('peopleStatsDashboard', gettext('People'), (int) $dashboardCounts['People'], 'fa-solid fa-people-group', 'gold', $sRootPath . '/people/list', $newPeopleMeta); ?>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <?php $statCard('groupsCountDashboard', gettext('Groups'), (int) $dashboardCounts['Groups'], 'fa-solid fa-users', 'navy', $sRootPath . '/groups/dashboard'); ?>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <?php if ($sundaySchoolEnabled): ?>
            <?php $statCard('groupStatsSundaySchool', gettext('Sunday School'), (int) $dashboardCounts['SundaySchool'], 'fa-solid fa-child', 'success', $sRootPath . '/groups/sundayschool'); ?>
        <?php else: ?>
            <?php $statDisabled(gettext('No Sunday School'), 'fa-solid fa-child', 'success'); ?>
        <?php endif; ?>
    </div>
    <div class="col-12 col-md-4 col-xl">
        <?php if ($eventsEnabled): ?>
            <?php $statCard('checkinsCountDashboard', gettext('Check-ins'), (int) $dashboardCounts['events'], 'fa-regular fa-calendar-check', 'info', $sRootPath . '/event/dashboard', InputUtils::escapeHTML(gettext('Checked in now'))); ?>
        <?php else: ?>
            <?php $statDisabled(gettext('No Check-ins'), 'fa-regular fa-calendar-check', 'info'); ?>
        <?php endif; ?>
    </div>
</div>

<?php
$dataChecks = array_filter([
    ['count' => $genderDataCheckCount, 'label' => gettext('%d missing gender'), 'url' => '/people/list?Gender=0'],
    ['count' => $roleDataCheckCount, 'label' => gettext('%d missing role'), 'url' => '/people/list?FamilyRole=0'],
    ['count' => $classificationDataCheckCount, 'label' => gettext('%d missing classification'), 'url' => '/people/list?Classification=0'],
], fn (array $check): bool => $check['count'] > 0);
?>

<div class="row row-cards mb-3 g-3">
    <div class="<?= empty($quickActions) && empty($dataChecks) ? 'col-12' : 'col-lg-8' ?>">
        <div class="card h-100" id="growthChartCard">
            <div class="card-header">
                <div>
                    <h3 class="card-title"><?= gettext('Membership growth') ?></h3>
                    <div class="card-subtitle text-body-secondary small"><?= gettext('People added each month, last 12 months') ?></div>
                </div>
            </div>
            <div class="card-body">
                <div id="growthChart" class="gel-chart" role="img" aria-label="<?= InputUtils::escapeAttribute(gettext('Membership growth')) ?>"></div>
            </div>
        </div>
    </div>
    <?php if (!empty($quickActions) || !empty($dataChecks)): ?>
    <div class="col-lg-4">
        <?php if (!empty($quickActions)): ?>
        <div class="card mb-3" id="quickActionsCard">
            <div class="card-header">
                <h3 class="card-title"><?= gettext('Quick actions') ?></h3>
            </div>
            <div class="card-body">
                <div class="gel-quick-actions">
                    <?php foreach ($quickActions as $action): ?>
                    <a href="<?= $sRootPath . $action['url'] ?>" class="gel-quick-action">
                        <span class="gel-quick-action-icon" aria-hidden="true"><i class="fa-solid <?= $action['icon'] ?>"></i></span>
                        <span><?= InputUtils::escapeHTML($action['label']) ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($dataChecks)): ?>
        <div class="card" id="dataQualityCard">
            <div class="card-header">
                <h3 class="card-title"><i class="fa-solid fa-clipboard-check me-2 text-warning" aria-hidden="true"></i><?= gettext('Needs attention') ?></h3>
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($dataChecks as $check): ?>
                <a href="<?= $sRootPath . $check['url'] ?>" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                    <span><?= InputUtils::escapeHTML(sprintf($check['label'], $check['count'])) ?></span>
                    <i class="fa-solid fa-chevron-right text-body-secondary small" aria-hidden="true"></i>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php if ($canViewEvents) { ?>
<div class="row row-cards mb-3 g-3">
    <div class="col-lg-8">
        <div class="card h-100" id="attendanceChartCard">
            <div class="card-header">
                <div>
                    <h3 class="card-title"><?= gettext('Attendance') ?></h3>
                    <div class="card-subtitle text-body-secondary small"><?= gettext('Attendance records per month, by event date') ?></div>
                </div>
            </div>
            <div class="card-body">
                <div id="attendanceChart" class="gel-chart" role="img" aria-label="<?= InputUtils::escapeAttribute(gettext('Attendance')) ?>"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100" id="upcomingEventsCard">
            <div class="card-header">
                <h3 class="card-title"><?= gettext('Upcoming events') ?></h3>
                <div class="card-actions">
                    <a href="<?= $sRootPath ?>/event/calendars" class="btn btn-sm btn-ghost-secondary"><?= gettext('Calendar') ?></a>
                </div>
            </div>
            <?php if (empty($upcomingEvents)): ?>
            <div class="card-body">
                <div class="empty py-3">
                    <div class="empty-icon"><i class="fa-regular fa-calendar fa-2x text-body-secondary" aria-hidden="true"></i></div>
                    <p class="empty-title"><?= gettext('No upcoming events') ?></p>
                </div>
            </div>
            <?php else: ?>
            <div class="list-group list-group-flush">
                <?php
                $monthNames = [1 => gettext('January'), gettext('February'), gettext('March'), gettext('April'), gettext('May'), gettext('June'),
                    gettext('July'), gettext('August'), gettext('September'), gettext('October'), gettext('November'), gettext('December')];
                foreach ($upcomingEvents as $event): ?>
                <a href="<?= $sRootPath ?>/event/view/<?= (int) $event['id'] ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
                    <span class="gel-date-chip" aria-hidden="true">
                        <span class="gel-date-chip-month"><?= InputUtils::escapeHTML(mb_substr($monthNames[(int) $event['start']->format('n')], 0, 3)) ?></span>
                        <span class="gel-date-chip-day"><?= $event['start']->format('j') ?></span>
                    </span>
                    <span class="min-w-0">
                        <span class="d-block fw-medium text-truncate text-body"><?= InputUtils::escapeHTML($event['title']) ?></span>
                        <span class="d-block small text-body-secondary"><?= InputUtils::escapeHTML(DateTimeUtils::formatDate($event['start'], true)) ?></span>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php } ?>

<script nonce="<?= SystemURLs::getCSPNonce() ?>">window.CRM.dashboardCharts = <?= InputUtils::jsonEncodeForScript(['growth' => $growthChart, 'attendance' => $attendanceChart]) ?>;</script>

<?php if ($eventsEnabled) { ?>
<div class="row mb-3">
    <div class="col-12">
        <div class="card" id="todayEventsCard">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title"><i class="fa-solid fa-calendar-day me-2"></i><?= gettext("Today's Events") ?></h3>
                <div class="ms-auto">
                    <a href="<?= SystemURLs::getRootPath() ?>/event/checkin" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-clipboard-check me-1"></i><?= gettext('Check-in') ?>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-vcenter table-hover card-table mb-0" width="100%" id="todayEventsDashboardItem"></table>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="row">
    <!-- People card — primary content (2/3 width) -->
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-users me-2 text-body-secondary"></i><?= gettext('People') ?>
                </div>
                <div class="card-options ms-auto">
                    <ul class="nav nav-tabs card-header-tabs" id="people-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="latest-fam-tab" data-bs-toggle="tab" href="#latest-fam-pane" role="tab" aria-controls="latest-fam-pane" aria-selected="true">
                                <i class="fa-solid fa-house-chimney me-1"></i><span class="d-none d-xl-inline"><?= gettext('Latest Families') ?></span><span class="d-xl-none"><?= gettext('New') ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="updated-fam-tab" data-bs-toggle="tab" href="#updated-fam-pane" role="tab" aria-controls="updated-fam-pane" aria-selected="false">
                                <i class="fa-solid fa-house-circle-check me-1"></i><span class="d-none d-xl-inline"><?= gettext('Updated Families') ?></span><span class="d-xl-none"><?= gettext('Updated') ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="latest-ppl-tab" data-bs-toggle="tab" href="#latest-ppl-pane" role="tab" aria-controls="latest-ppl-pane" aria-selected="false">
                                <i class="fa-solid fa-user-plus me-1"></i><span class="d-none d-xl-inline"><?= gettext('Latest People') ?></span><span class="d-xl-none"><?= gettext('New') ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="updated-ppl-tab" data-bs-toggle="tab" href="#updated-ppl-pane" role="tab" aria-controls="updated-ppl-pane" aria-selected="false">
                                <i class="fa-solid fa-user-pen me-1"></i><span class="d-none d-xl-inline"><?= gettext('Updated People') ?></span><span class="d-xl-none"><?= gettext('Updated') ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="tab-content" id="people-tabs-content">
                    <div class="tab-pane fade show active" id="latest-fam-pane" role="tabpanel" aria-labelledby="latest-fam-tab">
                        <table class="table table-vcenter table-hover card-table mb-0" width="100%" id="latestFamiliesDashboardItem"></table>
                    </div>
                    <div class="tab-pane fade" id="updated-fam-pane" role="tabpanel" aria-labelledby="updated-fam-tab">
                        <table class="table table-vcenter table-hover card-table mb-0" width="100%" id="updatedFamiliesDashboardItem"></table>
                    </div>
                    <div class="tab-pane fade" id="latest-ppl-pane" role="tabpanel" aria-labelledby="latest-ppl-tab">
                        <table class="table table-vcenter table-hover card-table mb-0" width="100%" id="latestPersonDashboardItem"></table>
                    </div>
                    <div class="tab-pane fade" id="updated-ppl-pane" role="tabpanel" aria-labelledby="updated-ppl-tab">
                        <table class="table table-vcenter table-hover card-table mb-0" width="100%" id="updatedPersonDashboardItem"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Birthdays & Anniversaries — sidebar (1/3 width) -->
    <div class="col-lg-4">
        <div class="card mb-3" id="birthdayCard">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title"><i class="fa-solid fa-cake-candles me-2"></i><?= gettext('Birthdays') ?></h3>
            </div>
            <div class="card-body p-0">
                <p class="text-body-secondary small px-3 pt-3 mb-2"><?= gettext('Past & next 7 days') ?></p>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" width="100%" id="PersonBirthdayDashboardItem"></table>
                </div>
            </div>
        </div>
        <div class="card mb-3" id="anniversaryCard">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title"><i class="fa-solid fa-heart me-2"></i><?= gettext('Anniversaries') ?></h3>
            </div>
            <div class="card-body p-0">
                <p class="text-body-secondary small px-3 pt-3 mb-2"><?= gettext('Past & next 7 days') ?></p>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" width="100%" id="FamiliesWithAnniversariesDashboardItem"></table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($depositEnabled) { ?>
<div class="row">
    <div class="col-12">
        <div class="card mb-3" id="depositChartRow">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title"><i class="fa-solid fa-circle-dollar-to-slot me-2"></i> <?= gettext('Deposit Tracking') ?></h3>
            </div>
            <div class="card-body">
                <div id="deposit-lineGraph" class="deposit-line-graph"></div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script src="<?= SystemURLs::assetVersioned('/skin/v2/root-dashboard.min.js') ?>"></script>
<?php
require SystemURLs::getDocumentRoot() . '/Include/Footer.php';
