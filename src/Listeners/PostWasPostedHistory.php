<?php

namespace Mattoid\MoneyHistoryAuto\Listeners;

use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Post\Event\Posted;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;

class PostWasPostedHistory
{
    private $source = "POSTWASPOSTED";
    private $sourceKey;
    private $sourceDesc = "回帖奖励";

    private $events;
    private $settings;
    private $autoremove;

    public function __construct(Dispatcher $events, SettingsRepositoryInterface $settings, Translator $translator)
    {
        $this->events = $events;
        $this->settings = $settings;

        $this->sourceKey = "mattoid-money-history-auto.forum.post-was-posted";
        $this->sourceDesc = $translator->trans("mattoid-money-history-auto.forum.post-was-posted");
        $this->autoremove = (int)$this->settings->get('antoinefr-money.autoremove', 1);
    }

    public function handle(Posted $event) {
        if ($event->post['number'] > 1) {
            $minimumLength = (int)$this->settings->get('antoinefr-money.postminimumlength', 0);

            if (strlen($event->post->content) >= $minimumLength) {
                $money = (float)$this->settings->get('antoinefr-money.moneyforpost', 0);

                $rewarded = $this->settings->get("mattoid-money-history-auto.privateChatsAreNotRewarded", 0);
                if ($rewarded && $event->post->discussion->is_private) {
                    $user = $event->actor;
                    $user->money -= $money;
                    $user->save();
                } else {
                    $this->events->dispatch(new MoneyHistoryEvent($event->actor, $money, $this->source, $this->sourceDesc, $this->sourceKey));
                }
            }
        }
    }
}
