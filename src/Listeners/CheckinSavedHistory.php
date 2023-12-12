<?php

namespace Mattoid\MoneyHistoryAuto\Listeners;

use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;
use Ziven\checkin\Event\checkinUpdated;
use Illuminate\Contracts\Events\Dispatcher;

class CheckinSavedHistory
{
    private $source = "CHECKINSAVED";
    private $sourceDesc = "签到奖励";

    private $events;
    private $settings;

    public function __construct(Dispatcher $events, SettingsRepositoryInterface $settings, Translator $translator)
    {
        $this->events = $events;
        $this->settings = $settings;
        $this->sourceDesc = $translator->trans("mattoid-money-history-auto.forum.checkin-saved");
    }

    public function handle(checkinUpdated $checkin) {
        $checkinRewardMoney = (float)$this->settings->get('ziven-forum-checkin.checkinRewardMoney', 0);

        $this->events->dispatch(new MoneyHistoryEvent($checkin->user, $checkinRewardMoney, $this->source, $this->sourceDesc));
    }
}
