<?php

namespace ChurchCRM\Service;

use ChurchCRM\model\ChurchCRM\EventAttendQuery;
use ChurchCRM\model\ChurchCRM\EventQuery;
use ChurchCRM\model\ChurchCRM\PersonQuery;
use Propel\Runtime\ActiveQuery\Criteria;

/**
 * Trend figures for the main dashboard, read from existing records only.
 */
class DashboardMetricsService
{
    private const MONTHS = 12;

    /**
     * @return array{labels: string[], values: int[]}
     */
    public function newPeoplePerMonth(): array
    {
        return $this->perMonth(fn (\DateTimeImmutable $from, \DateTimeImmutable $to): int => PersonQuery::create()
            ->filterByDateEntered(['min' => $from, 'max' => $to])
            ->count());
    }

    /**
     * Attendance records grouped by the month of the event they belong to.
     *
     * @return array{labels: string[], values: int[]}
     */
    public function attendancePerMonth(): array
    {
        return $this->perMonth(fn (\DateTimeImmutable $from, \DateTimeImmutable $to): int => EventAttendQuery::create()
            ->useEventQuery()
                ->filterByInActive(0)
                ->filterByStart(['min' => $from, 'max' => $to])
            ->endUse()
            ->count());
    }

    /**
     * @return array{current: int, previous: int}
     */
    public function newPeopleLast30Days(): array
    {
        $now = new \DateTimeImmutable();

        return [
            'current'  => PersonQuery::create()->filterByDateEntered(['min' => $now->modify('-30 days')])->count(),
            'previous' => PersonQuery::create()->filterByDateEntered(['min' => $now->modify('-60 days'), 'max' => $now->modify('-30 days')])->count(),
        ];
    }

    /**
     * @return array<int, array{id: int, title: string, start: \DateTimeInterface}>
     */
    public function upcomingEvents(int $limit = 5): array
    {
        $events = EventQuery::create()
            ->filterByInActive(0)
            ->filterByStart(new \DateTimeImmutable(), Criteria::GREATER_EQUAL)
            ->orderByStart()
            ->limit($limit)
            ->find();

        $rows = [];
        foreach ($events as $event) {
            $rows[] = [
                'id'    => (int) $event->getId(),
                'title' => (string) $event->getTitle(),
                'start' => $event->getStart(),
            ];
        }

        return $rows;
    }

    /**
     * @param callable(\DateTimeImmutable, \DateTimeImmutable): int $count
     * @return array{labels: string[], values: int[]}
     */
    private function perMonth(callable $count): array
    {
        $firstOfMonth = new \DateTimeImmutable('first day of this month 00:00:00');
        $labels = [];
        $values = [];
        for ($i = self::MONTHS - 1; $i >= 0; $i--) {
            $from = $firstOfMonth->modify("-{$i} months");
            $to = $from->modify('+1 month')->modify('-1 second');
            $labels[] = $from->format('Y-m');
            $values[] = $count($from, $to);
        }

        return ['labels' => $labels, 'values' => $values];
    }
}
