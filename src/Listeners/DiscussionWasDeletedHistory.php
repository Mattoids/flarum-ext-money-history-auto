<?php

namespace Mattoid\MoneyHistoryAuto\Listeners;

use AntoineFr\Money\Listeners\AutoRemoveEnum;
use Flarum\Locale\Translator;
use Flarum\Discussion\Event\Deleted as DiscussionDeleted;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;

class DiscussionWasDeletedHistory
{
    private $source = "DISCUSSIONWASDELETED";
    private $sourceDesc = "删帖扣除";

    private $events;
    private $settings;
    private $autoremove;

    public function __construct(Dispatcher $events, SettingsRepositoryInterface $settings, Translator $translator)
    {
        $this->events = $events;
        $this->settings = $settings;

        $this->sourceDesc = $translator->trans("mattoid-money-history-auto.forum.discussion-was-deleted");
        $this->autoremove = (int)$this->settings->get('antoinefr-money.autoremove', 1);
    }

    public function handle(DiscussionDeleted $event) {
        if ($this->autoremove == AutoRemoveEnum::DELETED) {
            $money = (float)$this->settings->get('antoinefr-money.moneyfordiscussion', 0);

            $this->events->dispatch(new MoneyHistoryEvent($event->discussion->user, -$money, $this->source, $this->sourceDesc));
        }
    }
}
