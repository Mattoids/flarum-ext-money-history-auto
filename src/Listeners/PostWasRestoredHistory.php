<?php

namespace Mattoid\MoneyHistoryAuto\Listeners;

use AntoineFr\Money\Listeners\AutoRemoveEnum;
use Flarum\Locale\Translator;
use Flarum\Post\Event\Restored as PostRestored;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;

class PostWasRestoredHistory
{
    private $source = "POSTWASRESTORED";
    private $sourceKey;
    private $sourceDesc = "";

    private $events;
    private $settings;
    private $autoremove;

    public function __construct(Dispatcher $events, SettingsRepositoryInterface $settings, Translator $translator)
    {
        $this->events = $events;
        $this->settings = $settings;

        $this->sourceKey = "mattoid-money-history-auto.forum.source-desc";
        $this->sourceDesc = $translator->trans("mattoid-money-history-auto.forum.source-desc");
        $this->autoremove = (int)$this->settings->get('antoinefr-money.autoremove', 1);
    }

    public function ignoreNotifyingUsers(string $content): string
    {
        if (!$this->settings->get('antoinefr-money.ignoreNotifyingUsers', false)) {
            return $content;
        }

        $pattern = '/@.*(#\d+|#p\d+)/';
        return trim(str_replace(["\r", "\n"], '', preg_replace($pattern, '', $content)));
    }

    public function handle(PostRestored $event) {
        if ($this->autoremove == AutoRemoveEnum::HIDDEN) {
            $minimumLength = (int)$this->settings->get('antoinefr-money.postminimumlength', 0);

            if (mb_strlen($this->ignoreNotifyingUsers($event->post->content)) >= $minimumLength) {
                $money = (float)$this->settings->get('antoinefr-money.moneyforpost', 0);

                $rewarded = $this->settings->get("mattoid-money-history-auto.privateChatsAreNotRewarded", 0);
                if ($rewarded && $event->post->discussion->is_private) {
                    $user = $event->actor;
                    $user->money -= $money;
                    $user->save();
                } else {
                    $this->events->dispatch(new MoneyHistoryEvent($event->post->user, $money, $this->source, $this->sourceDesc, $this->sourceKey));
                }
            }
        }
    }
}
