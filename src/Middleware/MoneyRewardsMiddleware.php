<?php

namespace Mattoid\MoneyHistoryAuto\Middleware;

use Flarum\Http\RequestUtil;
use Flarum\Locale\Translator;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Mattoid\MoneyHistory\Event\MoneyAllHistoryEvent;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;
use Mattoid\OperateLog\model\UserOperateLog;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MoneyRewardsMiddleware implements MiddlewareInterface
{
    private $events;
    private $source = "MONEYREWARDS";
    private $sourceDesc;

    public function __construct(Dispatcher $events, Translator $translator)
    {
        $this->events = $events;
        $this->sourceDesc = $translator->trans("mattoid-money-history-auto.forum.admin-rewards");
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $userId = Arr::get($actor, 'id');

        $response = $handler->handle($request);

        if ($response->getStatusCode() === 201 && preg_match('/\/posts\/\d*\/money-rewards/', $request->getUri())) {

            $amount = Arr::get($request->getParsedBody(), 'data.attributes.amount');
            $createMoney = Arr::get($request->getParsedBody(), 'data.attributes.createMoney');

            if (!$createMoney) {
                $this->events->dispatch(new MoneyHistoryEvent($actor, -$amount, $this->source, $this->sourceDesc));
            }

            $postId = Arr::get($request->getAttribute("routeParameters"), "id");
            $post = Post::query()->where('id', $postId)->first();
            $user = User::query()->where('id', $post->user_id)->first();
            $user->create_user_id = $userId;
            $this->events->dispatch(new MoneyHistoryEvent($user, $amount, $this->source, $this->sourceDesc));
        }

        return $response;
    }
}
